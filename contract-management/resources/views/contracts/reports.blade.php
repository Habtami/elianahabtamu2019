@extends('layouts.app')

@section('title', 'Contract Analytics Dashboard')

@section('styles')
<style>
    .recent-activity-toggle { cursor: pointer; user-select: none; }
    .recent-activity-toggle .ra-chevron { transition: transform .2s ease; }
    .recent-activity-toggle[aria-expanded="true"] .ra-chevron { transform: rotate(180deg); }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <!-- Enhanced Header -->
                <div class="card-header bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h2 class="mb-1 font-weight-bold">Contract Analytics Dashboard</h2>
                            <p class="mb-0 opacity-75">Comprehensive insights and performance metrics for contract management</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="table-tip-btn" data-table-tip aria-expanded="false" aria-controls="tipContractReports"><i class="fas fa-info-circle"></i> TIP: info</button>
                            <button onclick="refreshData(event)" class="btn btn-light btn-sm">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                            <form action="{{ route('contracts.export') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="format" value="csv">
                                <input type="hidden" name="scope" value="all">
                                <button type="submit" class="btn" style="background-color: #1C75BC; border-color: #1C75BC; color: white;" onmouseover="this.style.backgroundColor='#0F4C75'" onmouseout="this.style.backgroundColor='#1C75BC'">
                                    <i class="fas fa-file-export me-1"></i>Export
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Date Range and Last Updated -->
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center">
                                @include('partials.type-of-report', ['reportTypeCurrent' => 'contract-analytics'])
                            </div>
                            <div class="d-flex align-items-center">
                                <label class="text-white-50 me-2 mb-0">Date Range:</label>
                                <select id="dateRange" onchange="changeDateRange(this.value)" class="form-select form-select-sm" style="width: auto;">
                                    <option value="6_months" {{ $dateRange == '6_months' ? 'selected' : '' }}>Last 6 months</option>
                                    <option value="12_months" {{ $dateRange == '12_months' ? 'selected' : '' }}>Last 12 months</option>
                                    <option value="year_to_date" {{ $dateRange == 'year_to_date' ? 'selected' : '' }}>Year to date</option>
                                </select>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="date" id="startDate" value="{{ $startDate->format('Y-m-d') }}" class="form-control form-control-sm" style="width: 130px;">
                                <span class="text-white-50">to</span>
                                <input type="date" id="endDate" value="{{ $endDate->format('Y-m-d') }}" class="form-control form-control-sm" style="width: 130px;">
                                <button onclick="applyCustomDateRange()" class="btn btn-outline-light btn-sm">Apply</button>
                            </div>
                            <div class="d-flex gap-1">
                                <button onclick="setDateRange('today')" class="btn btn-outline-light btn-sm">Today</button>
                                <button onclick="setDateRange('week')" class="btn btn-outline-light btn-sm">Week</button>
                                <button onclick="setDateRange('month')" class="btn btn-outline-light btn-sm">Month</button>
                                <button onclick="setDateRange('quarter')" class="btn btn-outline-light btn-sm">Quarter</button>
                            </div>
                        </div>
                        <div class="text-white-50 small">
                            <i class="fas fa-clock me-1"></i>Last updated: {{ $lastUpdated->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>

                <div id="tipContractReports" class="table-tip-panel no-print">
                    <h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4>
                    <ul>
                        <li>"Date Range" (Last 6 months, Last 12 months, Year to date) and the 6M / 12M / YTD buttons reload every panel for that period.</li>
                        <li>Type a start and end date and press "Apply", or use Today, Week, Month, Quarter to reload with that period.</li>
                        <li>The four cards (Total Contracts, Active Contracts, Expiring Soon, Expired Contracts) show counts and percentages; click one to list those contracts.</li>
                        <li>"Contract Creation Trend", "Contracts by Department" and "Contracts by Status" charts follow the selected period.</li>
                        <li>Expand "Recent Activity" to see Time, Contract, Action, User and Details for the latest contract updates.</li>
                        <li>"Refresh" reloads the page (it also refreshes every 5 minutes); "Export" downloads the dashboard data as CSV.</li>
                    </ul>
                </div>

                <div class="card-body">
                    <!-- Enhanced Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-left-primary shadow h-100 py-2" role="button" tabindex="0" style="cursor: pointer;"
                                 onclick="openStatModal('total-contracts', 'Total Contracts')"
                                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Contracts
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalContracts }}</div>
                                            <div class="text-xs text-muted">All time records</div>
                                            <div class="progress mt-2" style="height: 4px;">
                                                <div class="progress-bar bg-primary" style="width: 100%"></div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-file-contract fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card border-left-success shadow h-100 py-2" role="button" tabindex="0" style="cursor: pointer;"
                                 onclick="openStatModal('active-contracts', 'Active Contracts')"
                                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Active Contracts
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeContracts }}</div>
                                            <div class="text-xs text-muted">Currently active</div>
                                            <div class="progress mt-2" style="height: 4px;">
                                                <div class="progress-bar bg-success" style="width: {{ $activePercentage }}%"></div>
                                            </div>
                                            <div class="text-xs text-success mt-1">{{ $activePercentage }}%</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card border-left-warning shadow h-100 py-2" role="button" tabindex="0" style="cursor: pointer;"
                                 onclick="openStatModal('expiring-contracts', 'Expiring Soon')"
                                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Expiring Soon
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expiringContracts }}</div>
                                            <div class="text-xs text-muted">Within 30 days</div>
                                            <div class="progress mt-2" style="height: 4px;">
                                                <div class="progress-bar bg-warning" style="width: {{ $expiringPercentage }}%"></div>
                                            </div>
                                            <div class="text-xs text-warning mt-1">{{ $expiringPercentage }}%</div>
                                            @if($expiringContracts > 0)
                                                <div class="badge bg-warning text-dark mt-1">Needs attention</div>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card border-left-danger shadow h-100 py-2" role="button" tabindex="0" style="cursor: pointer;"
                                 onclick="openStatModal('expired-contracts', 'Expired Contracts')"
                                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Expired Contracts
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expiredContracts }}</div>
                                            <div class="text-xs text-muted">Requires renewal</div>
                                            <div class="progress mt-2" style="height: 4px;">
                                                <div class="progress-bar bg-danger" style="width: {{ $expiredPercentage }}%"></div>
                                            </div>
                                            <div class="text-xs text-danger mt-1">{{ $expiredPercentage }}%</div>
                                            @if($expiredContracts > 0)
                                                <div class="badge bg-danger mt-1">Requires renewal</div>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Contract Creation Trend -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card shadow">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-chart-line me-2"></i>Contract Creation Trend
                                    </h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small">Monthly contract creation pattern</span>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" onclick="changeTrendPeriod('6_months')" class="btn btn-outline-primary {{ $dateRange == '6_months' ? 'active' : '' }}">6M</button>
                                            <button type="button" onclick="changeTrendPeriod('12_months')" class="btn btn-outline-primary {{ $dateRange == '12_months' ? 'active' : '' }}">12M</button>
                                            <button type="button" onclick="changeTrendPeriod('year_to_date')" class="btn btn-outline-primary {{ $dateRange == 'year_to_date' ? 'active' : '' }}">YTD</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <!-- Contracts by Department -->
                        <div class="col-md-6">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Contracts by Department</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="departmentChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Contracts by Status -->
                        <div class="col-md-6">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Contracts by Status</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="statusChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow">
                                <div class="card-header py-3 d-flex align-items-center justify-content-between recent-activity-toggle"
                                     role="button" tabindex="0"
                                     aria-expanded="false" aria-controls="recentActivityBody"
                                     onclick="toggleRecentActivity(this)"
                                     onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                                    <div>
                                        <h6 class="m-0 font-weight-bold text-primary">
                                            <i class="fas fa-history me-2"></i>Recent Activity
                                        </h6>
                                        <p class="mb-0 text-muted small">Latest contract updates</p>
                                    </div>
                                    <i class="fas fa-chevron-down text-muted small ra-chevron"></i>
                                </div>
                                <div id="recentActivityBody" class="d-none">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover" width="100%" cellspacing="0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Time</th>
                                                    <th>Contract</th>
                                                    <th>Action</th>
                                                    <th>User</th>
                                                    <th>Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($recentActivity as $activity)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-light text-dark">
                                                            {{ $activity->created_at->diffForHumans() }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($activity->contract)
                                                            <a href="{{ route('contracts.show', $activity->contract) }}" class="text-decoration-none fw-bold">
                                                                {{ Str::limit($activity->contract->name, 30) }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted italic">Deleted Contract</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-{{ $activity->field_name == 'contract_end_date' ? 'calendar-plus' : 'shield-alt' }} me-1"></i>
                                                            {{ ucfirst(str_replace('_', ' ', $activity->field_name)) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-sm me-2">
                                                                <div class="avatar-initial rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px; line-height: 24px;">
                                                                    {{ strtoupper(substr($activity->edited_by ?? 'S', 0, 1)) }}
                                                                </div>
                                                            </div>
                                                            {{ $activity->edited_by ?? 'System' }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted small">{{ $activity->reason ?? 'N/A' }}</span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4">
                                                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                                        <div class="text-muted">No recent activity found</div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    // Initialize charts
    updateCharts();

    // Auto refresh every 5 minutes
    setInterval(refreshData, 300000);
});

function toggleRecentActivity(header) {
    const body = document.getElementById('recentActivityBody');
    if (!body) return;
    const open = body.classList.toggle('d-none') === false;
    header.setAttribute('aria-expanded', open ? 'true' : 'false');
}

function updateCharts() {
    // Department Chart
    const departmentCtx = document.getElementById('departmentChart').getContext('2d');
    new Chart(departmentCtx, {
        type: 'doughnut',
        data: {
            labels: @json($contractsByDepartment->pluck('department')),
            datasets: [{
                data: @json($contractsByDepartment->pluck('count')),
                backgroundColor: [
                    '#1C75BC',  // Brand blue for buttons
                    '#0F4C75',  // Darker blue
                    '#2563EB',  // Lighter blue
                    '#3B82F6',  // Even lighter blue
                    '#60A5FA'   // Lightest blue
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: @json($contractsByStatus->pluck('status')->map(fn($s) => ucfirst($s))),
            datasets: [{
                data: @json($contractsByStatus->pluck('count')),
                backgroundColor: [
                    '#10b981',  // Success green
                    '#f59e0b',  // Warning yellow
                    '#ef4444',  // Danger red
                    '#1C75BC'   // Brand blue for any additional status
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Monthly Trend Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json($contractsByMonth->pluck('month')),
            datasets: [{
                label: 'Contracts Created',
                data: @json($contractsByMonth->pluck('count')),
                borderColor: '#1C75BC',  // Brand blue for buttons
                backgroundColor: 'rgba(28, 117, 188, 0.1)',  // Light brand blue
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#1C75BC',  // Brand blue for buttons
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

function changeDateRange(range) {
    window.location.href = '{{ route("contracts.reports") }}?date_range=' + range;
}

function changeTrendPeriod(period) {
    changeDateRange(period);
}

function setDateRange(preset) {
    const today = new Date();
    let startDate, endDate;

    switch(preset) {
        case 'today':
            startDate = endDate = today;
            break;
        case 'week':
            startDate = new Date(today);
            startDate.setDate(today.getDate() - today.getDay());
            endDate = new Date(today);
            endDate.setDate(startDate.getDate() + 6);
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        case 'quarter':
            const quarterStart = Math.floor(today.getMonth() / 3) * 3;
            startDate = new Date(today.getFullYear(), quarterStart, 1);
            endDate = new Date(today.getFullYear(), quarterStart + 3, 0);
            break;
    }

    document.getElementById('startDate').value = startDate.toISOString().split('T')[0];
    document.getElementById('endDate').value = endDate.toISOString().split('T')[0];
}

function applyCustomDateRange() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    if (startDate && endDate) {
        // For custom date ranges, we'd need to modify the controller
        // For now, just update the URL with the dates
        window.location.href = '{{ route("contracts.reports") }}?start_date=' + startDate + '&end_date=' + endDate;
    }
}

function refreshData(e) {
    // Show loading state if triggered by a button click
    const refreshBtn = e ? e.currentTarget : null;
    if (refreshBtn && refreshBtn.tagName === 'BUTTON') {
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Refreshing...';
        refreshBtn.disabled = true;
    }

    // Reload the page to refresh data
    setTimeout(() => {
        window.location.reload();
    }, 500);
}
</script>
@endsection
