<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractClosure;
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

        $visibleContractIds = Contract::forUser($user)->select('id');

        $query = ContractClosure::with(['contract', 'creator', 'completer'])
            ->whereIn('contract_id', $visibleContractIds);

        if ($request->filled('status') && array_key_exists($request->status, ContractClosure::getStatuses())) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('closing_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('closing_date', '<=', $request->to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('contract', function ($contract) use ($search) {
                $contract->where('name', 'like', '%' . $search . '%')
                    ->orWhere('supplier_name', 'like', '%' . $search . '%');
            });
        }

        $closures = $query->orderByDesc('closing_date')->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        $base = ContractClosure::whereIn('contract_id', Contract::forUser($user)->select('id'));

        $total = (clone $base)->count();
        $pending = (clone $base)->where('status', ContractClosure::STATUS_PENDING)->count();
        $complete = (clone $base)->where('status', ContractClosure::STATUS_COMPLETE)->count();
        $today = (clone $base)->whereDate('closing_date', now()->toDateString())->count();

        $contracts = Contract::forUser($user)
            ->whereDoesntHave('closure')
            ->orderBy('name')
            ->get();

        $statuses = ContractClosure::getStatuses();

        return view('contract-closures.index', compact(
            'closures', 'contracts', 'statuses', 'total', 'pending', 'complete', 'today'
        ));
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
