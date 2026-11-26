@extends('layouts.app')

@section('title', 'System Activity Logs')
@section('page-title', 'Activity Logs')

@section('styles')
<style>
    .al-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .al-stat {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
        width: 100%;
        text-align: left;
        font: inherit;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-left: 4px solid var(--card-color, #0d9488);
        border-radius: 12px;
        padding: 1.1rem 1.2rem;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .al-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
        border-left-color: var(--card-color, #0d9488);
    }
    .al-stat:focus-visible { outline: 2px solid var(--card-color, #0d9488); outline-offset: 2px; }
    .al-stat-main { min-width: 0; }
    .al-stat-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
    }
    .al-stat-value {
        display: block;
        font-size: 1.7rem;
        font-weight: 700;
        color: #0f172a;
        margin-top: 0.3rem;
        line-height: 1;
    }
    .al-stat-hint {
        display: block;
        margin-top: 0.55rem;
        font-size: 0.68rem;
        font-weight: 600;
        color: #8aa2b8;
        opacity: 0;
        transition: opacity 0.15s ease, color 0.15s ease;
    }
    .al-stat:hover .al-stat-hint,
    .al-stat:focus-visible .al-stat-hint { opacity: 1; color: #0f766e; }
    .al-stat-icon {
        flex: none;
        padding-top: 0.1rem;
        font-size: 1.9rem;
        line-height: 1;
        color: var(--card-color, #0d9488);
        opacity: 0.22;
    }

    .al-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.65rem;
        font-size: 0.72rem;
        font-weight: 600;
        border-radius: 7px;
        border: 1px solid #dbe6f1;
        background: #fff;
        color: #33506e;
        text-decoration: none;
        cursor: pointer;
    }
    .al-action-btn:hover { background: #f1f5f9; }
    .al-actions { display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap; }

    .al-perpage { display: flex; align-items: center; gap: 0.35rem; }
    .al-perpage label { font-size: 0.72rem; font-weight: 600; color: #6b8299; margin: 0; }
    .al-perpage select {
        padding: 0.35rem 0.5rem;
        border: 1px solid #cfdcea;
        border-radius: 7px;
        font-size: 0.75rem;
        background: #fff;
        color: #1e293b;
    }

    .al-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.6rem;
        padding: 0.85rem 1.5rem;
        border-top: 1px solid #edf2f8;
        font-size: 0.78rem;
        color: #6b8299;
    }
    .al-pagination strong { color: #1e293b; font-weight: 600; }
    .al-pages { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; margin-left: auto; }
    .al-page-btn {
        display: inline-block;
        padding: 0.35rem 0.7rem;
        border: 1px solid #dbe6f1;
        background: #fff;
        border-radius: 6px;
        color: #33506e;
        cursor: pointer;
        font-weight: 500;
        font-size: 0.8rem;
        font-family: inherit;
        text-decoration: none;
        text-align: center;
    }
    .al-page-btn:hover:not(:disabled) { background: #f1f5f9; }
    .al-page-btn.al-active { background: #0d9488; border-color: #0d9488; color: #fff; }
    .al-page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .al-page-dots { padding: 0.35rem 0.3rem; color: #6b8299; }

    .al-modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(10, 25, 45, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1700;
        padding: 1rem;
    }
    .al-modal-bg.open { display: flex; }
    .al-modal {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 1120px;
        max-height: 88vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 24px 60px rgba(8, 25, 45, 0.35);
        overflow: hidden;
    }
    .al-modal.al-modal-sm { max-width: 480px; }
    .al-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.3rem;
        border-bottom: 1px solid #edf2f8;
    }
    .al-modal-head h3 { margin: 0; font-size: 1.05rem; color: #0f2a44; }
    .al-modal-head span { font-size: 0.75rem; color: #6b8299; }
    .al-modal-close {
        border: none;
        background: #eef2f7;
        color: #33506e;
        width: 32px;
        height: 32px;
        border-radius: 9px;
        font-size: 1.1rem;
        line-height: 1;
        cursor: pointer;
        flex-shrink: 0;
    }
    .al-modal-close:hover { background: #dbe6f1; }
    .al-modal-body { padding: 1rem 1.3rem 1.4rem; overflow-y: auto; }
    .al-modal-foot {
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
        padding: 0.7rem 1.3rem;
        border-top: 1px solid #edf2f8;
        background: #f8fafc;
        font-size: 0.82rem;
        color: #6b8299;
    }
    .al-modal-foot strong { color: #1e293b; font-weight: 600; }
    .al-modal-pages { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; }

    .al-table-wrap { overflow-x: auto; }
    .al-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    .al-table th {
        background: #f3f7fc;
        color: #345572;
        text-align: left;
        padding: 0.65rem 0.6rem;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        border-bottom: 2px solid #d7e2ee;
        white-space: nowrap;
    }
    .al-table td {
        padding: 0.7rem 0.6rem;
        border-bottom: 1px solid #edf2f8;
        color: #1e293b;
        vertical-align: top;
    }
    .al-table tbody tr:hover td { background: #f8fbff; }
    .al-table th.al-roll,
    .al-table td.al-roll {
        width: 3.2rem;
        color: #7a90a6;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .al-empty { text-align: center; color: #8ca3b8; padding: 2rem 1rem; font-style: italic; }

    .al-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.68rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .al-badge.red { background: #fee2e2; color: #991b1b; }
    .al-badge.amber { background: #fef3c7; color: #92400e; }
    .al-badge.blue { background: #dbeafe; color: #1e40af; }
    .al-cat {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #52657a;
    }
    .al-modal-sub { font-size: 0.72rem; color: #7a90a6; }
    .al-reason {
        margin-top: 0.25rem;
        padding: 0.3rem 0.55rem;
        background: #f8fafc;
        border-left: 3px solid #3b82f6;
        border-radius: 4px;
        font-size: 0.74rem;
        font-style: italic;
        color: #52657a;
    }
    .al-diff { margin-top: 0.3rem; display: flex; flex-wrap: wrap; gap: 0.35rem; align-items: center; font-size: 0.72rem; }
    .al-diff .old { color: #b91c1c; background: #fee2e2; padding: 0 0.35rem; border-radius: 4px; }
    .al-diff .new { color: #166534; background: #dcfce7; padding: 0 0.35rem; border-radius: 4px; }
    .al-diff .arrow { color: #94a3b8; }
    .al-trace { font-size: 0.72rem; color: #52657a; }
    .al-trace strong { color: #1e293b; font-weight: 600; }

    .al-print-options { display: flex; flex-direction: column; gap: 0.55rem; }
    .al-print-options label {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        font-size: 0.88rem;
        color: #1e293b;
        cursor: pointer;
    }
    .al-print-options input { width: 16px; height: 16px; accent-color: #0d9488; }
    .al-print-hint { margin: 0.7rem 0 0; font-size: 0.78rem; font-weight: 600; color: #b91c1c; }

    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        .sidebar, .sidebar-toggle, .topbar, .page-footer, .no-print,
        .table-tip-btn, .table-tip-panel, .al-modal-bg { display: none !important; }
        .print-hide-section { display: none !important; }
        body { background: #fff; }
        .main-content, .main-content.expanded { margin-left: 0 !important; }
        .al-page { max-width: none !important; margin: 0 !important; padding: 0 !important; }
        .al-stat { box-shadow: none; cursor: default; }
        .al-table-card { box-shadow: none; border: 1px solid #c9d7e6; }
        .al-table-scroll { overflow: visible !important; }
        .al-main-table thead { display: table-header-group; }
        .al-main-table th, .al-main-table td { border: 1px solid #9aa9b8; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endsection

@section('content')
@php
    $statCards = [
        ['key' => 'total', 'label' => 'Total Activities', 'color' => '#0d9488', 'icon' => 'fa-list-ul', 'id' => 'alModalTotal', 'hint' => 'Click to view all activities →', 'view' => 'View all activity records'],
        ['key' => 'logins', 'label' => 'Successful Logins', 'color' => '#10b981', 'icon' => 'fa-user-check', 'id' => 'alModalLogins', 'hint' => 'Click to view all successful logins →', 'view' => 'View successful login records'],
        ['key' => 'updates', 'label' => 'Contract Updates', 'color' => '#f59e0b', 'icon' => 'fa-edit', 'id' => 'alModalUpdates', 'hint' => 'Click to view all contract updates →', 'view' => 'View contract update records'],
        ['key' => 'critical', 'label' => 'Critical Events', 'color' => '#ef4444', 'icon' => 'fa-exclamation-triangle', 'id' => 'alModalCritical', 'hint' => 'Click to view all critical events →', 'view' => 'View critical severity records'],
    ];
@endphp

<div class="al-page container mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-clock-rotate-left text-[#0d9488] mr-3"></i>
                System Activity Logs
            </h1>
            <p class="text-gray-600 mt-2">Audit trail of logins, record changes and administrative actions across the system.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 no-print">
            <button type="button" data-al-print-options data-print-default="table"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <a href="{{ route('admin.activity-logs', array_merge(request()->except(['page', 'per_page']), ['export' => 1])) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-export mr-2"></i>Export Report
            </a>
        </div>
    </div>

    <!-- Summary cards -->
    <div class="al-stats" data-print-section="cards">
        @foreach($statCards as $card)
            @php $groupLogs = $statGroups[$card['key']]; @endphp
            <button type="button" class="al-stat" style="--card-color: {{ $card['color'] }};"
                    data-al-modal="{{ $card['id'] }}" aria-haspopup="dialog"
                    aria-label="{{ $card['view'] }}" title="{{ $card['view'] }}">
                <span class="al-stat-main">
                    <span class="al-stat-label">{{ $card['label'] }}</span>
                    <span class="al-stat-value">{{ $groupLogs->count() }}</span>
                    <span class="al-stat-hint">{{ $card['hint'] }}</span>
                </span>
                <span class="al-stat-icon" aria-hidden="true"><i class="fas {{ $card['icon'] }}"></i></span>
            </button>
        @endforeach
    </div>

    <!-- Table -->
    @include('partials.print-letterhead', ['reportMeta' => $reportMeta, 'letterheadTitle' => 'Activity Logs Register'])
    <div class="al-table-card bg-white rounded-lg shadow-sm border border-gray-200" data-print-section="table">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 px-6 py-4 border-b border-gray-200">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-history mr-2 text-[#0d9488]"></i>Audit Trail &amp; Activity Logs
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    @if(request('search'))
                        {{ $logs->total() }} result(s) for &ldquo;{{ request('search') }}&rdquo;
                    @else
                        {{ $logs->total() }} record(s)
                    @endif
                    &middot; page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}
                </p>
            </div>
            <div class="al-actions no-print">
                <button type="button" class="table-tip-btn" data-table-tip aria-expanded="false" aria-controls="tipActivityLogs">
                    <i class="fas fa-info-circle"></i> TIP: info
                </button>
                <button type="button" class="al-action-btn" data-al-print-options data-print-default="table" title="Print this table only">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>

        <div class="px-6 py-3 border-b border-gray-100 no-print">
            <form method="GET" action="{{ route('admin.activity-logs') }}" class="flex flex-wrap items-end gap-2">
                <input type="hidden" name="per_page" value="{{ $logs->perPage() }}">
                <div class="flex items-center gap-1">
                    <span class="text-xs font-semibold text-gray-500">Range</span>
                    <select name="range" aria-label="Time range"
                            class="rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d9488]">
                        <option value="all" @selected(request('range', 'all') === 'all')>All Time</option>
                        <option value="today" @selected(request('range') === 'today')>Today</option>
                        <option value="yesterday" @selected(request('range') === 'yesterday')>Yesterday</option>
                        <option value="week" @selected(request('range') === 'week')>Last 7 Days</option>
                        <option value="month" @selected(request('range') === 'month')>Last 30 Days</option>
                        <option value="year" @selected(request('range') === 'year')>Last Year</option>
                    </select>
                </div>
                <select name="type" aria-label="Category"
                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d9488]">
                    <option value="">All Categories</option>
                    <option value="login" @selected(request('type') === 'login')>Successful Logins</option>
                    <option value="failed_login" @selected(request('type') === 'failed_login')>Failed Logins</option>
                    <option value="logout" @selected(request('type') === 'logout')>Logouts</option>
                    <option value="contract_create" @selected(request('type') === 'contract_create')>Insertions</option>
                    <option value="contract_update" @selected(request('type') === 'contract_update')>Updates</option>
                    <option value="contract_delete" @selected(request('type') === 'contract_delete')>Deletions</option>
                    <option value="system_error" @selected(request('type') === 'system_error')>System Errors</option>
                    <option value="admin_action" @selected(request('type') === 'admin_action')>Admin Actions</option>
                    <option value="password_reset_request" @selected(request('type') === 'password_reset_request')>Password Resets</option>
                </select>
                <select name="severity" aria-label="Severity"
                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d9488]">
                    <option value="">All Severities</option>
                    <option value="info" @selected(request('severity') === 'info')>Info Only</option>
                    <option value="warning" @selected(request('severity') === 'warning')>Warnings Only</option>
                    <option value="danger" @selected(request('severity') === 'danger')>Critical Only</option>
                </select>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" autocomplete="off"
                           aria-label="Search"
                           placeholder="Search actor, IP, reason..."
                           class="w-full sm:w-56 rounded-lg border border-gray-300 pl-9 pr-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d9488]">
                </div>
                <button type="submit"
                        class="px-3 py-1.5 bg-[#0d9488] text-white text-sm font-medium rounded-lg hover:bg-[#0f766e] transition">
                    <i class="fas fa-filter mr-1"></i>Filter
                </button>
                <a href="{{ route('admin.activity-logs') }}"
                   class="px-3 py-1.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-rotate-left mr-1"></i>Reset
                </a>
            </form>
            <form method="GET" action="{{ route('admin.activity-logs') }}" class="al-perpage mt-2">
                @foreach(request()->except(['per_page', 'page']) as $key => $value)
                    @if(is_scalar($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <label for="alPerPage">Rows</label>
                <select name="per_page" id="alPerPage" onchange="this.form.submit()">
                    @foreach([5, 10, 25, 50] as $size)
                        <option value="{{ $size }}" @selected($logs->perPage() === $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div id="tipActivityLogs" class="table-tip-panel no-print">
            <h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4>
            <ul>
                <li><strong>Range</strong>, <strong>Category</strong>, <strong>Severity</strong> and <strong>Search</strong> narrow the log, then press <strong>Filter</strong>. Search matches actor, reason, field and IP address.</li>
                <li>Each row shows the timestamp, severity badge, activity category, actor, action performed (with reason and old → new values) and trace info (IP and device).</li>
                <li>The KPI cards open the full list of that group regardless of the search, filters or page size; every card count matches the rows in its modal, and each modal has its own pager.</li>
                <li>Severity badges: <strong>Critical</strong> is red, <strong>Warning</strong> is amber and <strong>Info</strong> is blue.</li>
                <li><strong>Rows</strong> changes the page size, and numbered pages sit below the table.</li>
                <li><strong>Export Report</strong> downloads the filtered log as a CSV with the letterhead; <strong>Print</strong> offers the summary cards or the table.</li>
            </ul>
        </div>

        <div class="al-table-scroll overflow-x-auto">
            <table class="al-main-table min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-4 py-3 font-medium al-roll">No</th>
                        <th class="px-6 py-3 font-medium">Timestamp</th>
                        <th class="px-6 py-3 font-medium">Severity</th>
                        <th class="px-6 py-3 font-medium">Activity Category</th>
                        <th class="px-6 py-3 font-medium">Actor Details</th>
                        <th class="px-6 py-3 font-medium">Action Performed</th>
                        <th class="px-6 py-3 font-medium">Trace Info</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-4 py-4 text-gray-400 al-roll">{{ ($logs->firstItem() ?? 1) + $loop->index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-gray-800">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->severity === 'danger')
                                <span class="al-badge red"><i class="fas fa-triangle-exclamation"></i>Critical</span>
                            @elseif($log->severity === 'warning')
                                <span class="al-badge amber"><i class="fas fa-circle-exclamation"></i>Warning</span>
                            @else
                                <span class="al-badge blue"><i class="fas fa-circle-info"></i>Info</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="al-cat">{{ str_replace('_', ' ', $log->activity_type) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="bg-gray-100 rounded-full p-2 text-[#0d9488]"><i class="fas fa-user-shield"></i></span>
                                <span>
                                    <span class="block font-semibold text-gray-800">{{ $log->edited_by }}</span>
                                    <span class="block text-xs text-gray-500">{{ $log->user_id ? 'Authenticated User' : 'System Account' }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-800">{{ $log->description }}</div>
                            @if($log->reason)
                                <div class="al-reason"><i class="fas fa-comment-alt mr-1"></i>&ldquo;{{ $log->reason }}&rdquo;</div>
                            @endif
                            @if(in_array($log->activity_type, ['contract_update', 'driver_update', 'driver_status']) && $log->field_name !== 'contract')
                                <div class="al-diff">
                                    <span class="old"><i class="fas fa-minus-circle mr-1"></i>{{ Str::limit($log->getFormattedOldValue(), 30) }}</span>
                                    <span class="arrow"><i class="fas fa-long-arrow-alt-right"></i></span>
                                    <span class="new"><i class="fas fa-plus-circle mr-1"></i>{{ Str::limit($log->getFormattedNewValue(), 30) }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="al-trace"><strong><i class="fas fa-network-wired mr-1"></i>{{ $log->ip_address ?? '127.0.0.1' }}</strong></div>
                            <div class="al-trace" title="{{ $log->user_agent }}">
                                <i class="fas fa-laptop mr-1"></i>{{ Str::limit($log->user_agent, 28) }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <i class="fas fa-history text-4xl text-gray-300"></i>
                            @if(in_array(request('range'), ['today', 'yesterday', 'week', 'month', 'year'], true) || request()->filled('type') || request()->filled('severity') || request()->filled('search'))
                                <p class="mt-4 text-gray-700 font-medium">No activity records match the current filters</p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Try adjusting the range, category, severity or search, or
                                    <a href="{{ route('admin.activity-logs') }}" class="text-[#0d9488] hover:underline">reset the filters</a>.
                                </p>
                            @else
                                <p class="mt-4 text-gray-700 font-medium">No activity records yet</p>
                                <p class="mt-1 text-sm text-gray-500">Audit entries are generated automatically as the system is used.</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->total() > 0)
            @php
                $alCurrent = $logs->currentPage();
                $alLast = $logs->lastPage();
                $alPages = [];
                for ($alPage = 1; $alPage <= $alLast; $alPage++) {
                    if ($alPage === 1 || $alPage === $alLast || abs($alPage - $alCurrent) <= 1) {
                        $alPages[] = $alPage;
                    } elseif (end($alPages) !== '...') {
                        $alPages[] = '...';
                    }
                }
            @endphp
            <div class="al-pagination no-print">
                <span>
                    Showing <strong>{{ $logs->firstItem() }}–{{ $logs->lastItem() }}</strong>
                    of <strong>{{ $logs->total() }}</strong>
                </span>
                <div class="al-pages">
                    @if($alCurrent > 1)
                        <a class="al-page-btn" href="{{ $logs->url($alCurrent - 1) }}" rel="prev">« Prev</a>
                    @else
                        <button type="button" class="al-page-btn" disabled>« Prev</button>
                    @endif
                    @foreach($alPages as $alPage)
                        @if($alPage === '...')
                            <span class="al-page-dots">...</span>
                        @else
                            <a class="al-page-btn {{ $alPage === $alCurrent ? 'al-active' : '' }}" href="{{ $logs->url($alPage) }}" @if($alPage === $alCurrent) aria-current="page" @endif>{{ $alPage }}</a>
                        @endif
                    @endforeach
                    @if($alCurrent < $alLast)
                        <a class="al-page-btn" href="{{ $logs->url($alCurrent + 1) }}" rel="next">Next »</a>
                    @else
                        <button type="button" class="al-page-btn" disabled>Next »</button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <p class="text-xs text-gray-500 mt-4">
        <i class="fas fa-info-circle mr-1"></i>
        Audit entries are generated automatically by the system and are read-only.
    </p>
</div>

@foreach($statCards as $card)
    @php $groupLogs = $statGroups[$card['key']]; @endphp
    <div class="al-modal-bg" id="{{ $card['id'] }}" role="dialog" aria-modal="true" aria-labelledby="{{ $card['id'] }}Title">
        <div class="al-modal">
            <div class="al-modal-head">
                <div>
                    <h3 id="{{ $card['id'] }}Title"><i class="fas {{ $card['icon'] }} mr-2" style="color: {{ $card['color'] }};"></i>{{ $card['label'] }}</h3>
                    <span>{{ $groupLogs->count() }} record(s)</span>
                </div>
                <button type="button" class="al-modal-close" aria-label="Close" data-al-close>×</button>
            </div>
            <div class="al-modal-body">
                @if($groupLogs->isEmpty())
                    <div class="al-empty">No activity records in this group.</div>
                @else
                    <div class="al-table-wrap">
                        <table class="al-table">
                            <thead>
                                <tr>
                                    <th class="al-roll">No</th>
                                    <th>Timestamp</th>
                                    <th>Severity</th>
                                    <th>Category</th>
                                    <th>Actor</th>
                                    <th>Action Performed</th>
                                    <th>Trace Info</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupLogs as $log)
                                    <tr>
                                        <td class="al-roll">{{ $loop->iteration }}</td>
                                        <td class="whitespace-nowrap">
                                            <div class="font-semibold">{{ $log->created_at->format('d M Y') }}</div>
                                            <div class="al-modal-sub">{{ $log->created_at->format('H:i:s') }}</div>
                                        </td>
                                        <td>
                                            @if($log->severity === 'danger')
                                                <span class="al-badge red"><i class="fas fa-triangle-exclamation"></i>Critical</span>
                                            @elseif($log->severity === 'warning')
                                                <span class="al-badge amber"><i class="fas fa-circle-exclamation"></i>Warning</span>
                                            @else
                                                <span class="al-badge blue"><i class="fas fa-circle-info"></i>Info</span>
                                            @endif
                                        </td>
                                        <td><span class="al-cat">{{ str_replace('_', ' ', $log->activity_type) }}</span></td>
                                        <td>
                                            <div class="font-semibold">{{ $log->edited_by }}</div>
                                            <div class="al-modal-sub">{{ $log->user_id ? 'Authenticated User' : 'System Account' }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $log->description }}</div>
                                            @if($log->reason)
                                                <div class="al-reason"><i class="fas fa-comment-alt mr-1"></i>&ldquo;{{ $log->reason }}&rdquo;</div>
                                            @endif
                                            @if(in_array($log->activity_type, ['contract_update', 'driver_update', 'driver_status']) && $log->field_name !== 'contract')
                                                <div class="al-diff">
                                                    <span class="old"><i class="fas fa-minus-circle mr-1"></i>{{ Str::limit($log->getFormattedOldValue(), 30) }}</span>
                                                    <span class="arrow"><i class="fas fa-long-arrow-alt-right"></i></span>
                                                    <span class="new"><i class="fas fa-plus-circle mr-1"></i>{{ Str::limit($log->getFormattedNewValue(), 30) }}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="al-trace"><strong><i class="fas fa-network-wired mr-1"></i>{{ $log->ip_address ?? '127.0.0.1' }}</strong></div>
                                            <div class="al-trace" title="{{ $log->user_agent }}">
                                                <i class="fas fa-laptop mr-1"></i>{{ Str::limit($log->user_agent, 24) }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="al-modal-foot">
                <span class="al-modal-range"></span>
                <div class="al-modal-pages"></div>
            </div>
        </div>
    </div>
@endforeach

<div class="al-modal-bg" id="alModalPrint" role="dialog" aria-modal="true" aria-labelledby="alPrintTitle">
    <div class="al-modal al-modal-sm">
        <div class="al-modal-head">
            <div>
                <h3 id="alPrintTitle">Print Activity Logs</h3>
                <span>Choose which sections appear on the printed page</span>
            </div>
            <button type="button" class="al-modal-close" aria-label="Close" data-al-close>×</button>
        </div>
        <div class="al-modal-body">
            <div class="al-print-options">
                <label><input type="checkbox" class="al-print-check" value="cards"> Summary cards</label>
                <label><input type="checkbox" class="al-print-check" value="table" checked> Activity logs table</label>
            </div>
            <p id="alPrintHint" class="al-print-hint" style="display:none;">Select at least one section to print.</p>
        </div>
        <div class="al-modal-foot" style="display:flex;">
            <span></span>
            <div class="al-modal-pages">
                <button type="button" class="al-action-btn" data-al-close>Cancel</button>
                <button type="button" class="inline-flex items-center px-3 py-1.5 bg-[#0d9488] text-white text-xs font-semibold rounded-lg hover:bg-[#0f766e]" id="alPrintGo">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        'use strict';

        var MODAL_PAGE_SIZE = 10;

        function openModalEl(modal) {
            if (!modal) return;
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modal) {
            if (!modal) return;
            modal.classList.remove('open');
            if (!document.querySelector('.al-modal-bg.open')) {
                document.body.style.overflow = '';
            }
        }

        function pageWindow(current, last) {
            var pages = [];
            for (var page = 1; page <= last; page++) {
                if (page === 1 || page === last || Math.abs(page - current) <= 1) {
                    pages.push(page);
                } else if (pages[pages.length - 1] !== '...') {
                    pages.push('...');
                }
            }
            return pages;
        }

        function initModalPager(modal) {
            if (!modal) return;
            var table = modal.querySelector('.al-modal-body .al-table');
            if (!table) return;

            var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
            if (!rows.length) return;

            var foot = modal.querySelector('.al-modal-foot');
            var rangeNode = foot && foot.querySelector('.al-modal-range');
            var pagesNode = foot && foot.querySelector('.al-modal-pages');
            if (!foot || !rangeNode || !pagesNode) return;

            var state = modal.alPager;
            if (!state) {
                state = modal.alPager = { rows: rows, page: 1, foot: foot, range: rangeNode, pages: pagesNode };
            }
            state.page = 1;
            paintModalPage(state, false);
        }

        function paintModalPage(state, refocus) {
            var rows = state.rows;
            var total = rows.length;
            var lastPage = Math.max(1, Math.ceil(total / MODAL_PAGE_SIZE));
            state.page = Math.min(Math.max(state.page, 1), lastPage);

            var start = (state.page - 1) * MODAL_PAGE_SIZE;
            var end = Math.min(start + MODAL_PAGE_SIZE, total);

            rows.forEach(function (row, index) {
                row.hidden = index < start || index >= end;
            });

            state.foot.style.display = 'flex';
            state.range.innerHTML = 'Showing <strong>' + (start + 1) + '–' + end + '</strong> of <strong>' + total + '</strong>';

            state.pages.textContent = '';

            state.pages.appendChild(pagerButton('« Prev', state.page <= 1, false, function () {
                goToModalPage(state, state.page - 1);
            }));

            pageWindow(state.page, lastPage).forEach(function (item) {
                if (item === '...') {
                    var dots = document.createElement('span');
                    dots.className = 'al-page-dots';
                    dots.textContent = '...';
                    state.pages.appendChild(dots);
                    return;
                }

                var button = pagerButton(String(item), false, item === state.page, function () {
                    goToModalPage(state, item);
                });
                if (item === state.page) button.setAttribute('aria-current', 'page');
                state.pages.appendChild(button);
            });

            state.pages.appendChild(pagerButton('Next »', state.page >= lastPage, false, function () {
                goToModalPage(state, state.page + 1);
            }));

            if (refocus) {
                var active = state.pages.querySelector('.al-active');
                if (active) active.focus();
            }
        }

        function pagerButton(label, disabled, active, onClick) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'al-page-btn' + (active ? ' al-active' : '');
            button.textContent = label;
            button.disabled = disabled;
            button.addEventListener('click', onClick);
            return button;
        }

        function goToModalPage(state, page) {
            state.page = page;
            paintModalPage(state, true);
            var body = state.foot.parentNode.querySelector('.al-modal-body');
            if (body) body.scrollTop = 0;
        }

        var printModal = document.getElementById('alModalPrint');
        var printChecks = Array.prototype.slice.call(document.querySelectorAll('.al-print-check'));
        var printHint = document.getElementById('alPrintHint');

        function openPrintOptions(defaultSection) {
            var wanted = defaultSection || 'table';
            printChecks.forEach(function (check) {
                check.checked = check.value === wanted;
            });
            if (printHint) printHint.style.display = 'none';
            openModalEl(printModal);
        }

        function printSelectedSections() {
            var selected = printChecks
                .filter(function (check) { return check.checked; })
                .map(function (check) { return check.value; });

            if (!selected.length) {
                if (printHint) printHint.style.display = 'block';
                return;
            }

            var sections = document.querySelectorAll('[data-print-section]');

            function restoreSections() {
                Array.prototype.forEach.call(sections, function (section) {
                    section.classList.remove('print-hide-section');
                });
                window.removeEventListener('afterprint', restoreSections);
            }

            Array.prototype.forEach.call(sections, function (section) {
                var key = section.getAttribute('data-print-section');
                section.classList.toggle('print-hide-section', selected.indexOf(key) === -1);
            });

            closeModal(printModal);
            window.addEventListener('afterprint', restoreSections, { once: true });
            window.print();
        }

        Array.prototype.forEach.call(document.querySelectorAll('[data-al-modal]'), function (button) {
            button.addEventListener('click', function () {
                var modal = document.getElementById(button.getAttribute('data-al-modal'));
                initModalPager(modal);
                openModalEl(modal);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('.al-modal-bg'), function (backdrop) {
            backdrop.addEventListener('click', function (event) {
                if (event.target === backdrop) closeModal(backdrop);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-al-close]'), function (button) {
            button.addEventListener('click', function () {
                closeModal(button.closest('.al-modal-bg'));
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-al-print-options]'), function (button) {
            button.addEventListener('click', function () {
                openPrintOptions(button.getAttribute('data-print-default'));
            });
        });

        var printGo = document.getElementById('alPrintGo');
        if (printGo) {
            printGo.addEventListener('click', printSelectedSections);
        }

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            var open = document.querySelectorAll('.al-modal-bg.open');
            if (open.length) {
                closeModal(open[open.length - 1]);
            }
        });
    })();
</script>
@endsection
