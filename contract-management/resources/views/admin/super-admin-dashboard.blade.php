@extends('layouts.app')

@section('title', 'Super Admin Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
<style>
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 0.75rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #E5E7EB;
    }
    /* Recent activities compact/full styles */
    .recent-activities-header { display:flex; gap:0.75rem; align-items:center; }
    .activities-toggle-btn { background: transparent; border: none; color: #6B7280; cursor: pointer; }
    .activities-toggle-btn:hover { color: #1C75BC; }
    .activities-list { transition: max-height 0.28s ease; }
    .activities-list.compact { max-height: 300px; overflow: auto; }
    .activities-list.full { max-height: 1000vh; overflow: visible; }
</style>
@endsection

@section('content')
<div class="dashboard-wrap">
        <!-- Header Section -->
        <div class="flex flex-wrap items-end justify-between gap-3 mb-5">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 mb-1">{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'System Admin' }} Dashboard</h1>
                <p class="text-sm text-slate-500 mb-0">Comprehensive system overview and management</p>
            </div>
            <span class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-full px-3 py-1.5 shadow-sm">
                <i class="fas fa-circle text-[6px] text-emerald-500"></i> System operational
            </span>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Users -->
            <button type="button" onclick="openStatModal('total-users', 'Total Users')" title="View total users"
                    class="kpi-card blue text-left w-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400">
                <div>
                    <p class="kpi-title">Total Users</p>
                    <p class="kpi-value">{{ $totalUsers }}</p>
                    <p class="kpi-subtitle">All users in system</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-users"></i></div>
            </button>

            <!-- Active Users -->
            <button type="button" onclick="openStatModal('active-users', 'Active Users')" title="View active users"
                    class="kpi-card green text-left w-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-400">
                <div>
                    <p class="kpi-title">Active Users</p>
                    <p class="kpi-value">{{ $activeUsers }}</p>
                    <p class="kpi-subtitle">Currently active accounts</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-user-check"></i></div>
            </button>

            <!-- Admin Users -->
            <button type="button" onclick="openStatModal('admin-users', 'Admin Users')" title="View admin users"
                    class="kpi-card red text-left w-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-400">
                <div>
                    <p class="kpi-title">Admin Users</p>
                    <p class="kpi-value">{{ $adminUsers }}</p>
                    <p class="kpi-subtitle">Privileged accounts</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-user-shield"></i></div>
            </button>

            <!-- Departments -->
            <button type="button" onclick="openStatModal('departments', 'Departments')" title="View departments"
                    class="kpi-card yellow text-left w-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-400">
                <div>
                    <p class="kpi-title">Departments</p>
                    <p class="kpi-value">{{ $totalDepartments }}</p>
                    <p class="kpi-subtitle">Organization units</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-building"></i></div>
            </button>
        </div>

        <!-- Contracts Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Contracts -->
            <button type="button" onclick="openStatModal('total-contracts', 'Total Contracts')" title="View all contracts"
                    class="kpi-card blue text-left w-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-400">
                <div>
                    <p class="kpi-title">Total Contracts</p>
                    <p class="kpi-value">{{ $totalContracts }}</p>
                    <p class="kpi-subtitle">All contracts in system</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-file-contract"></i></div>
            </button>

            <!-- Active Contracts -->
            <button type="button" onclick="openStatModal('active-contracts', 'Active Contracts')" title="View active contracts"
                    class="kpi-card green text-left w-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-400">
                <div>
                    <p class="kpi-title">Active Contracts</p>
                    <p class="kpi-value">{{ $activeContracts }}</p>
                    <p class="kpi-subtitle">Currently in force</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
            </button>

            <!-- Expiring Soon -->
            <button type="button" onclick="openStatModal('expiring-contracts', 'Expiring Soon')" title="View contracts expiring soon"
                    class="kpi-card yellow text-left w-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-400">
                <div>
                    <p class="kpi-title">Expiring Soon</p>
                    <p class="kpi-value">{{ $expiringContracts }}</p>
                    <p class="kpi-subtitle">Need attention soon</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-clock"></i></div>
            </button>

            <!-- Expired Contracts -->
            <button type="button" onclick="openStatModal('expired-contracts', 'Expired Contracts')" title="View expired contracts"
                    class="kpi-card red text-left w-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-400">
                <div>
                    <p class="kpi-title">Expired Contracts</p>
                    <p class="kpi-value">{{ $expiredContracts }}</p>
                    <p class="kpi-subtitle">Past end date</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-times-circle"></i></div>
            </button>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- User Distribution Chart -->
            <div class="card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">User Distribution</h3>
                    <button onclick="refreshData(event)" class="text-slate-400 hover:text-[#1C75BC] transition-colors" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="flex flex-col md:flex-row items-center justify-around h-56">
                    <div class="w-40 h-40">
                        <canvas id="userChart"></canvas>
                    </div>
                    <div class="mt-4 md:mt-0 space-y-3">
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 mr-3"></span>
                            <span class="text-sm text-gray-600">Active: <strong>{{ $activeUsers }}</strong></span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full bg-rose-500 mr-3"></span>
                            <span class="text-sm text-gray-600">Inactive: <strong>{{ $inactiveUsers }}</strong></span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full bg-blue-500 mr-3"></span>
                            <span class="text-sm text-gray-600">Admin: <strong>{{ $adminUsers }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contract Status Chart -->
            <div class="card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Contract Status Breakdown</h3>
                    <button onclick="refreshData(event)" class="text-slate-400 hover:text-[#1C75BC] transition-colors" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="h-56">
                    <canvas id="contractChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities Section -->
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-2">
                <div class="recent-activities-header">
                    <h3 class="text-lg font-bold text-slate-800">System-wide Recent Activities</h3>
                    <span class="text-sm text-slate-500">({{ $recentActivities->count() }})</span>
                </div>
                <div class="flex items-center gap-3">
                    <button id="activitiesToggleBtn" class="activities-toggle-btn text-sm" aria-expanded="true">Compact view</button>
                    <button onclick="refreshData(event)" class="text-slate-400 hover:text-[#1C75BC] transition-colors" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="p-5">
                <div id="activitiesList" class="activity-timeline activities-list relative pl-8 space-y-6" data-count="{{ $recentActivities->count() }}">
                    @forelse($recentActivities as $activity)
                    <div class="relative">
                        <!-- Icon Circle -->
                        <div class="absolute -left-[2.15rem] top-0 w-6 h-6 rounded-full bg-blue-100 border-4 border-white flex items-center justify-center z-10">
                            <i class="fas fa-user text-[10px] text-blue-600"></i>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div>
                                <p class="text-gray-800 font-semibold text-sm">
                                    {{ $activity->edited_by ?? 'System' }}
                                </p>
                                <p class="text-gray-600 text-sm mt-1">
                                    {{ $activity->description }}
                                </p>
                                @if($activity->reason)
                                <div class="mt-2 text-xs italic text-gray-500 bg-gray-50 p-2 rounded border-l-2 border-gray-200">
                                    "{{ $activity->reason }}"
                                </div>
                                @endif
                            </div>
                            <div class="mt-2 md:mt-0">
                                <span class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $activity->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-history text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No recent activities found</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Stat detail modal -->
        <div id="statModal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="statModalTitle">
            <div class="absolute inset-0 bg-black/50" onclick="closeStatModal()"></div>
            <div class="relative mx-auto mt-10 mb-10 w-full max-w-2xl px-4 max-h-[calc(100vh-5rem)] overflow-y-auto">
                <div class="bg-white rounded-xl shadow-xl">
                    <div class="flex items-start justify-between px-5 py-4 border-b border-gray-200">
                        <div>
                            <h3 id="statModalTitle" class="text-lg font-semibold text-gray-800"></h3>
                            <p id="statModalMeta" class="text-sm text-gray-500 mt-0.5"></p>
                        </div>
                        <button type="button" onclick="closeStatModal()" aria-label="Close"
                                class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                    </div>
                    <div class="p-5">
                        <div id="statModalLoading" class="text-center py-10 text-gray-500">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Loading...
                        </div>
                        <div id="statModalEmpty" class="hidden text-center py-10">
                            <i class="fas fa-inbox text-3xl text-gray-300"></i>
                            <p class="mt-3 text-gray-600">No records found.</p>
                        </div>
                        <div id="statModalList" class="hidden divide-y divide-gray-100"></div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection

@section('scripts')
<script>
    let statRequestSeq = 0;

    const statTones = {
        green: 'bg-green-100 text-green-800',
        amber: 'bg-amber-100 text-amber-800',
        red: 'bg-red-100 text-red-800',
        blue: 'bg-blue-100 text-blue-800',
        gray: 'bg-gray-100 text-gray-700',
    };

    function escapeStatHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (ch) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
        });
    }

    function openStatModal(scope, title) {
        const modal = document.getElementById('statModal');
        if (!modal) return;

        document.getElementById('statModalTitle').textContent = title;
        document.getElementById('statModalMeta').textContent = '';
        document.getElementById('statModalList').innerHTML = '';
        document.getElementById('statModalList').classList.add('hidden');
        document.getElementById('statModalEmpty').classList.add('hidden');
        document.getElementById('statModalLoading').classList.remove('hidden');

        modal.classList.remove('hidden');
        loadStatSummary(scope);
    }

    function closeStatModal() {
        const modal = document.getElementById('statModal');
        if (modal) modal.classList.add('hidden');
    }

    async function loadStatSummary(scope) {
        const seq = ++statRequestSeq;

        try {
            const response = await fetch('{{ url('/dashboard/summary') }}/' + encodeURIComponent(scope), {
                headers: { 'Accept': 'application/json' },
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok) throw new Error(data.message || 'Unable to load details.');
            if (seq !== statRequestSeq) return;

            renderStatSummary(data);
        } catch (error) {
            if (seq !== statRequestSeq) return;
            document.getElementById('statModalLoading').classList.add('hidden');
            const empty = document.getElementById('statModalEmpty');
            empty.querySelector('p').textContent = error.message;
            empty.classList.remove('hidden');
        }
    }

    function renderStatSummary(data) {
        document.getElementById('statModalLoading').classList.add('hidden');
        document.getElementById('statModalMeta').textContent = (data.count ?? 0) + ' record(s)';

        const empty = document.getElementById('statModalEmpty');
        const list = document.getElementById('statModalList');
        const items = Array.isArray(data.items) ? data.items : [];

        if (!items.length) {
            empty.classList.remove('hidden');
            return;
        }

        list.innerHTML = items.map(function (item, index) {
            const title = item.url
                ? '<a href="' + escapeStatHtml(item.url) + '" class="font-medium text-[#1C75BC] hover:underline">' + escapeStatHtml(item.title) + '</a>'
                : '<span class="font-medium text-gray-800">' + escapeStatHtml(item.title) + '</span>';

            const badge = item.badge
                ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium ' +
                    (statTones[item.tone] || statTones.gray) + '">' + escapeStatHtml(item.badge) + '</span>'
                : '';

            return '<div class="flex items-start gap-3 py-2.5">' +
                '<div class="w-6 shrink-0 pt-0.5 text-right text-xs font-semibold text-gray-400">' + (index + 1) + '</div>' +
                '<div class="min-w-0 flex-1">' + title +
                (item.subtitle ? '<div class="text-sm text-gray-600 truncate">' + escapeStatHtml(item.subtitle) + '</div>' : '') +
                (item.meta ? '<div class="text-xs text-gray-500 mt-0.5">' + escapeStatHtml(item.meta) + '</div>' : '') +
                '</div>' +
                (badge ? '<div class="shrink-0">' + badge + '</div>' : '') +
                '</div>';
        }).join('');

        list.classList.remove('hidden');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeStatModal();
    });

    document.addEventListener('DOMContentLoaded', function() {
        // User Distribution Chart
        const userCtx = document.getElementById('userChart').getContext('2d');
        new Chart(userCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive', 'Admin'],
                datasets: [{
                    data: [{{ $activeUsers }}, {{ $inactiveUsers }}, {{ $adminUsers }}],
                    backgroundColor: ['#10B981', '#F43F5E', '#3B82F6'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '70%'
            }
        });

        // Recent activities compact/full toggle
        (function() {
            const list = document.getElementById('activitiesList');
            const btn = document.getElementById('activitiesToggleBtn');
            if (!list || !btn) return;

            const count = parseInt(list.dataset.count || '0', 10);
            const storageKey = 'dashboard.activitiesCompact';

            // default to compact when many items
            let compact = localStorage.getItem(storageKey);
            if (compact === null) {
                compact = count > 8 ? '1' : '0';
            }

            function applyState() {
                if (compact === '1') {
                    list.classList.add('compact');
                    list.classList.remove('full');
                    btn.textContent = 'Full view';
                    btn.setAttribute('aria-expanded', 'false');
                } else {
                    list.classList.remove('compact');
                    list.classList.add('full');
                    btn.textContent = 'Compact view';
                    btn.setAttribute('aria-expanded', 'true');
                }
            }

            btn.addEventListener('click', function() {
                compact = compact === '1' ? '0' : '1';
                localStorage.setItem(storageKey, compact);
                applyState();
            });

            // initial apply
            applyState();
        })();

        // Contract Status Chart
        const contractCtx = document.getElementById('contractChart').getContext('2d');
        new Chart(contractCtx, {
            type: 'bar',
            data: {
                labels: ['Active', 'Expiring Soon', 'Expired'],
                datasets: [{
                    label: 'Contracts',
                    data: [{{ $activeContracts }}, {{ $expiringContracts }}, {{ $expiredContracts }}],
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { drawBorder: false, color: '#F3F4F6' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });

    function refreshData(e) {
        const btn = e.currentTarget;
        const icon = btn.querySelector('i');
        icon.classList.add('fa-spin');
        
        setTimeout(() => {
            window.location.reload();
        }, 500);
    }
</script>
@endsection
