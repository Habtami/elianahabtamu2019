<?php

namespace App\Http\Controllers;

use App\Exports\ContractClosuresExport;
use App\Helpers\ReportMeta;
use App\Models\Contract;
use App\Models\ContractClosure;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ContractClosureController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->canViewContractClosures()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view contract closures.');
        }

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50], true)) {
            $perPage = 10;
        }

        [$query, $search] = $this->filteredQuery($request);

        $closures = $query->paginate($perPage)->appends($request->query());

        $contracts = Contract::forUser($user)
            ->whereDoesntHave('closure')
            ->orderBy('name')
            ->get();

        $reportMeta = ReportMeta::build(
            $closures->getCollection(),
            $this->letterheadFilters($request),
            $request->input('from'),
            $request->input('to'),
            'closing_date',
            $closures->total()
        );

        return view('contract-closures.index', array_merge(
            $this->summaryStats(),
            [
                'closures' => $closures,
                'search' => $search,
                'statGroups' => $this->statGroups(),
                'contracts' => $contracts,
                'statuses' => ContractClosure::getStatuses(),
                'reportMeta' => $reportMeta,
            ]
        ));
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();

        if (!$user->canViewContractClosures()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to export contract closures.');
        }

        [$query, $search] = $this->filteredQuery($request);
        $closures = $query->get();
        $reportMeta = ReportMeta::build(
            $closures,
            $this->letterheadFilters($request),
            $request->input('from'),
            $request->input('to'),
            'closing_date'
        );

        (new ContractClosuresExport($closures, $search, $reportMeta))
            ->download('contract-closures-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();

        if (!$user->canViewContractClosures()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to export contract closures.');
        }

        [$query, $search] = $this->filteredQuery($request);
        $closures = $query->get();
        $reportMeta = ReportMeta::build(
            $closures,
            $this->letterheadFilters($request),
            $request->input('from'),
            $request->input('to'),
            'closing_date'
        );

        return Pdf::loadView('contract-closures.pdf', array_merge(
            $this->summaryStats(),
            ['closures' => $closures, 'search' => $search, 'reportMeta' => $reportMeta]
        ))->download('contract-closures-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * @return array<int, string|null>
     */
    private function letterheadFilters(Request $request): array
    {
        $statusLabels = ContractClosure::getStatuses();

        return [
            $request->filled('search') ? 'Search: '.$request->input('search') : null,
            $request->filled('status') && array_key_exists($request->status, $statusLabels) ? 'Status: '.$statusLabels[$request->status] : null,
        ];
    }

    /**
     * @return array{0: \Illuminate\Database\Eloquent\Builder, 1: string}
     */
    private function filteredQuery(Request $request): array
    {
        $search = trim((string) $request->input('search', ''));

        $query = ContractClosure::with(['contract', 'creator', 'completer'])
            ->whereIn('contract_id', Contract::forUser(auth()->user())->select('id'));

        if ($request->filled('status') && array_key_exists($request->status, ContractClosure::getStatuses())) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('closing_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('closing_date', '<=', $request->to);
        }

        if ($search !== '') {
            $like = '%' . $search . '%';

            $query->whereHas('contract', function ($contract) use ($like) {
                $contract->where('name', 'like', $like)
                    ->orWhere('supplier_name', 'like', $like);
            });
        }

        $query->orderByDesc('closing_date')->orderByDesc('id');

        return [$query, $search];
    }

    private function summaryStats(): array
    {
        $base = ContractClosure::whereIn('contract_id', Contract::forUser(auth()->user())->select('id'));

        return [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', ContractClosure::STATUS_PENDING)->count(),
            'complete' => (clone $base)->where('status', ContractClosure::STATUS_COMPLETE)->count(),
            'today' => (clone $base)->whereDate('closing_date', now()->toDateString())->count(),
        ];
    }

    private function scopeQuery(string $scope)
    {
        $query = ContractClosure::with(['contract', 'completer'])
            ->whereIn('contract_id', Contract::forUser(auth()->user())->select('id'))
            ->orderByDesc('closing_date')
            ->orderByDesc('id');

        if ($scope === 'pending') {
            $query->where('status', ContractClosure::STATUS_PENDING);
        } elseif ($scope === 'complete') {
            $query->where('status', ContractClosure::STATUS_COMPLETE);
        } elseif ($scope === 'today') {
            $query->whereDate('closing_date', now()->toDateString());
        }

        return $query;
    }

    /**
     * @return array<string, \Illuminate\Database\Eloquent\Collection>
     */
    private function statGroups(): array
    {
        return [
            'total' => $this->scopeQuery('total')->get(),
            'pending' => $this->scopeQuery('pending')->get(),
            'complete' => $this->scopeQuery('complete')->get(),
            'today' => $this->scopeQuery('today')->get(),
        ];
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->canCreateContractClosures()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to request contract closings.');
        }

        $validated = $request->validate([
            'contract_id' => 'required|integer|exists:contracts,id',
            'closing_date' => 'required|date',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $contract = Contract::forUser($user)->find($validated['contract_id']);

        if (!$contract) {
            return back()->withErrors(['contract_id' => 'That contract is not available to you.'])->withInput();
        }

        if ($contract->closure()->exists()) {
            return back()->withErrors(['contract_id' => 'That contract already has a closing record.'])->withInput();
        }

        ContractClosure::create([
            'contract_id' => $contract->id,
            'closing_date' => $validated['closing_date'],
            'status' => ContractClosure::STATUS_PENDING,
            'remarks' => $validated['remarks'] ?? null,
            'created_by' => $user->id,
        ]);

        return redirect()->route('contract-closures.index')
            ->with('success', 'Closing request created for ' . $contract->name . '.');
    }

    public function complete(ContractClosure $contractClosure)
    {
        $user = auth()->user();

        if (!$user->canCompleteContractClosures()) {
            return redirect()->route('contract-closures.index')
                ->with('error', 'You are not authorized to complete contract closings.');
        }

        if ($contractClosure->isComplete()) {
            return redirect()->route('contract-closures.index')
                ->with('error', 'That contract closing is already complete.');
        }

        $contractClosure->update([
            'status' => ContractClosure::STATUS_COMPLETE,
            'completed_by' => $user->id,
            'completed_at' => now(),
        ]);

        return redirect()->route('contract-closures.index')
            ->with('success', 'Contract closing completed for ' . ($contractClosure->contract->name ?? 'the contract') . '.');
    }

    public function show(ContractClosure $contractClosure)
    {
        $user = auth()->user();

        if (!$user->canViewContractClosures()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view contract closures.');
        }

        $contractVisible = Contract::forUser($user)->whereKey($contractClosure->contract_id)->exists();

        if (!$contractVisible) {
            return redirect()->route('contract-closures.index')
                ->with('error', 'That contract is not available to you.');
        }

        $contractClosure->load(['contract', 'completer', 'creator']);

        return view('contract-closures.show', compact('contractClosure'));
    }
}
