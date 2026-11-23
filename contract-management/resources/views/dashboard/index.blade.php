@extends('layouts.app')

@section('title', 'Dashboard - Contract Management System')

@section('page-title', 'Contract Management Dashboard')

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
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
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
</style>
@endsection

@section('content')
<!-- Main Dashboard Content -->
<div class="container mx-auto px-4 py-6">
    <!-- Alert Banner -->
    @if (count($alertContracts) > 0)
    <div class="mb-6 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-2xl shadow-lg overflow-hidden">
        <div class="p-4 flex items-center justify-between flex-wrap gap-3">
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
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Key Performance Indicators</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Contracts Card -->
            <div class="kpi-card blue">
                <div>
                    <p class="kpi-title">Total Contracts</p>
                    <p class="kpi-value">{{ $totalContracts }}</p>
                    <p class="kpi-subtitle">All contracts in system</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-file-contract"></i></div>
            </div>

            <!-- Active Contracts Card -->
            <div class="kpi-card green">
                <div>
                    <p class="kpi-title">Active Contracts</p>
                    <p class="kpi-value">{{ $activeContracts }}</p>
                    <p class="kpi-subtitle">Currently in force</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
            </div>

            <!-- Contracts Expiring -->
            <div class="kpi-card yellow">
                <div>
                    <p class="kpi-title">Expiring Soon</p>
                    <p class="kpi-value">{{ $expiringSoon }}</p>
                    <p class="kpi-subtitle">Ending this month</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-clock"></i></div>
            </div>

            <!-- Expired Contracts -->
            <div class="kpi-card red">
                <div>
                    <p class="kpi-title">Expired Contracts</p>
                    <p class="kpi-value">{{ $expiredContracts }}</p>
                    <p class="kpi-subtitle">Immediate action required</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-circle-exclamation"></i></div>
            </div>
        </div>
    </div>

    <!-- Charts and Data Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Department Distribution Chart -->
        <div class="card p-5">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Contracts by Department</h3>
            <div class="h-56">
                <canvas id="departmentChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2">
                @foreach ($contractsByDepartment as $dept)
                <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $dept->color ?? '#3B82F6' }}"></div>
                        <span class="text-sm text-gray-700">{{ $dept->department_name }}</span>
                    </div>
                    <span class="text-sm font-semibold">{{ $dept->count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Timeline Chart -->
        <div class="card p-5">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Contract Activity Timeline</h3>
            <div class="h-56">
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
        <div class="lg:col-span-2 card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Recent Contracts</h3>
                <a href="{{ route('contracts.index') }}" class="text-[#1C75BC] hover:text-[#1664a0] text-sm font-semibold flex items-center">
                    View All
                    <i class="fas fa-chevron-right ml-1"></i>
                </a>
            </div>

            @if ($recentContracts->count() > 0)
            <div class="space-y-3">
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
                                        {{ $contract->created_at ? $contract->created_at->format('M d, Y') : 'N/A' }}
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
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Recent Activity</h3>
            </div>

            @if ($recentActivity->count() > 0)
            <div class="activity-timeline space-y-5">
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
                                @if($activity->contract)
                                    <a href="{{ route('contracts.show', $activity->contract_id) }}"
                                       class="text-[#1C75BC] hover:text-[#1664a0] font-medium">
                                        {{ $activity->contract->name }}
                                    </a>
                                @else
                                    <span class="text-gray-500 italic">a deleted contract</span>
                                @endif
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
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Department Distribution Chart
        const departmentCtx = document.getElementById('departmentChart').getContext('2d');
        const departmentChart = new Chart(departmentCtx, {
            type: 'doughnut',
            data: {
                labels: @json($contractsByDepartment->pluck('department_name')),
                datasets: [{
                    data: @json($contractsByDepartment->pluck('count')),
                    backgroundColor: ['#1C75BC', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444', '#0EA5E9'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 10,
                    borderRadius: 6,
                    spacing: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            font: { family: 'Inter', size: 11 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { family: 'Inter', size: 12 },
                        bodyFont: { family: 'Inter', size: 12 }
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
                    backgroundColor: 'rgba(28, 117, 188, 0.08)',
                    borderWidth: 3,
                    tension: 0.42,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#1C75BC',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { family: 'Inter', size: 12 },
                        bodyFont: { family: 'Inter', size: 12 }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { family: 'Inter', size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#94a3b8',
                            font: { family: 'Inter', size: 11 }
                        },
                        grid: { color: '#eef2f7' },
                        border: { display: false }
                    }
                }
            }
        });

    });
</script>
@endsection
