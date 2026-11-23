<?php

namespace App\Http\Controllers;

use App\Exports\PaymentRequestsExport;
use App\Models\Contract;
use App\Models\PaymentRequest;
use App\Models\PaymentType;
use App\Models\ProcurementType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->canViewPaymentRequests()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view payment requests.');
        }

        [$query, $search] = $this->filteredQuery($request);

        $paymentRequests = $query->paginate(5)->appends($request->query());

        return view('payment-requests.index', array_merge(
            $this->summaryStats(),
            [
                'paymentRequests' => $paymentRequests,
                'search' => $search,
                'materialGroups' => Contract::getMaterialGroups(),
                'procurementTypes' => ProcurementType::activeNames(),
                'paymentTypes' => PaymentType::activeNames(),
            ]
        ));
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();

        if (!$user->canViewPaymentRequests()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to export payment requests.');
        }

        [$query, $search] = $this->filteredQuery($request);

        (new PaymentRequestsExport($query->get(), $search))
            ->download('payment-requests-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();

        if (!$user->canViewPaymentRequests()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to export payment requests.');
        }

        [$query, $search] = $this->filteredQuery($request);
        $requests = $query->get();

        return Pdf::loadView('payment-requests.pdf', array_merge(
            $this->summaryStats(),
            ['requests' => $requests, 'search' => $search]
        ))->download('payment-requests-' . now()->format('Ymd-His') . '.pdf');
    }

    public function summary(string $scope)
    {
        $user = auth()->user();

        if (!$user->canViewPaymentRequests()) {
            return response()->json(['message' => 'You are not authorized to view payment requests.'], 403);
        }

        switch ($scope) {
            case 'pending':
                $query = PaymentRequest::with(['gsApprover', 'financeApprover'])
                    ->whereNull('finance_approved_at');
                $label = 'Pending Payment Requests';
                break;
            case 'complete':
                $query = PaymentRequest::with(['gsApprover', 'financeApprover'])
                    ->whereNotNull('finance_approved_at');
                $label = 'Completed Payment Requests';
                break;
            default:
                $query = PaymentRequest::with(['gsApprover', 'financeApprover']);
                $label = 'Total Payment Requests';
                break;
        }

        $total = (clone $query)->count();

        $items = $query->orderByDesc('id')
            ->limit(50)
            ->get()
            ->map(fn (PaymentRequest $paymentRequest) => [
                'reference' => $paymentRequest->getReference(),
                'date' => $paymentRequest->payment_request_date?->format('M d, Y'),
                'gs_status' => $paymentRequest->isGsApproved() ? 'Approved' : 'Pending',
                'gs_approver' => $paymentRequest->gsApprover->name ?? null,
                'gs_approved_at' => $paymentRequest->gs_approved_at?->format('M d, Y H:i'),
                'finance_status' => $paymentRequest->getFinanceStatusLabel(),
                'finance_approver' => $paymentRequest->financeApprover->name ?? null,
                'finance_approved_at' => $paymentRequest->finance_approved_at?->format('M d, Y H:i'),
                'status' => $paymentRequest->getStatusLabel(),
                'status_key' => $paymentRequest->getStatus(),
                'remarks' => $paymentRequest->remarks,
            ]);

        return response()->json([
            'scope' => $scope,
            'label' => $label,
            'total' => $total,
            'items' => $items,
            'limited' => $total > $items->count(),
        ]);
    }

    /**
     * @return array{0: \Illuminate\Database\Eloquent\Builder, 1: string}
     */
    private function filteredQuery(Request $request): array
    {
        $search = trim((string) $request->input('search', ''));
        $sort = (string) $request->input('sort', 'created_at');
        $direction = strtolower((string) $request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query = PaymentRequest::with(['gsApprover', 'financeApprover', 'creator']);

        if ($search !== '') {
            $like = '%' . $search . '%';

            $query->where(function ($query) use ($like, $search) {
                $query->where('payment_request_date', 'like', $like)
                    ->orWhere('document_no', 'like', $like)
                    ->orWhere('remarks', 'like', $like)
                    ->orWhereHas('gsApprover', function ($query) use ($like) {
                        $query->where('name', 'like', $like);
                    })
                    ->orWhereHas('financeApprover', function ($query) use ($like) {
                        $query->where('name', 'like', $like);
                    });

                if (preg_match('/^PR-?\s*0*(\d+)$/i', trim($search), $matches)) {
                    $query->orWhere('id', (int) $matches[1]);
                }
            });
        }

        if ($sort === 'status') {
            $query->orderByRaw(
                'CASE WHEN finance_approved_at IS NOT NULL THEN 2 ELSE 1 END ' . $direction
            )->orderBy('id', 'desc');
        } elseif ($sort === 'reference') {
            $query->orderBy('id', $direction);
        } elseif ($sort === 'payment_request_date') {
            $query->orderBy('payment_request_date', $direction)->orderBy('id', 'desc');
        } else {
            $query->latest('id');
        }

        return [$query, $search];
    }

    private function summaryStats(): array
    {
        return [
            'total' => PaymentRequest::count(),
            'pending' => PaymentRequest::whereNull('finance_approved_at')->count(),
            'complete' => PaymentRequest::whereNotNull('finance_approved_at')->count(),
        ];
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->canCreatePaymentRequests()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to create payment requests.');
        }

        $validated = $request->validate([
            'payment_request_date' => 'required|date',
            'document_no' => 'required|string|max:255',
            'payment_type' => ['required', 'string', Rule::in(PaymentType::activeNames())],
            'amount' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'material_group' => ['required', 'string', Rule::in(Contract::getMaterialGroups())],
            'procurement_type' => ['required', 'string', Rule::in(ProcurementType::activeNames())],
            'remarks' => 'nullable|string|max:1000',
        ], [
            'document_no.required' => 'Enter the document number.',
            'payment_type.required' => 'Select the payment type.',
            'amount.required' => 'Enter the amount price.',
            'quantity.required' => 'Enter the quantity.',
            'material_group.required' => 'Select the material group.',
            'procurement_type.required' => 'Select the procurement type.',
        ]);

        PaymentRequest::create([
            'payment_request_date' => $validated['payment_request_date'],
            'document_no' => trim($validated['document_no']),
            'payment_type' => trim($validated['payment_type']),
            'amount' => $validated['amount'],
            'quantity' => $validated['quantity'],
            'material_group' => $validated['material_group'],
            'procurement_type' => $validated['procurement_type'],
            'remarks' => filled($validated['remarks'] ?? null) ? trim($validated['remarks']) : null,
            'created_by' => $user->id,
        ]);

        return redirect()->route('payment-requests.index')
            ->with('success', 'Payment request created successfully.');
    }

    public function storeProcurementType(Request $request)
    {
        $user = auth()->user();

        if (!$user->canCreatePaymentRequests()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You are not authorized to register procurement types.'], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to register procurement types.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
        ], [
            'name.required' => 'Enter the procurement type name.',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $validated['name']));

        $type = ProcurementType::firstOrCreate(
            ['name' => $name],
            [
                'is_active' => true,
                'sort_order' => ((int) ProcurementType::max('sort_order')) + 1,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'name' => $type->name,
                'created' => $type->wasRecentlyCreated,
                'message' => $type->wasRecentlyCreated
                    ? 'Procurement type registered.'
                    : 'That procurement type is already registered.',
            ], $type->wasRecentlyCreated ? 201 : 200);
        }

        return back()->with('success', 'Procurement type registered.');
    }

    public function storePaymentType(Request $request)
    {
        $user = auth()->user();

        if (!$user->canCreatePaymentRequests()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You are not authorized to register payment types.'], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to register payment types.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
        ], [
            'name.required' => 'Enter the payment type name.',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $validated['name']));

        $type = PaymentType::firstOrCreate(
            ['name' => $name],
            [
                'is_active' => true,
                'sort_order' => ((int) PaymentType::max('sort_order')) + 1,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'name' => $type->name,
                'created' => $type->wasRecentlyCreated,
                'message' => $type->wasRecentlyCreated
                    ? 'Payment type registered.'
                    : 'That payment type is already registered.',
            ], $type->wasRecentlyCreated ? 201 : 200);
        }

        return back()->with('success', 'Payment type registered.');
    }

    public function approveGs(PaymentRequest $paymentRequest)
    {
        $user = auth()->user();

        if (!$user->canApprovePaymentRequestsGs()) {
            return redirect()->route('payment-requests.index')
                ->with('error', 'You are not authorized to give the GS approval.');
        }

        if ($paymentRequest->isGsApproved()) {
            return redirect()->route('payment-requests.index')
                ->with('error', 'Payment request ' . $paymentRequest->getReference() . ' already has GS approval.');
        }

        $paymentRequest->update([
            'gs_approved_by' => $user->id,
            'gs_approved_at' => now(),
        ]);

        return redirect()->route('payment-requests.index')
            ->with('success', 'GS approval recorded for ' . $paymentRequest->getReference() . '.');
    }

    public function completeFinance(PaymentRequest $paymentRequest)
    {
        $user = auth()->user();

        if (!$user->canCompletePaymentRequestsFinance()) {
            return redirect()->route('payment-requests.index')
                ->with('error', 'You are not authorized to complete the payment.');
        }

        if (!$paymentRequest->isGsApproved()) {
            return redirect()->route('payment-requests.index')
                ->with('error', 'GS approval is required before Finance can complete the payment.');
        }

        if ($paymentRequest->isFinanceCompleted()) {
            return redirect()->route('payment-requests.index')
                ->with('error', 'Payment request ' . $paymentRequest->getReference() . ' is already complete.');
        }

        $paymentRequest->update([
            'finance_approved_by' => $user->id,
            'finance_approved_at' => now(),
        ]);

        return redirect()->route('payment-requests.index')
            ->with('success', 'Payment completed for ' . $paymentRequest->getReference() . '.');
    }

    public function show(PaymentRequest $paymentRequest)
    {
        $user = auth()->user();

        if (!$user->canViewPaymentRequests()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view payment requests.');
        }

        $paymentRequest->load(['gsApprover', 'financeApprover', 'creator']);

        return view('payment-requests.show', compact('paymentRequest'));
    }
}
