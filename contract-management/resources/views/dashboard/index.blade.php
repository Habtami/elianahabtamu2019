@extends('layouts.app')

@section('title', 'Dashboard - Contract Management System')

@section('styles')
<style>
    :root {
        --epss-blue: #1C75BC;
        --epss-blue-light: #E8F4FD;
        --epss-blue-dark: #1664a0;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
        --info: #3B82F6;
    }

    .dashboard-card {
        transition: all 0.3s ease;
        border: 1px solid #E5E7EB;
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }

    .contract-status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 600;
    }

    .activity-timeline {
        position: relative;
        padding-left: 2rem;
    }

    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 0.75rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #E5E7EB;
    }

    .activity-dot {
        position: absolute;
        left: 0.5rem;
        top: 0.5rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .contract-progress {
        height: 4px;
        border-radius: 2px;
        overflow: hidden;
        background: #E5E7EB;
    }

    .contract-progress-bar {
        height: 100%;
        transition: width 0.3s ease;
    }

    /* Sidebar Styles */
    .sidebar {
        width: 240px;
        min-height: 100vh;
        background: linear-gradient(180deg, #1C75BC 0%, #0F4C75 100%);
        color: white;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1000;
        transition: all 0.3s ease;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar.collapsed {
        width: 60px;
    }

    .sidebar.collapsed .sidebar-header {
        padding: 1rem 0.5rem;
        text-align: center;
    }

    .sidebar.collapsed .sidebar-header .sidebar-title {
        display: none;
    }

    .sidebar.collapsed .sidebar-menu-item span {
        display: none;
    }

    .sidebar.collapsed .sidebar-menu-item i {
        margin-right: 0;
        text-align: center;
        width: 100%;
    }

    .sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
    }

    .sidebar-logo {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        margin-right: 0.75rem;
        flex-shrink: 0;
    }

    .sidebar-logo-icon {
        font-size: 1.25rem;
        color: #1C75BC;
        font-weight: bold;
    }

    .sidebar-title {
        flex: 1;
        min-width: 0;
    }

    .sidebar-title h3 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        color: white;
        line-height: 1.2;
    }

    .sidebar-title p {
        font-size: 0.75rem;
        margin: 0;
        color: rgba(255, 255, 255, 0.8);
        line-height: 1.2;
    }

    .sidebar-toggle-btn {
        position: absolute;
        right: -12px;
        top: 50%;
        transform: translateY(-50%);
        background: #1C75BC;
        border: 2px solid white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 1001;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .sidebar-toggle-btn:hover {
        background: #0F4C75;
        transform: translateY(-50%) scale(1.1);
    }

    .sidebar-toggle-btn i {
        font-size: 0.8rem;
        color: white;
    }

    .sidebar-menu {
        padding: 1rem 0;
    }

    .sidebar-menu-item {
        margin: 0.25rem 0;
    }

    .sidebar-menu-item a {
        display: flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }

    .sidebar.collapsed .sidebar-menu-item a {
        padding: 0.75rem 1rem;
        justify-content: center;
    }

    .sidebar-menu-item a:hover,
    .sidebar-menu-item a.active {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        border-left-color: #1C75BC;
    }

    .sidebar-menu-item i {
        width: 20px;
        margin-right: 0.75rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .sidebar.collapsed .sidebar-menu-item i {
        margin-right: 0;
        width: 100%;
    }

    .main-content {
        margin-left: 240px;
        min-height: 100vh;
        background-color: #f9fafb;
        transition: margin-left 0.3s ease;
    }

    .main-content.expanded {
        margin-left: 60px;
    }

    .sidebar-toggle {
        display: none;
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1001;
        background: #1C75BC;
        color: white;
        border: none;
        padding: 0.5rem;
        border-radius: 0.375rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar.collapsed.show {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .sidebar-toggle {
            display: block;
        }

        .sidebar-toggle-btn {
            display: none;
        }
    }
</style>
@endsection

@section('content')
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">CM</div>
        </div>
        <div class="sidebar-title">
            <h3>Contract Manager</h3>
            <p>Management System</p>
        </div>
        <div class="sidebar-toggle-btn" onclick="toggleSidebarCollapse()" id="sidebarToggle">
            <i class="fas fa-chevron-left" id="toggleIcon"></i>
        </div>
    </div>

    <nav class="sidebar-menu">
        <!-- Dashboard - Available to all authenticated users -->
        <div class="sidebar-menu-item">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- Contracts - Available to all users who can view contracts -->
        @if(auth()->user()->canViewAllContracts())
        <div class="sidebar-menu-item">
            <a href="{{ route('contracts.index') }}" class="{{ request()->routeIs('contracts.*') && !request()->routeIs('contracts.reports') && !request()->routeIs('contracts.alerts') ? 'active' : '' }}">
                <i class="fas fa-file-contract"></i>
                <span>Contracts</span>
            </a>
        </div>
        @endif

        <!-- New Contract - Available to users who can create contracts -->
        @if(auth()->user()->canCreateContracts())
        <div class="sidebar-menu-item">
            <a href="{{ route('contracts.create') }}">
                <i class="fas fa-plus"></i>
                <span>New Contract</span>
            </a>
        </div>
        @endif

        <!-- Reports - Available to admin, finance, and managers -->
        @if(in_array(auth()->user()->role, ['admin','finance','gs_manager','cmd_manager']))
        <div class="sidebar-menu-item">
            <a href="{{ route('contracts.reports') }}" class="{{ request()->routeIs('contracts.reports') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </div>
        @endif

        <!-- Alerts - Available to all users who can view contracts -->
        @if(auth()->user()->canViewAllContracts())
        <div class="sidebar-menu-item">
            <a href="{{ route('contracts.alerts') }}" class="{{ request()->routeIs('contracts.alerts') ? 'active' : '' }}">
                <i class="fas fa-bell"></i>
                <span>Alerts</span>
            </a>
        </div>
        @endif

        <!-- User Management - Admin only -->
        @if(auth()->user()->role === 'admin')
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i>
                <span>User Management</span>
            </a>
        </div>
        @endif

        <!-- Divider -->
        <div class="border-t border-white/10 my-4 mx-4"></div>

        <!-- Profile/Account - Available to all -->
        <div class="sidebar-menu-item">
            <a href="#" onclick="event.preventDefault();">
                <i class="fas fa-user"></i>
                <span>{{ auth()->user()->name }}</span>
            </a>
        </div>

        <!-- Logout - Available to all -->
        <div class="sidebar-menu-item">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</div>

<!-- Mobile sidebar toggle -->
<button class="sidebar-toggle" onclick="toggleMobileSidebar()">
    <i class="fas fa-bars"></i>
</button>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <!-- Dashboard Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-2">Contract Management Dashboard</h1>
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-[#1C75BC]/10 flex items-center justify-center mr-2">
                                <i class="fas fa-user text-[#1C75BC] text-sm"></i>
                            </div>
                            <div>
                                <p class="text-gray-600">Welcome back, <span class="font-semibold text-gray-800">{{ $user->name }}</span></p>
                                <p class="text-sm text-gray-500">{{ $user->getRoleDisplayName() }}</p>
                            </div>
                        </div>
                        <div class="hidden lg:block text-gray-300">|</div>
                        <div class="hidden lg:flex items-center space-x-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="far fa-calendar-alt mr-1"></i>
                                <span>{{ now()->format('F d, Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="far fa-clock mr-1"></i>
                                <span>{{ now()->format('h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-4 lg:mt-0 flex space-x-3">
                    @if(auth()->user()->canCreateContracts())
                    <a href="{{ route('contracts.create') }}"
                       class="bg-[#1C75BC] hover:bg-[#1664a0] text-white font-semibold py-2 px-4 rounded-lg flex items-center transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        New Contract
                    </a>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin','finance','gs_manager','cmd_manager']))
                    <a href="{{ route('contracts.reports') }}"
                       class="bg-white border border-[#1C75BC] text-[#1C75BC] hover:bg-[#1C75BC]/5 font-semibold py-2 px-4 rounded-lg flex items-center transition-colors">
                        <i class="fas fa-chart-bar mr-2"></i>
                        View Reports
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="container mx-auto px-4 py-6">
        <!-- Alert Banner -->
        @if (count($alertContracts) > 0)
        <div class="mb-6 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-2 rounded-lg mr-3">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Attention Required</h3>
                        <p class="text-white/90">{{ count($alertContracts) }} contract(s) need immediate attention</p>
                    </div>
                </div>
                <a href="{{ route('contracts.alerts') }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg font-medium">
                    View All Alerts
                </a>
            </div>
        </div>
        @endif

        <!-- Key Metrics Section -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Key Performance Indicators</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Contracts Card -->
                <div class="dashboard-card bg-white rounded-xl shadow-sm p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Total Contracts</p>
                            <p class="text-3xl font-bold text-gray-800 mb-2">{{ $totalContracts }}</p>
                            <p class="text-green-600 text-sm font-medium">
                                <i class="fas fa-arrow-up mr-1"></i>
                                {{ $contractsByMonth->last()->count ?? 0 }} new this month
                            </p>
                        </div>
                        <div class="stat-card-icon bg-blue-50">
                            <i class="fas fa-file-contract text-xl text-blue-600"></i>
                        </div>
                    </div>
                    <div class="mt-4 contract-progress">
                        <div class="contract-progress-bar bg-blue-500" style="width: {{ $totalContracts > 0 ? ($activeContracts / $totalContracts * 100) : 0 }}%"></div>
                    </div>
                    <div class="mt-2 flex justify-between text-xs text-gray-500">
                        <span>{{ $activeContracts }} active</span>
                        <span>{{ $totalContracts - $activeContracts }} inactive</span>
                    </div>
                </div>

                <!-- Active Contracts Card -->
                <div class="dashboard-card bg-white rounded-xl shadow-sm p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Active Contracts</p>
                            <p class="text-3xl font-bold text-gray-800 mb-2">{{ $activeContracts }}</p>
                            <p class="text-green-600 text-sm font-medium">
                                <i class="fas fa-check-circle mr-1"></i>
                                {{ $totalContracts > 0 ? round($activeContracts / $totalContracts * 100, 1) : 0 }}% active rate
                            </p>
                        </div>
                        <div class="stat-card-icon bg-green-50">
                            <i class="fas fa-check-circle text-xl text-green-600"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-gray-600">
                        <div class="flex-1">
                            <div class="flex justify-between mb-1">
                                <span>Health Score</span>
                                <span class="font-semibold">{{ $totalContracts > 0 ? round($activeContracts / $totalContracts * 100) : 0 }}%</span>
                            </div>
                            <div class="contract-progress">
                                <div class="contract-progress-bar bg-green-500" style="width: {{ $totalContracts > 0 ? ($activeContracts / $totalContracts * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contracts Expiring -->
                <div class="dashboard-card bg-white rounded-xl shadow-sm p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Expiring Soon</p>
                            <p class="text-3xl font-bold text-gray-800 mb-2">{{ $expiringSoon }}</p>
                            <p class="text-yellow-600 text-sm font-medium">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $endingThisMonth->count() }} ending this month
                            </p>
                        </div>
                        <div class="stat-card-icon bg-yellow-50">
                            <i class="fas fa-clock text-xl text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">Renewal Timeline</span>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">
                                {{ $expiringSoon > 0 ? 'Action Required' : 'All Clear' }}
                            </span>
                        </div>
                        @if ($endingThisMonth->count() > 0)
                        <div class="text-xs text-gray-500">
                            {{ $endingThisMonth->first()->name }} ends {{ $endingThisMonth->first()->end_date->diffForHumans() }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Expired Contracts -->
                <div class="dashboard-card bg-white rounded-xl shadow-sm p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Expired Contracts</p>
                            <p class="text-3xl font-bold text-gray-800 mb-2">{{ $expiredContracts }}</p>
                            <p class="text-red-600 text-sm font-medium">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $totalContracts > 0 ? round($expiredContracts / $totalContracts * 100, 1) : 0 }}% expired
                            </p>
                        </div>
                        <div class="stat-card-icon bg-red-50">
                            <i class="fas fa-exclamation-circle text-xl text-red-600"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Requires Review</span>
                            <span class="font-semibold">{{ $expiredContracts }}</span>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            Immediate action required for expired contracts
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Data Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Department Distribution Chart -->
            <div class="dashboard-card bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Contracts by Department</h3>
                <div class="h-64">
                    <canvas id="departmentChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    @foreach ($contractsByDepartment as $dept)
                    <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $dept->color ?? '#3B82F6' }}"></div>
                            <span class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $dept->department)) }}</span>
                        </div>
                        <span class="text-sm font-semibold">{{ $dept->count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Timeline Chart -->
            <div class="dashboard-card bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Contract Activity Timeline</h3>
                <div class="h-64">
                    <canvas id="timelineChart"></canvas>
                </div>
                <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
                    <div>
                        <span class="font-semibold">{{ $contractsByMonth->sum('count') }}</span>
                        <span class="ml-1">contracts created in last 6 months</span>
                    </div>
                    <div class="text-green-600 font-medium">
                        <i class="fas fa-arrow-up mr-1"></i>
                        {{ $contractsByMonth->last()->count ?? 0 }} this month
                    </div>
                </div>
            </div>
        </div>

        <!-- Contracts and Activity Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Contracts -->
            <div class="lg:col-span-2 dashboard-card bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Recent Contracts</h3>
                    <a href="{{ route('contracts.index') }}" class="text-[#1C75BC] hover:text-[#1664a0] text-sm font-semibold flex items-center">
                        View All
                        <i class="fas fa-chevron-right ml-1"></i>
                    </a>
                </div>

                @if ($recentContracts->count() > 0)
                <div class="space-y-4">
                    @foreach ($recentContracts as $contract)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-[#1C75BC] transition-colors">
                        <div class="flex-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <a href="{{ route('contracts.show', $contract->id) }}"
                                       class="font-semibold text-gray-800 hover:text-[#1C75BC] mb-1 block">
                                        {{ \Illuminate\Support\Str::limit($contract->name, 40) }}
                                    </a>
                                    <div class="flex items-center space-x-3 text-sm text-gray-500">
                                        <span class="flex items-center">
                                            <i class="far fa-building mr-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $contract->department)) }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="far fa-calendar mr-1"></i>
                                            {{ $contract->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4 flex flex-col items-end">
                                    <span class="contract-status-badge
                                        @if($contract->status === 'active') bg-green-100 text-green-800
                                        @elseif($contract->status === 'expiring') bg-yellow-100 text-yellow-800
                                        @elseif($contract->status === 'expired') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($contract->status) }}
                                    </span>
                                    <span class="text-xs text-gray-500 mt-2">
                                        {{ $contract->getDaysUntilContractEnd() }} days remaining
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="text-gray-400 mb-3">
                        <i class="fas fa-file-contract text-4xl"></i>
                    </div>
                    <p class="text-gray-500 mb-4">No contracts found</p>
                    @if(auth()->user()->canCreateContracts())
                    <a href="{{ route('contracts.create') }}" class="inline-flex items-center text-[#1C75BC] hover:text-[#1664a0] font-semibold">
                        <i class="fas fa-plus mr-2"></i>
                        Create Your First Contract
                    </a>
                    @endif
                </div>
                @endif
            </div>

            <!-- Recent Activity -->
            <div class="dashboard-card bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Recent Activity</h3>
                </div>

                @if ($recentActivity->count() > 0)
                <div class="activity-timeline space-y-6">
                    @foreach ($recentActivity->take(5) as $activity)
                    <div class="relative pl-8">
                        <div class="activity-dot
                            @if($activity->field_name == 'contract_end_date') bg-yellow-500
                            @elseif($activity->field_name == 'performance_guarantee') bg-blue-500
                            @else bg-gray-500 @endif">
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800">
                                        <span class="text-[#1C75BC]">{{ $activity->edited_by }}</span>
                                        updated
                                        <span class="font-medium">
                                            @if($activity->field_name == 'contract_end_date')
                                            contract end date
                                            @elseif($activity->field_name == 'performance_guarantee')
                                            performance guarantee
                                            @else
                                            {{ $activity->field_name }}
                                            @endif
                                        </span>
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        for
                                        <a href="{{ route('contracts.show', $activity->contract_id) }}"
                                           class="text-[#1C75BC] hover:text-[#1664a0] font-medium">
                                            {{ $activity->contract->name }}
                                        </a>
                                    </p>
                                    @if ($activity->reason)
                                    <div class="mt-2 p-3 bg-white rounded border-l-4 border-gray-300">
                                        <p class="text-sm text-gray-700 italic">"{{ $activity->reason }}"</p>
                                    </div>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-500 whitespace-nowrap">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="text-gray-400 mb-3">
                        <i class="fas fa-history text-4xl"></i>
                    </div>
                    <p class="text-gray-500">No recent activity</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Hidden logout form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Department Distribution Chart
        const departmentCtx = document.getElementById('departmentChart').getContext('2d');
        const departmentChart = new Chart(departmentCtx, {
            type: 'pie',
            data: {
                labels: @json($contractsByDepartment->pluck('department')->map(fn($d) => ucfirst(str_replace('_', ' ', $d)))),
                datasets: [{
                    data: @json($contractsByDepartment->pluck('count')),
                    backgroundColor: ['#1C75BC', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                        }
                    }
                }
            }
        });

        // Timeline Chart
        const timelineCtx = document.getElementById('timelineChart').getContext('2d');
        const timelineChart = new Chart(timelineCtx, {
            type: 'line',
            data: {
                labels: @json($contractsByMonth->pluck('month')),
                datasets: [{
                    label: 'Contracts Created',
                    data: @json($contractsByMonth->pluck('count')),
                    borderColor: '#1C75BC',
                    backgroundColor: 'rgba(28, 117, 188, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: '#1C75BC'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Load sidebar state from localStorage
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (sidebarCollapsed) {
            toggleSidebarCollapse(false); // Don't animate on load
        }
    });

    function toggleSidebarCollapse(animate = true) {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleIcon = document.getElementById('toggleIcon');

        const isCollapsed = sidebar.classList.contains('collapsed');

        if (animate) {
            sidebar.style.transition = 'all 0.3s ease';
            mainContent.style.transition = 'margin-left 0.3s ease';
        }

        if (isCollapsed) {
            // Expand sidebar
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
            toggleIcon.classList.remove('fa-chevron-right');
            toggleIcon.classList.add('fa-chevron-left');
            localStorage.setItem('sidebarCollapsed', 'false');
        } else {
            // Collapse sidebar
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            toggleIcon.classList.remove('fa-chevron-left');
            toggleIcon.classList.add('fa-chevron-right');
            localStorage.setItem('sidebarCollapsed', 'true');
        }
    }

    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('show');
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.querySelector('.sidebar-toggle');

        if (window.innerWidth <= 768 &&
            !sidebar.contains(event.target) &&
            event.target !== toggleBtn &&
            !toggleBtn.contains(event.target)) {
            sidebar.classList.remove('show');
        }
    });
</script>
@endsection
