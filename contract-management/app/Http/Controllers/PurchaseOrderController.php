<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseOrdersExport;
use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->canViewPurchaseOrders()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view purchase orders.');
        }

        [$query, $search] = $this->filteredQuery($request);

        $perPage = (int) $request->get('per_page', 5);
        if (!in_array($perPage, [5, 10, 25, 50], true)) {
            $perPage = 5;
        }

        $purchaseOrders = $query->paginate($perPage)->appends($request->query());

        $statGroups = [
            'total' => $this->scopeQuery('total')->orderByDesc('approval_date')->orderByDesc('id')->get(),
            'pending_first' => $this->scopeQuery('pending_first')->orderByDesc('approval_date')->orderByDesc('id')->get(),
            'pending_second' => $this->scopeQuery('pending_second')->orderByDesc('approval_date')->orderByDesc('id')->get(),
            'approved' => $this->scopeQuery('approved')->orderByDesc('approval_date')->orderByDesc('id')->get(),
        ];

        return view('purchase-orders.index', [
            'purchaseOrders' => $purchaseOrders,
            'search' => $search,
            'statGroups' => $statGroups,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();

        if (!$user->canViewPurchaseOrders()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to export purchase orders.');
        }

        [$query, $search] = $this->filteredQuery($request);

        (new PurchaseOrdersExport($query->get(), $search))
            ->download('purchase-orders-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();

        if (!$user->canViewPurchaseOrders()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to export purchase orders.');
        }

        [$query, $search] = $this->filteredQuery($request);
        $orders = $query->get();

        return Pdf::loadView('purchase-orders.pdf', array_merge(
            $this->summaryStats(),
            ['orders' => $orders, 'search' => $search]
        ))->download('purchase-orders-' . now()->format('Ymd-His') . '.pdf');
    }

    private function scopeQuery(string $scope)
    {
        $threshold = PurchaseOrder::SECOND_APPROVAL_THRESHOLD;
        $query = PurchaseOrder::with(['gsManagerApprover', 'ddgApprover']);

        switch ($scope) {
            case 'pending_first':
                return $query->whereNull('gs_manager_approved_at');
            case 'pending_second':
                // Includes orders still awaiting the 1st approval, which show as "Locked".
                return $query->whereNull('ddg_approved_at')
                    ->where('amount', '>', $threshold);
            case 'approved':
                return $this->approvedQuery()->with(['gsManagerApprover', 'ddgApprover']);
            default:
                return $query;
        }
    }

    private function approvedQuery()
    {
        $threshold = PurchaseOrder::SECOND_APPROVAL_THRESHOLD;

        return PurchaseOrder::where(function ($query) use ($threshold) {
            $query->where(function ($query) use ($threshold) {
                $query->where('amount', '>', $threshold)
                    ->whereNotNull('ddg_approved_at');
            })->orWhere(function ($query) use ($threshold) {
                $query->where(function ($query) use ($threshold) {
                    $query->whereNull('amount')->orWhere('amount', '<=', $threshold);
                })->whereNotNull('gs_manager_approved_at');
            });
        });
    }

    /**
     * @return array{0: \Illuminate\Database\Eloquent\Builder, 1: string}
     */
    private function filteredQuery(Request $request): array
    {
        $threshold = PurchaseOrder::SECOND_APPROVAL_THRESHOLD;

        $search = trim((string) $request->input('search', ''));
        $sort = (string) $request->input('sort', 'created_at');
        $direction = strtolower((string) $request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query = PurchaseOrder::with(['gsManagerApprover', 'ddgApprover', 'creator']);

        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($query) use ($like) {
                $query->where('po_number', 'like', $like)
                    ->orWhereHas('gsManagerApprover', function ($query) use ($like) {
                        $query->where('name', 'like', $like);
                    })
                    ->orWhereHas('ddgApprover', function ($query) use ($like) {
                        $query->where('name', 'like', $like);
                    });
            });
        }

        if ($sort === 'status') {
            $query->orderByRaw(
                'CASE
                    WHEN amount > ? AND ddg_approved_at IS NOT NULL THEN 3
                    WHEN (amount IS NULL OR amount <= ?) AND gs_manager_approved_at IS NOT NULL THEN 3
                    WHEN amount > ? AND gs_manager_approved_at IS NOT NULL THEN 2
                    ELSE 1
                END ' . $direction,
                [$threshold, $threshold, $threshold]
            )->orderBy('id', 'desc');
        } elseif (in_array($sort, ['po_number', 'amount', 'approval_date'], true)) {
            $query->orderBy($sort, $direction)->orderBy('id', 'desc');
        } else {
            $query->latest('id');
        }

        return [$query, $search];
    }

    private function summaryStats(): array
    {
        return [
            'total' => $this->scopeQuery('total')->count(),
            'pendingFirst' => $this->scopeQuery('pending_first')->count(),
            'pendingSecond' => $this->scopeQuery('pending_second')->count(),
            'approved' => $this->scopeQuery('approved')->count(),
        ];
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->canCreatePurchaseOrders()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to create purchase orders.');
        }

        $validated = $request->validate([
            'po_number' => 'required|string|max:255|unique:purchase_orders,po_number',
            'amount' => 'nullable|numeric|min:0',
            'approval_date' => 'required|date',
            'remarks' => 'nullable|string|max:1000',
        ], [
            'po_number.unique' => 'That PO number is already registered.',
        ]);

        $purchaseOrder = PurchaseOrder::create([
            'po_number' => trim($validated['po_number']),
            'amount' => $validated['amount'] ?? null,
            'approval_date' => $validated['approval_date'],
            'remarks' => filled($validated['remarks'] ?? null) ? trim($validated['remarks']) : null,
            'created_by' => $user->id,
        ]);

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order ' . $purchaseOrder->po_number . ' created successfully.');
    }

    public function approveGs(PurchaseOrder $purchaseOrder)
    {
        $user = auth()->user();

        if (!$user->canApprovePurchaseOrdersGs()) {
            return redirect()->route('purchase-orders.index')
                ->with('error', 'You are not authorized to give the first approval.');
        }

        if ($purchaseOrder->isGsApproved()) {
            return redirect()->route('purchase-orders.index')
                ->with('error', 'Purchase order ' . $purchaseOrder->po_number . ' already has its first approval.');
        }

        $purchaseOrder->update([
            'gs_manager_approved_by' => $user->id,
            'gs_manager_approved_at' => now(),
        ]);

        $message = $purchaseOrder->requiresDdgApproval()
            ? 'First approval recorded for ' . $purchaseOrder->po_number . '. DDG second approval is now required.'
            : 'Purchase order ' . $purchaseOrder->po_number . ' approved.';

        return redirect()->route('purchase-orders.index')->with('success', $message);
    }

    public function approveDdg(PurchaseOrder $purchaseOrder)
    {
        $user = auth()->user();

        if (!$user->canApprovePurchaseOrdersDdg()) {
            return redirect()->route('purchase-orders.index')
                ->with('error', 'You are not authorized to give the second approval.');
        }

        if (!$purchaseOrder->requiresDdgApproval()) {
            return redirect()->route('purchase-orders.index')
                ->with('error', 'Purchase order ' . $purchaseOrder->po_number . ' does not require a second approval (amount is at or below ETB 400,000).');
        }

        if (!$purchaseOrder->isGsApproved()) {
            return redirect()->route('purchase-orders.index')
                ->with('error', 'GS Manager approval is required before the DDG approval.');
        }

        if ($purchaseOrder->isDdgApproved()) {
            return redirect()->route('purchase-orders.index')
                ->with('error', 'Purchase order ' . $purchaseOrder->po_number . ' is already fully approved.');
        }

        $purchaseOrder->update([
            'ddg_approved_by' => $user->id,
            'ddg_approved_at' => now(),
        ]);

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Second approval recorded for ' . $purchaseOrder->po_number . '.');
    }
}
