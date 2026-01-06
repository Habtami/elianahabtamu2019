<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Total contracts
        $totalContracts = Contract::forUser($user)->count();

        // Active contracts
        $activeContracts = Contract::forUser($user)->active()->count();

        // Expiring soon contracts (within alert days)
        $expiringSoon = Contract::forUser($user)->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->count();

        // Expired contracts
        $expiredContracts = Contract::forUser($user)->expired()->count();

        // Alert contracts
        $alertContracts = $this->getAlertContracts($user);

        // Contracts by department
        $contractsByDepartment = Contract::forUser($user)->select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
            ->get()
            ->map(function ($item) {
                $item->color = $this->getDepartmentColor($item->department);
                return $item;
            });

        // Contracts by month (last 6 months)
        $contractsByMonth = Contract::forUser($user)->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->get();

        // Recent contracts
        $recentContracts = Contract::forUser($user)->latest()->take(5)->get();

        // Contracts ending this month
        $endingThisMonth = Contract::forUser($user)->whereMonth('end_date', now()->month)
            ->whereYear('end_date', now()->year)
            ->orderBy('end_date')
            ->get();

        // Recent activity
        $recentActivity = ActivityLog::with('contract')
            ->whereHas('contract', function ($query) use ($user) {
                $query->forUser($user);
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