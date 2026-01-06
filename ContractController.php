<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContractController extends Controller
{
    /**
     * Display a listing of contracts.
     */
    public function index(Request $request)
    {
        $query = Contract::with('histories');

        // Filter by alert status
        if ($request->has('alert_type')) {
            if ($request->alert_type === 'contract_end') {
                $query->whereNotNull('end_date');
            } elseif ($request->alert_type === 'performance_guarantee') {
                $query->whereNotNull('performance_guarantee_end_date');
            }
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $contracts = $query->orderBy('created_at', 'desc')->paginate(15);

        // Check for alerts
        foreach ($contracts as $contract) {
            $contract->contract_alert = $contract->shouldAlertContractEndDate();
            $contract->pg_alert = $contract->shouldAlertPerformanceGuarantee();
        }

        return view('contracts.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new contract.
     */
    public function create()
    {
        return view('contracts.create');
    }

    /**
     * Store a newly created contract in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'alert_days_before' => 'required|integer|min:1|max:365',
            'performance_guarantee_amount' => 'nullable|numeric|min:0',
            'performance_guarantee_end_date' => 'nullable|date|after:start_date',
            'performance_guarantee_alert_days' => 'nullable|integer|min:1|max:365',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $contract = Contract::create($request->all());

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract created successfully.');
    }

    /**
     * Display the specified contract.
     */
    public function show(Contract $contract)
    {
        $contract->load(['histories' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        $contract->contract_alert = $contract->shouldAlertContractEndDate();
        $contract->pg_alert = $contract->shouldAlertPerformanceGuarantee();
        $contract->days_until_end = $contract->getDaysUntilContractEnd();
        $contract->days_until_pg_end = $contract->getDaysUntilPerformanceGuaranteeEnd();

        return view('contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing the specified contract.
     */
    public function edit(Contract $contract)
    {
        return view('contracts.edit', compact('contract'));
    }

    /**
     * Update the specified contract in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'alert_days_before' => 'required|integer|min:1|max:365',
            'performance_guarantee_amount' => 'nullable|numeric|min:0',
            'performance_guarantee_end_date' => 'nullable|date|after:start_date',
            'performance_guarantee_alert_days' => 'nullable|integer|min:1|max:365',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $contract->update($request->all());

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract updated successfully.');
    }

    /**
     * Remove the specified contract from storage.
     */
    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('success', 'Contract deleted successfully.');
    }

    /**
     * Show form to extend contract end date
     */
    public function extendContractEndDate(Contract $contract)
    {
        return view('contracts.extend-contract-end', compact('contract'));
    }

    /**
     * Update contract end date with reason tracking
     */
    public function updateContractEndDate(Request $request, Contract $contract)
    {
        $validator = Validator::make($request->all(), [
            'new_end_date' => 'required|date|after:' . $contract->end_date,
            'reason' => 'required|string|max:1000',
            'edited_by' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $contract->updateContractEndDate(
            $request->new_end_date,
            $request->reason,
            $request->edited_by ?? auth()->user()->name ?? 'System'
        );

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract end date extended successfully. Extension count: ' . $contract->contract_end_date_edit_count);
    }

    /**
     * Show form to extend performance guarantee
     */
    public function extendPerformanceGuarantee(Contract $contract)
    {
        return view('contracts.extend-performance-guarantee', compact('contract'));
    }

    /**
     * Update performance guarantee with reason tracking
     */
    public function updatePerformanceGuarantee(Request $request, Contract $contract)
    {
        $validator = Validator::make($request->all(), [
            'new_amount' => 'required|numeric|min:0',
            'new_end_date' => 'required|date',
            'reason' => 'required|string|max:1000',
            'edited_by' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $contract->updatePerformanceGuarantee(
            $request->new_amount,
            $request->new_end_date,
            $request->reason,
            $request->edited_by ?? auth()->user()->name ?? 'System'
        );

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Performance guarantee updated successfully. Extension count: ' . $contract->performance_guarantee_edit_count);
    }

    /**
     * Get contracts with alerts (for dashboard/notifications)
     */
    public function alerts()
    {
        $contracts = Contract::all();
        $alertContracts = [];

        foreach ($contracts as $contract) {
            $alerts = [];

            if ($contract->shouldAlertContractEndDate()) {
                $alerts[] = [
                    'type' => 'contract_end',
                    'message' => 'Contract ending in ' . $contract->getDaysUntilContractEnd() . ' days',
                    'days_remaining' => $contract->getDaysUntilContractEnd(),
                ];
            }

            if ($contract->shouldAlertPerformanceGuarantee()) {
                $alerts[] = [
                    'type' => 'performance_guarantee',
                    'message' => 'Performance guarantee ending in ' . $contract->getDaysUntilPerformanceGuaranteeEnd() . ' days',
                    'days_remaining' => $contract->getDaysUntilPerformanceGuaranteeEnd(),
                ];
            }

            if (!empty($alerts)) {
                $alertContracts[] = [
                    'contract' => $contract,
                    'alerts' => $alerts,
                ];
            }
        }

        return view('contracts.alerts', compact('alertContracts'));
    }
}
