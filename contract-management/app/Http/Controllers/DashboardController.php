<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // If admin or super admin, show admin dashboard
        if ($user->isAdmin()) {
            return $this->superAdminDashboard();
        }

        // Total contracts
        $totalContracts = Contract::forUser($user)->count();

        // Active contracts
        $activeContracts = Contract::forUser($user)->active()->count();

        // Expiring soon contracts (using actual status based on alert days)
        $expiringSoon = Contract::forUser($user)->expiring()->count();

        // Expired contracts
        $expiredContracts = Contract::forUser($user)->expired()->count();

        // Alert contracts
        $alertContracts = $this->getAlertContracts($user);

        // Contracts by department
        $departments = User::getDepartments();
        $contractsByDepartment = Contract::forUser($user)->select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
            ->get()
            ->map(function ($item) use ($departments) {
                $item->color = $this->getDepartmentColor($item->department);
                $item->department_name = $departments[$item->department] ?? ucfirst(str_replace('_', ' ', $item->department));
                return $item;
            });

        // Contracts by month (last 6 months)
        $startDate = now()->subMonths(5)->startOfMonth();
        $endDate = now();

        $monthlyData = Contract::forUser($user)->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Fill in missing months to ensure a continuous line in the chart
        $contractsByMonth = collect();
        $current = clone $startDate;
        
        while ($current <= $endDate) {
            $monthKey = $current->format('Y-m');
            $contractsByMonth->push((object)[
                'month' => $current->format('M Y'),
                'count' => $monthlyData[$monthKey] ?? 0
            ]);
            $current->addMonth();
        }

        // If we only have one month, add the previous month with 0 for better line rendering
        if ($contractsByMonth->count() === 1) {
            $firstMonth = \Carbon\Carbon::createFromFormat('M Y', $contractsByMonth->first()->month);
            $prevMonth = $firstMonth->subMonth();
            $contractsByMonth->prepend((object)[
                'month' => $prevMonth->format('M Y'),
                'count' => 0
            ]);
        }

        // Recent contracts
        $recentContracts = Contract::forUser($user)->latest()->take(5)->get();

        // Contracts ending this month
        $endingThisMonth = Contract::forUser($user)->whereMonth('end_date', now()->month)
            ->whereYear('end_date', now()->year)
            ->orderBy('end_date')
            ->get();

        // Recent activity
        $recentActivity = ActivityLog::with(['contract' => function ($query) {
            $query->withTrashed();
        }])
            ->whereHas('contract', function ($query) use ($user) {
                $query->withTrashed()->forUser($user);
            })
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.index', compact(
            'user',
            'totalContracts',
            'activeContracts',
            'expiringSoon',
            'expiredContracts',
            'alertContracts',
            'contractsByDepartment',
            'contractsByMonth',
            'recentContracts',
            'endingThisMonth',
            'recentActivity'
        ));
    }

    private function superAdminDashboard()
    {
        // Get higher level statistics
        $totalUsers = User::count();
        $activeUsers = User::where('status', true)->count();
        $adminUsers = User::whereIn('role', ['super_admin', 'admin'])->count();
        $inactiveUsers = User::where('status', false)->count();
        $totalDepartments = User::distinct('department')->count('department');

        $totalContracts = Contract::count();
        $activeContracts = Contract::active()->count();
        $expiringContracts = Contract::expiring()->count();
        $expiredContracts = Contract::expired()->count();

        $recentActivities = ActivityLog::with(['contract' => function($q) {
            $q->withTrashed();
        }])->latest()->take(10)->get();

        $systemAlerts = collect(); // Placeholder for system alerts

        return view('admin.super-admin-dashboard', compact(
            'totalUsers', 'activeUsers', 'adminUsers', 'inactiveUsers', 'totalDepartments',
            'totalContracts', 'activeContracts', 'expiringContracts', 'expiredContracts',
            'recentActivities', 'systemAlerts'
        ));
    }
    
    public function summary(Request $request, string $scope)
    {
        $user = $request->user();

        $contractScopes = ['total-contracts', 'active-contracts', 'expiring-contracts', 'expired-contracts'];
        $canViewContractDetails = in_array($scope, $contractScopes, true) && in_array($user->role, [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_ADMIN,
            User::ROLE_FINANCE,
            User::ROLE_GS_MANAGER,
            User::ROLE_CMD_MANAGER,
        ], true);

        if (!$user->isAdmin() && !$canViewContractDetails) {
            abort(403, 'You are not authorized to view dashboard details.');
        }

        switch ($scope) {
            case 'total-users':
                $title = 'Total Users';
                $items = User::orderBy('name')->limit(100)->get()
                    ->map(fn (User $row) => $this->userSummaryItem($user, $row));
                break;

            case 'active-users':
                $title = 'Active Users';
                $items = User::where('status', true)->orderBy('name')->limit(100)->get()
                    ->map(fn (User $row) => $this->userSummaryItem($user, $row));
                break;

            case 'admin-users':
                $title = 'Admin Users';
                $items = User::whereIn('role', ['super_admin', 'admin'])->orderBy('name')->limit(100)->get()
                    ->map(fn (User $row) => $this->userSummaryItem($user, $row));
                break;

            case 'departments':
                $title = 'Departments';
                $departments = User::getDepartments();
                $items = User::select('department', DB::raw('count(*) as total'))
                    ->groupBy('department')
                    ->orderBy('department')
                    ->get()
                    ->map(fn ($row) => [
                        'title' => $departments[$row->department] ?? ($row->department ?: 'Unassigned'),
                        'subtitle' => $row->total . ' user(s)',
                        'meta' => null,
                        'badge' => (string) $row->total,
                        'tone' => 'blue',
                        'url' => null,
                    ]);
                break;

            case 'total-contracts':
                $title = 'Total Contracts';
                $items = Contract::forUser($user)->orderByDesc('created_at')->limit(100)->get()
                    ->map(fn (Contract $contract) => $this->contractSummaryItem($contract));
                break;

            case 'active-contracts':
                $title = 'Active Contracts';
                $items = Contract::forUser($user)->active()->orderBy('end_date')->limit(100)->get()
                    ->map(fn (Contract $contract) => $this->contractSummaryItem($contract));
                break;

            case 'expiring-contracts':
                $title = 'Expiring Soon';
                $items = Contract::forUser($user)->expiring()->orderBy('end_date')->limit(100)->get()
                    ->map(fn (Contract $contract) => $this->contractSummaryItem($contract));
                break;

            case 'expired-contracts':
                $title = 'Expired Contracts';
                $items = Contract::forUser($user)->expired()->orderByDesc('end_date')->limit(100)->get()
                    ->map(fn (Contract $contract) => $this->contractSummaryItem($contract));
                break;

            default:
                abort(404);
        }

        return response()->json([
            'title' => $title,
            'count' => $items->count(),
            'items' => $items->values(),
        ]);
    }

    private function userSummaryItem(User $viewer, User $user): array
    {
        return [
            'title' => $user->name,
            'subtitle' => $user->email,
            'meta' => $user->getRoleDisplayName() . ' · ' . $user->getDepartmentDisplayName(),
            'badge' => $user->status ? 'Active' : 'Inactive',
            'tone' => $user->status ? 'green' : 'gray',
            'url' => $viewer->hasPermission('admin_panel') ? route('admin.users.edit', $user->id) : null,
        ];
    }

    private function contractSummaryItem(Contract $contract): array
    {
        $tone = [
            'active' => 'green',
            'expiring' => 'amber',
            'expired' => 'red',
        ][$contract->status] ?? 'gray';

        return [
            'title' => $contract->name,
            'subtitle' => $contract->supplier_name ?: '—',
            'meta' => trim(implode(' · ', array_filter([
                User::getDepartments()[$contract->department] ?? ucfirst(str_replace('_', ' ', (string) $contract->department)),
                $contract->end_date?->format('M d, Y') ? 'Ends ' . $contract->end_date->format('M d, Y') : null,
            ])), ' ·'),
            'badge' => ucfirst((string) $contract->status),
            'tone' => $tone,
            'url' => route('contracts.show', $contract->id),
        ];
    }
    
    private function getAlertContracts($user)
    {
        $contracts = Contract::forUser($user)->get();
        $alertContracts = [];
        
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
        
        return $alertContracts;
    }
    
    private function getDepartmentColor($department)
    {
        $colors = [
            'cmd' => '#3B82F6',
            'general_service' => '#10B981',
            'finance' => '#F59E0B',
        ];
        
        return $colors[$department] ?? '#6B7280';
    }
}