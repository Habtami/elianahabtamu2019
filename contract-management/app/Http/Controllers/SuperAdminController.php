<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contract;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                return redirect()->route('dashboard')
                    ->with('error', 'You are not authorized to access the super admin dashboard.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        // Get higher level statistics
        $totalUsers = User::count();
        $activeUsers = User::where('status', true)->count();
        $adminUsers = User::whereIn('role', ['super_admin', 'admin'])->count();
        $inactiveUsers = User::where('status', false)->count();

        $totalContracts = Contract::count();
        $activeContracts = Contract::where('status', 'active')->count();
        $expiringContracts = Contract::where('end_date', '<=', now()->addDays(30))->count();
        $expiredContracts = Contract::where('end_date', '<', now())->count();

        $recentActivities = ActivityLog::with(['contract' => function($q) {
            $q->withTrashed();
        }])->latest()->take(10)->get();

        return view('admin.super-admin-dashboard', compact(
            'totalUsers', 'activeUsers', 'adminUsers', 'inactiveUsers',
            'totalContracts', 'activeContracts', 'expiringContracts', 'expiredContracts',
            'recentActivities'
        ));
    }
}
