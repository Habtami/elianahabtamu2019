@extends('layouts.app')

@section('title', 'System Activity Logs')

@section('content')
<div class="container-fluid py-4">
    <!-- Range-aware KPI Summary Section -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 small mb-1">Total Activities ({{ ucfirst($stats['current_range'] == 'all' ? 'day' : $stats['current_range']) }})</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 p-2 rounded">
                            <i class="fas fa-list-ul fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 small mb-1">Total Logins ({{ ucfirst($stats['current_range'] == 'all' ? 'day' : $stats['current_range']) }})</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['logins'] }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 p-2 rounded">
                            <i class="fas fa-user-check fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-dark-50 small mb-1">Contract Updates ({{ ucfirst($stats['current_range'] == 'all' ? 'day' : $stats['current_range']) }})</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['updates'] }}</h3>
                        </div>
                        <div class="bg-dark bg-opacity-10 p-2 rounded">
                            <i class="fas fa-edit fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm {{ $stats['errors'] > 0 ? 'bg-danger' : 'bg-secondary' }} text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 small mb-1">System Errors ({{ ucfirst($stats['current_range'] == 'all' ? 'day' : $stats['current_range']) }})</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['errors'] }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 p-2 rounded">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold">
                        <i class="fas fa-history me-2"></i>Audit Trail & Activity Logs
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" onclick="exportLogs()" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-export me-1"></i> Export Report
                        </button>
                        <div class="text-muted small align-self-center ms-2">
                            Monitoring Rule: <span class="badge bg-light text-dark fw-normal border">Professional Audit Standards</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Enhanced Filters -->
                    <form id="filterForm" action="{{ route('admin.activity-logs') }}" method="GET" class="row g-2 mb-4">
                        <div class="col-md-2">
                            <label class="small text-muted fw-bold">Time Range</label>
                            <select name="range" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                                <option value="all" {{ request('range') == 'all' ? 'selected' : '' }}>All Time (Default)</option>
                                <option value="today" {{ request('range') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="yesterday" {{ request('range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                <option value="week" {{ request('range') == 'week' ? 'selected' : '' }}>Last 7 Days</option>
                                <option value="month" {{ request('range') == 'month' ? 'selected' : '' }}>Last 30 Days</option>
                                <option value="year" {{ request('range') == 'year' ? 'selected' : '' }}>Last Year</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted fw-bold">Category</label>
                            <select name="type" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <option value="login" {{ request('type') == 'login' ? 'selected' : '' }}>Successful Logins</option>
                                <option value="failed_login" {{ request('type') == 'failed_login' ? 'selected' : '' }}>Failed Logins</option>
                                <option value="logout" {{ request('type') == 'logout' ? 'selected' : '' }}>Logouts</option>
                                <option value="contract_create" {{ request('type') == 'contract_create' ? 'selected' : '' }}>Insertions</option>
                                <option value="contract_update" {{ request('type') == 'contract_update' ? 'selected' : '' }}>Updates</option>
                                <option value="contract_delete" {{ request('type') == 'contract_delete' ? 'selected' : '' }}>Deletions</option>
                                <option value="system_error" {{ request('type') == 'system_error' ? 'selected' : '' }}>System Errors</option>
                                <option value="admin_action" {{ request('type') == 'admin_action' ? 'selected' : '' }}>Admin Actions</option>
                                <option value="password_reset_request" {{ request('type') == 'password_reset_request' ? 'selected' : '' }}>Password Resets</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted fw-bold">Severity</label>
                            <select name="severity" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                                <option value="">All Severities</option>
                                <option value="info" {{ request('severity') == 'info' ? 'selected' : '' }}>Info Only</option>
                                <option value="warning" {{ request('severity') == 'warning' ? 'selected' : '' }}>Warnings Only</option>
                                <option value="danger" {{ request('severity') == 'danger' ? 'selected' : '' }}>Critical Only</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold">Search</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Search actor, IP, reason..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Apply Filters</button>
                            <a href="{{ route('admin.activity-logs') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">Reset All</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 px-3">Timestamp</th>
                                    <th class="border-0">Severity</th>
                                    <th class="border-0">Activity Category</th>
                                    <th class="border-0">Actor Details</th>
                                    <th class="border-0">Action Performed</th>
                                    <th class="border-0 text-end px-3">Trace Info</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td class="px-3">
                                        <div class="fw-bold text-nowrap">{{ $log->created_at->format('d M Y') }}</div>
                                        <div class="text-muted small">{{ $log->created_at->format('H:i:s') }}</div>
                                    </td>
                                    <td>
                                        @if($log->severity === 'danger')
                                            <span class="badge bg-danger shadow-sm">Critical</span>
                                        @elseif($log->severity === 'warning')
                                            <span class="badge bg-warning text-dark shadow-sm">Warning</span>
                                        @else
                                            <span class="badge bg-info text-white shadow-sm">Info</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-uppercase small fw-bold text-muted border-bottom">{{ str_replace('_', ' ', $log->activity_type) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-2">
                                                <i class="fas fa-user-shield text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $log->edited_by }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $log->user_id ? 'Authenticated User' : 'System Account' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="mb-1 fw-medium text-dark">{{ $log->description }}</div>
                                        @if($log->reason)
                                            <div class="small p-2 bg-light rounded border-start border-3 border-primary">
                                                <i class="fas fa-comment-alt me-1 text-muted small"></i>
                                                <span class="fst-italic text-muted">"{{ $log->reason }}"</span>
                                            </div>
                                        @endif
                                        @if($log->activity_type === 'contract_update' && $log->field_name !== 'contract')
                                            <div class="mt-1 small d-flex flex-wrap gap-2">
                                                <span class="text-danger bg-danger bg-opacity-10 px-2 rounded"><i class="fas fa-minus-circle me-1"></i>{{ Str::limit($log->getFormattedOldValue(), 30) }}</span>
                                                <i class="fas fa-long-arrow-alt-right text-muted"></i>
                                                <span class="text-success bg-success bg-opacity-10 px-2 rounded"><i class="fas fa-plus-circle me-1"></i>{{ Str::limit($log->getFormattedNewValue(), 30) }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end px-3">
                                        <div class="fw-bold small text-dark"><i class="fas fa-network-wired me-1"></i>{{ $log->ip_address ?? '127.0.0.1' }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;" title="{{ $log->user_agent }}">
                                            <i class="fas fa-laptop me-1"></i>{{ Str::limit($log->user_agent, 20) }}
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="opacity-50">
                                            <i class="fas fa-history fa-3x mb-3 text-muted"></i>
                                            <p class="h5">No Activity Records</p>
                                            <p class="text-muted">Adjust your filters to see historical audit data.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Enhanced Pagination -->
                    <div class="mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <div class="text-muted small order-2 order-md-1">
                            Showing <strong>{{ $logs->firstItem() ?? 0 }}</strong> to <strong>{{ $logs->lastItem() ?? 0 }}</strong> of <strong>{{ $logs->total() }}</strong> activities
                        </div>
                        <div class="order-1 order-md-2">
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function exportLogs() {
        const form = document.getElementById('filterForm');
        const url = new URL(form.action);
        const params = new FormData(form);
        
        // Add export parameter
        url.searchParams.append('export', '1');
        for (const [key, value] of params.entries()) {
            if(value) url.searchParams.append(key, value);
        }
        
        window.location.href = url.toString();
    }
</script>

<style>
    .card { transition: transform 0.2s; border-radius: 0.75rem; }
    .table thead th { font-weight: 600; background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; }
    .badge { font-weight: 600; padding: 0.45em 0.85em; border-radius: 0.5rem; }
    .bg-info { background-color: #3B82F6 !important; }
    .bg-warning { background-color: #F59E0B !important; }
    .bg-danger { background-color: #EF4444 !important; }
    .table-hover tbody tr:hover { background-color: #f1f5f9; }
</style>
@endsection
