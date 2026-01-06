<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Contract::forUser($user)->with('creator');
        
        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by department
        if ($request->has('department') && $user->canViewAllContracts()) {
            $query->where('department', $request->department);
        }
        
        $perPage = request('per_page', 10);
        $contracts = $query->latest()->paginate($perPage);
        
        $departments = User::getDepartments();
        $statuses = Contract::getStatuses();
        
        return view('contracts.index', compact('contracts', 'departments', 'statuses'));
    }
    
    public function create()
    {
        $user = auth()->user();

        if (!$user->canCreateContracts()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to create contracts.');
        }

        $departments = [];

        // Set department based on user role
        if ($user->role === User::ROLE_GENERAL_SERVICE) {
            $departments = [User::DEPARTMENT_GENERAL_SERVICE => 'General Service'];
        } elseif ($user->role === User::ROLE_CMD) {
            $departments = [User::DEPARTMENT_CMD => 'CMD'];
        } elseif ($user->role === User::ROLE_FINANCE) {
            $departments = [User::DEPARTMENT_FINANCE => 'Finance'];
        } elseif (in_array($user->role, [User::ROLE_ADMIN, User::ROLE_GS_MANAGER, User::ROLE_CMD_MANAGER])) {
            // Managers and admins can create contracts for all departments
            $departments = User::getDepartments();
        }

        return view('contracts.create', compact('departments'));
    }
    
    public function store(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->canCreateContracts()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to create contracts.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'required|string|in:cmd,general_service,finance',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'alert_days' => 'required|integer|min:1|max:365',
            'performance_guarantee_amount' => 'nullable|numeric|min:0',
            'performance_guarantee_end_date' => 'nullable|date|after:start_date',
            'performance_guarantee_alert_days' => 'nullable|integer|min:1|max:365',
        ]);
        
        // Check if user can create contract for selected department
        $userDepartment = $user->getUserDepartment();
        if ($userDepartment && $request->department !== $userDepartment) {
            return back()->with('error', 'You can only create contracts for your department.');
        }
        
        $contract = Contract::create([
            'name' => $request->name,
            'description' => $request->description,
            'department' => $request->department,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'alert_days' => $request->alert_days,
            'performance_guarantee_amount' => $request->performance_guarantee_amount,
            'performance_guarantee_end_date' => $request->performance_guarantee_end_date,
            'performance_guarantee_alert_days' => $request->performance_guarantee_alert_days,
            'created_by' => $user->id,
            'status' => Contract::STATUS_ACTIVE,
            'extended_count' => 0
        ]);
        
        $contract->updateStatus();
        
        return redirect()->route('contracts.index')
            ->with('success', 'Contract created successfully.');
    }
    
    public function show(Contract $contract)
    {
        $user = auth()->user();

        // Check if user can view this contract
        if (!in_array($contract->department, $user->getAllowedDepartments())) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view this contract.');
        }

        $contract->load('histories');

        $activityLogs = ActivityLog::where('contract_id', $contract->id)
            ->latest()
            ->get();

        return view('contracts.show', compact('contract', 'activityLogs'));
    }
    
    public function edit(Contract $contract)
    {
        $user = auth()->user();

        // Check if user can edit this contract
        if (!$contract->canUserEdit($user)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'You are not authorized to edit this contract.');
        }

        $departments = [];

        // Set department based on user role
        if ($user->role === User::ROLE_GENERAL_SERVICE) {
            $departments = [User::DEPARTMENT_GENERAL_SERVICE => 'General Service'];
        } elseif ($user->role === User::ROLE_CMD) {
            $departments = [User::DEPARTMENT_CMD => 'CMD'];
        } elseif ($user->role === User::ROLE_FINANCE) {
            $departments = [User::DEPARTMENT_FINANCE => 'Finance'];
        }

        return view('contracts.edit', compact('contract', 'departments'));
    }
    
    public function update(Request $request, Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user can edit this contract
        if (!$contract->canUserEdit($user)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'You are not authorized to edit this contract.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'required|string|in:cmd,general_service,finance',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'alert_days' => 'required|integer|min:1|max:365',
            'performance_guarantee_amount' => 'nullable|numeric|min:0',
            'performance_guarantee_end_date' => 'nullable|date|after:start_date',
            'performance_guarantee_alert_days' => 'nullable|integer|min:1|max:365',
        ]);
        
        // Check if user can update contract for selected department
        if ($request->department !== $contract->department) {
            return back()->with('error', 'You cannot change the department of this contract.');
        }
        
        $contract->update([
            'name' => $request->name,
            'description' => $request->description,
            'department' => $request->department,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'alert_days' => $request->alert_days,
            'performance_guarantee_amount' => $request->performance_guarantee_amount,
            'performance_guarantee_end_date' => $request->performance_guarantee_end_date,
            'performance_guarantee_alert_days' => $request->performance_guarantee_alert_days,
        ]);
        
        $contract->updateStatus();
        
        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract updated successfully.');
    }
    
    public function destroy(Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user can delete this contract
        if (!$contract->canUserDelete($user)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'You are not authorized to delete this contract.');
        }
        
        $contract->delete();
        
        return redirect()->route('contracts.index')
            ->with('success', 'Contract deleted successfully.');
    }
    
    public function alerts()
    {
        $user = auth()->user();
        $alertContracts = [];
        
        $contracts = Contract::forUser($user)->get();
        
        foreach ($contracts as $contract) {
            $alerts = [];
            
            // Check contract end alert
            if ($contract->isExpiringSoon()) {
                $days = $contract->getDaysUntilContractEnd();
                $alerts[] = [
                    'type' => 'contract_end',
                    'message' => "Contract ends in {$days} days",
                    'severity' => $days <= 7 ? 'high' : 'medium'
                ];
            }
            
            // Check performance guarantee alert
            if ($contract->performance_guarantee_end_date && $contract->isPerformanceGuaranteeExpiringSoon()) {
                $days = $contract->getDaysUntilPerformanceGuaranteeEnd();
                $alerts[] = [
                    'type' => 'performance_guarantee',
                    'message' => "Performance guarantee ends in {$days} days",
                    'severity' => $days <= 7 ? 'high' : 'medium'
                ];
            }
            
            if (!empty($alerts)) {
                $alertContracts[] = [
                    'contract' => $contract,
                    'alerts' => $alerts
                ];
            }
        }
        
        // Sort by severity and days remaining
        usort($alertContracts, function ($a, $b) {
            $aSeverity = $this->getSeverityScore($a['alerts']);
            $bSeverity = $this->getSeverityScore($b['alerts']);
            return $bSeverity <=> $aSeverity;
        });
        
        return view('contracts.alerts', compact('alertContracts'));
    }
    
    private function getSeverityScore($alerts)
    {
        $score = 0;
        foreach ($alerts as $alert) {
            if ($alert['severity'] === 'high') {
                $score += 3;
            } elseif ($alert['severity'] === 'medium') {
                $score += 1;
            }
        }
        return $score;
    }
    
    public function extendContractEnd(Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user can extend this contract
        if (!$contract->canUserEdit($user)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'You are not authorized to extend this contract.');
        }
        
        return view('contracts.extend-contract-end', compact('contract'));
    }
    
    public function updateContractEnd(Request $request, Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user can extend this contract
        if (!$contract->canUserEdit($user)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'You are not authorized to extend this contract.');
        }
        
        $request->validate([
            'new_end_date' => 'required|date|after:' . $contract->end_date,
            'reason' => 'required|string|max:500',
        ]);
        
        $editedBy = auth()->user()->name . ' (' . auth()->user()->getDepartmentDisplayName() . ')';
        $contract->extendContractEnd($request->new_end_date, $request->reason, $editedBy);
        
        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract end date extended successfully.');
    }
    
    public function extendPerformanceGuarantee(Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user can extend performance guarantee
        if (!$contract->canUserEdit($user)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'You are not authorized to extend performance guarantee.');
        }
        
        if (!$contract->performance_guarantee_end_date) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'This contract does not have a performance guarantee.');
        }
        
        return view('contracts.extend-performance-guarantee', compact('contract'));
    }
    
    public function updatePerformanceGuarantee(Request $request, Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user can extend performance guarantee
        if (!$contract->canUserEdit($user)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'You are not authorized to extend performance guarantee.');
        }
        
        if (!$contract->performance_guarantee_end_date) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'This contract does not have a performance guarantee.');
        }
        
        $request->validate([
            'new_amount' => 'required|numeric|min:0',
            'new_end_date' => 'required|date|after:' . $contract->performance_guarantee_end_date,
            'reason' => 'required|string|max:500',
        ]);

        $editedBy = auth()->user()->name . ' (' . auth()->user()->getDepartmentDisplayName() . ')';
        $contract->extendPerformanceGuarantee($request->new_amount, $request->new_end_date, $request->reason, $editedBy);
        
        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Performance guarantee end date extended successfully.');
    }
    
    public function reports(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role, [User::ROLE_ADMIN, User::ROLE_FINANCE, User::ROLE_GS_MANAGER, User::ROLE_CMD_MANAGER])) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view reports.');
        }

        // Get date range from request or default to last 12 months
        $dateRange = $request->get('date_range', '12_months');
        $startDate = $this->getStartDateForRange($dateRange);
        $endDate = now();

        // Get statistics
        $totalContracts = Contract::forUser($user)->count();
        $activeContracts = Contract::forUser($user)->active()->count();
        $expiringContracts = Contract::forUser($user)->expiring()->count();
        $expiredContracts = Contract::forUser($user)->expired()->count();

        // Calculate percentages
        $activePercentage = $totalContracts > 0 ? round(($activeContracts / $totalContracts) * 100, 1) : 0;
        $expiringPercentage = $totalContracts > 0 ? round(($expiringContracts / $totalContracts) * 100, 1) : 0;
        $expiredPercentage = $totalContracts > 0 ? round(($expiredContracts / $totalContracts) * 100, 1) : 0;

        // Contracts by department
        $contractsByDepartment = Contract::forUser($user)
            ->select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
            ->get();

        // Contracts by month (dynamic based on date range)
        $contractsByMonth = Contract::forUser($user)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->get();

        // Contracts by status
        $contractsByStatus = Contract::forUser($user)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Recent activity (latest contract updates)
        $recentActivity = ActivityLog::with('contract')
            ->whereHas('contract', function ($query) use ($user) {
                $query->forUser($user);
            })
            ->latest()
            ->take(10)
            ->get();

        // Last updated timestamp
        $lastUpdated = now();

        return view('contracts.reports', compact(
            'user',
            'totalContracts',
            'activeContracts',
            'expiringContracts',
            'expiredContracts',
            'activePercentage',
            'expiringPercentage',
            'expiredPercentage',
            'contractsByDepartment',
            'contractsByMonth',
            'contractsByStatus',
            'recentActivity',
            'dateRange',
            'startDate',
            'endDate',
            'lastUpdated'
        ));
    }

    private function getStartDateForRange($range)
    {
        return match($range) {
            '6_months' => now()->subMonths(6),
            '12_months' => now()->subMonths(12),
            'year_to_date' => now()->startOfYear(),
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            default => now()->subMonths(12)
        };
    }

    public function exportReports()
    {
        $user = auth()->user();

        if (!in_array($user->role, [User::ROLE_ADMIN, User::ROLE_FINANCE, User::ROLE_GS_MANAGER, User::ROLE_CMD_MANAGER])) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to export reports.');
        }

        // Get the same data as the reports method
        $totalContracts = Contract::forUser($user)->count();
        $activeContracts = Contract::forUser($user)->active()->count();
        $expiringContracts = Contract::forUser($user)->expiring()->count();
        $expiredContracts = Contract::forUser($user)->expired()->count();

        $contractsByDepartment = Contract::forUser($user)
            ->select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
            ->get();

        $contractsByStatus = Contract::forUser($user)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $contracts = Contract::forUser($user)->with('creator')->get();

        // Create CSV content
        $csvData = [];

        // Summary section
        $csvData[] = ['Contract Management Report'];
        $csvData[] = ['Generated on', now()->format('Y-m-d H:i:s')];
        $csvData[] = ['Generated by', $user->name . ' (' . $user->getDepartmentDisplayName() . ')'];
        $csvData[] = [''];
        $csvData[] = ['Summary Statistics'];
        $csvData[] = ['Total Contracts', $totalContracts];
        $csvData[] = ['Active Contracts', $activeContracts];
        $csvData[] = ['Expiring Soon', $expiringContracts];
        $csvData[] = ['Expired Contracts', $expiredContracts];
        $csvData[] = [''];

        // Contracts by Department
        $csvData[] = ['Contracts by Department'];
        $csvData[] = ['Department', 'Count'];
        foreach ($contractsByDepartment as $dept) {
            $csvData[] = [ucfirst(str_replace('_', ' ', $dept->department)), $dept->count];
        }
        $csvData[] = [''];

        // Contracts by Status
        $csvData[] = ['Contracts by Status'];
        $csvData[] = ['Status', 'Count'];
        foreach ($contractsByStatus as $status) {
            $csvData[] = [ucfirst($status->status), $status->count];
        }
        $csvData[] = [''];

        // Contract Details
        $csvData[] = ['Contract Details'];
        $csvData[] = ['Name', 'Department', 'Start Date', 'End Date', 'Status', 'Created By', 'Performance Guarantee Amount', 'Performance Guarantee End Date'];
        foreach ($contracts as $contract) {
            $csvData[] = [
                $contract->name,
                ucfirst(str_replace('_', ' ', $contract->department)),
                $contract->start_date?->format('Y-m-d'),
                $contract->end_date?->format('Y-m-d'),
                ucfirst($contract->status),
                $contract->creator->name ?? 'N/A',
                $contract->performance_guarantee_amount ? number_format($contract->performance_guarantee_amount, 2) : 'N/A',
                $contract->performance_guarantee_end_date?->format('Y-m-d') ?? 'N/A'
            ];
        }

        // Generate CSV response
        $filename = 'contract-report-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export(Request $request)
    {
        $format = $request->input('format', 'csv');
        $scope = $request->input('scope', 'all');

        // Generate report based on parameters
        $data = $this->generateExportData($request);

        if ($format === 'pdf') {
            return $this->exportAsPdf($data);
        } elseif ($format === 'excel') {
            return $this->exportAsExcel($data);
        } elseif ($format === 'csv') {
            return $this->exportAsCsv($data);
        }

        // Default to CSV
        return $this->exportAsCsv($data);
    }

    private function generateExportData(Request $request)
    {
        $user = auth()->user();
        $scope = $request->input('scope', 'all');

        // Base query
        $query = Contract::forUser($user);

        // Apply scope filters
        if ($scope === 'active') {
            $query->active();
        } elseif ($scope === 'expiring') {
            $query->expiring();
        } elseif ($scope === 'expired') {
            $query->expired();
        }

        $contracts = $query->with('creator')->get();

        // Get summary data
        $summary = [
            'total_contracts' => $contracts->count(),
            'active_contracts' => $contracts->where('status', 'active')->count(),
            'expiring_contracts' => $contracts->where('status', 'expiring')->count(),
            'expired_contracts' => $contracts->where('status', 'expired')->count(),
            'generated_by' => $user->name . ' (' . $user->getDepartmentDisplayName() . ')',
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];

        return [
            'summary' => $summary,
            'contracts' => $contracts,
        ];
    }

    private function exportAsCsv($data)
    {
        $filename = 'contracts-export-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Summary section
            fputcsv($file, ['Contract Export Report']);
            fputcsv($file, ['Generated on', $data['summary']['generated_at']]);
            fputcsv($file, ['Generated by', $data['summary']['generated_by']]);
            fputcsv($file, ['']);
            fputcsv($file, ['Summary Statistics']);
            fputcsv($file, ['Total Contracts', $data['summary']['total_contracts']]);
            fputcsv($file, ['Active Contracts', $data['summary']['active_contracts']]);
            fputcsv($file, ['Expiring Soon', $data['summary']['expiring_contracts']]);
            fputcsv($file, ['Expired Contracts', $data['summary']['expired_contracts']]);
            fputcsv($file, ['']);

            // Contract Details
            fputcsv($file, ['Contract Details']);
            fputcsv($file, ['Name', 'Department', 'Start Date', 'End Date', 'Status', 'Created By', 'Performance Guarantee Amount', 'Performance Guarantee End Date']);

            foreach ($data['contracts'] as $contract) {
                fputcsv($file, [
                    $contract->name,
                    ucfirst(str_replace('_', ' ', $contract->department)),
                    $contract->start_date?->format('Y-m-d'),
                    $contract->end_date?->format('Y-m-d'),
                    ucfirst($contract->status),
                    $contract->creator->name ?? 'N/A',
                    $contract->performance_guarantee_amount ? number_format($contract->performance_guarantee_amount, 2) : 'N/A',
                    $contract->performance_guarantee_end_date?->format('Y-m-d') ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportAsPdf($data)
    {
        // Simple HTML-based PDF export (basic implementation)
        $html = $this->generateHtmlReport($data);

        $filename = 'contracts-export-' . now()->format('Y-m-d-H-i-s') . '.html';
        $headers = [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        return response($html, 200, $headers);
    }

    private function exportAsExcel($data)
    {
        // For now, export as CSV with Excel filename
        // In a real implementation, you'd use Laravel Excel or similar package
        $filename = 'contracts-export-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Summary section
            fputcsv($file, ['Contract Export Report']);
            fputcsv($file, ['Generated on', $data['summary']['generated_at']]);
            fputcsv($file, ['Generated by', $data['summary']['generated_by']]);
            fputcsv($file, ['']);
            fputcsv($file, ['Summary Statistics']);
            fputcsv($file, ['Total Contracts', $data['summary']['total_contracts']]);
            fputcsv($file, ['Active Contracts', $data['summary']['active_contracts']]);
            fputcsv($file, ['Expiring Soon', $data['summary']['expiring_contracts']]);
            fputcsv($file, ['Expired Contracts', $data['summary']['expired_contracts']]);
            fputcsv($file, ['']);

            // Contract Details
            fputcsv($file, ['Contract Details']);
            fputcsv($file, ['Name', 'Department', 'Start Date', 'End Date', 'Status', 'Created By', 'Performance Guarantee Amount', 'Performance Guarantee End Date']);

            foreach ($data['contracts'] as $contract) {
                fputcsv($file, [
                    $contract->name,
                    ucfirst(str_replace('_', ' ', $contract->department)),
                    $contract->start_date?->format('Y-m-d'),
                    $contract->end_date?->format('Y-m-d'),
                    ucfirst($contract->status),
                    $contract->creator->name ?? 'N/A',
                    $contract->performance_guarantee_amount ? number_format($contract->performance_guarantee_amount, 2) : 'N/A',
                    $contract->performance_guarantee_end_date?->format('Y-m-d') ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generateHtmlReport($data)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Contract Export Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1, h2 { color: #333; }
                table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .summary { background-color: #f9f9f9; padding: 15px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <h1>Contract Export Report</h1>
            <div class="summary">
                <p><strong>Generated on:</strong> ' . $data['summary']['generated_at'] . '</p>
                <p><strong>Generated by:</strong> ' . $data['summary']['generated_by'] . '</p>
                <h2>Summary Statistics</h2>
                <ul>
                    <li>Total Contracts: ' . $data['summary']['total_contracts'] . '</li>
                    <li>Active Contracts: ' . $data['summary']['active_contracts'] . '</li>
                    <li>Expiring Soon: ' . $data['summary']['expiring_contracts'] . '</li>
                    <li>Expired Contracts: ' . $data['summary']['expired_contracts'] . '</li>
                </ul>
            </div>

            <h2>Contract Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>PG Amount</th>
                        <th>PG End Date</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($data['contracts'] as $contract) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($contract->name) . '</td>';
            $html .= '<td>' . htmlspecialchars(ucfirst(str_replace('_', ' ', $contract->department))) . '</td>';
            $html .= '<td>' . ($contract->start_date ? $contract->start_date->format('Y-m-d') : 'N/A') . '</td>';
            $html .= '<td>' . ($contract->end_date ? $contract->end_date->format('Y-m-d') : 'N/A') . '</td>';
            $html .= '<td>' . htmlspecialchars(ucfirst($contract->status)) . '</td>';
            $html .= '<td>' . htmlspecialchars($contract->creator->name ?? 'N/A') . '</td>';
            $html .= '<td>' . ($contract->performance_guarantee_amount ? number_format($contract->performance_guarantee_amount, 2) : 'N/A') . '</td>';
            $html .= '<td>' . ($contract->performance_guarantee_end_date ? $contract->performance_guarantee_end_date->format('Y-m-d') : 'N/A') . '</td>';
            $html .= '</tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';

        return $html;
    }
}
