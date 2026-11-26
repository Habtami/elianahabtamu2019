@extends('layouts.app')

@section('title', 'All Contracts')

@section('styles')
<style>
    :root {
        --epss-blue: #1C75BC;
        --epss-blue-light: #E8F4FD;
        --epss-blue-dark: #1664a0;
        --success: #10B981;
        --warning: hsla(59, 92%, 50%, 0.941);
        --danger: #EF4444;
        --info: #3B82F6;
    }

    .page-container {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        min-height: 100vh;
    }

    .content-card {
        background: white;
        border-radius: 1rem;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .content-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .table-container {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #F3F4F6;
        overflow: hidden;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .table-header {
        background: #F9FAFB;
        border-bottom: 1px solid #E5E7EB;
    }

    .contracts-table {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }

    .table-header th {
        padding: 0.75rem 0.625rem;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        color: #6B7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
        vertical-align: middle;
    }

    .table-header th.text-right {
        text-align: right;
    }

    .table-header th.text-center {
        text-align: center;
    }

    .table-th-sort {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }

    .table-th-sort:hover {
        background: #F3F4F6;
    }

    .sort-arrow {
        font-size: 9px;
    }

    @media (max-width: 1400px) {
        .table-header th {
            padding: 0.75rem 0.375rem;
            letter-spacing: 0;
        }

        .table-cell {
            padding: 0.75rem 0.4375rem;
        }

        .sort-arrow {
            font-size: 8px;
        }
    }

    @media (max-width: 1200px) {
        .table-header th {
            padding: 0.625rem 0.25rem;
            font-size: 9px;
        }

        .table-cell {
            padding: 0.625rem 0.3125rem;
        }
    }

    @media (max-width: 1100px) {
        .table-header th {
            white-space: normal;
            line-height: 1.15;
        }
    }

    .table-row {
        border-bottom: 1px solid #F3F4F6;
        transition: background-color 0.15s ease;
    }

    .table-row:last-child {
        border-bottom: none;
    }

    .table-row:hover {
        background: #F9FAFB;
    }

    .table-cell {
        padding: 0.75rem 0.625rem;
        text-align: left;
        vertical-align: middle;
        overflow: hidden;
        min-width: 0;
    }

    .table-cell.text-right {
        text-align: right;
    }

    .table-cell.text-center {
        text-align: center;
    }

    .status-badge {
        font-size: 0.6875rem;
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        border-width: 1px;
        border-style: solid;
        line-height: 1.25;
        white-space: nowrap;
    }

    .filter-card {
        background: #ffffff;
        border: 1px solid #F3F4F6;
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .filter-control {
        border: 1px solid #D1D5DB;
        border-radius: 0.5rem;
        background: #ffffff;
    }

    .filter-control:focus {
        outline: none;
        border-color: #3B82F6;
    }

    .filter-pill {
        border: 1px solid #BBF7D0;
        border-radius: 9999px;
    }

    body.dark-theme .filter-card {
        background: #111a2b;
        border-color: #1e293b;
    }

    body.dark-theme .filter-control {
        background-color: #0f1a2c;
        border-color: #334155;
        color: #e2e8f0;
    }

    body.dark-theme .filter-control::placeholder {
        color: #64748b;
    }

    body.dark-theme .filter-pill {
        background-color: #122b22;
        border-color: #166534;
        color: #86efac;
    }

    .action-button {
        border-radius: 0.75rem;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }

    .action-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.15);
    }

    .empty-state {
        background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
        border: 2px dashed #CBD5E1;
        border-radius: 1rem;
    }

    .contract-link {
        transition: color 0.2s ease;
    }

    .contract-link:hover {
        color: var(--epss-blue) !important;
    }

    body.dark-theme .table-header th {
        color: #94a3b8;
    }

    body.dark-theme .table-th-sort:hover {
        background: #1b2a45;
    }

    body.dark-theme .table-row:hover {
        background: #16233a;
    }

    body.dark-theme .table-footer {
        background: #111a2b;
        border-color: #1e293b;
    }

    body.dark-theme .table-footer .bg-blue-50 {
        background: #1e3a5f !important;
        border-color: #1d4ed8 !important;
        color: #93c5fd !important;
    }

    body.dark-theme .table-footer a:hover {
        background: #16233a;
    }

    /* ---- shared register pattern ---- */
    .ct-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .ct-stat {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
        width: 100%;
        text-align: left;
        font: inherit;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-left: 4px solid var(--card-color, #0ea5e9);
        border-radius: 12px;
        padding: 1.1rem 1.2rem;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .ct-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
        border-left-color: var(--card-color, #0ea5e9);
    }
    .ct-stat:focus-visible { outline: 2px solid var(--card-color, #0ea5e9); outline-offset: 2px; }
    .ct-stat-main { min-width: 0; }
    .ct-stat-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
    }
    .ct-stat-value {
        display: block;
        font-size: 1.3rem;
        font-weight: 700;
        color: #0f172a;
        margin-top: 0.3rem;
        line-height: 1.15;
    }
    .ct-stat-sub { display: block; margin-top: 0.35rem; font-size: 0.74rem; font-weight: 500; color: #7a90a6; }
    .ct-stat-hint {
        display: block;
        margin-top: 0.45rem;
        font-size: 0.68rem;
        font-weight: 600;
        color: #8aa2b8;
        opacity: 0;
        transition: opacity 0.15s ease, color 0.15s ease;
    }
    .ct-stat:hover .ct-stat-hint,
    .ct-stat:focus-visible .ct-stat-hint { opacity: 1; color: #0ea5e9; }
    .ct-stat-icon {
        flex: none;
        padding-top: 0.1rem;
        font-size: 1.9rem;
        line-height: 1;
        color: var(--card-color, #0ea5e9);
        opacity: 0.22;
    }

    .ct-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.6rem;
        padding: 0.7rem 1rem;
        border-bottom: 1px solid #edf2f8;
        background: #fbfdff;
    }
    .ct-perpage { display: flex; align-items: center; gap: 0.35rem; margin: 0; }
    .ct-perpage label { font-size: 0.72rem; font-weight: 600; color: #6b8299; margin: 0; }
    .ct-perpage select {
        padding: 0.35rem 0.5rem;
        border: 1px solid #cfdcea;
        border-radius: 7px;
        font-size: 0.75rem;
        background: #fff;
        color: #1e293b;
    }
    .ct-action-btn {
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
    .ct-action-btn:hover { background: #f1f5f9; }
    .ct-actions { display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap; }

    .ct-pagination {
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
    .ct-pagination strong { color: #1e293b; font-weight: 600; }
    .ct-pages { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; margin-left: auto; }
    .ct-page-btn {
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
    .ct-page-btn:hover:not(:disabled) { background: #f1f5f9; }
    .ct-page-btn.ct-active { background: #0ea5e9; border-color: #0ea5e9; color: #fff; }
    .ct-page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .ct-page-dots { padding: 0.35rem 0.3rem; color: #6b8299; }

    .ct-modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(10, 25, 45, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1700;
        padding: 1rem;
    }
    .ct-modal-bg.open { display: flex; }
    .ct-modal {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 1080px;
        max-height: 88vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 24px 60px rgba(8, 25, 45, 0.35);
        overflow: hidden;
    }
    .ct-modal.ct-modal-sm { max-width: 480px; }
    .ct-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.3rem;
        border-bottom: 1px solid #edf2f8;
    }
    .ct-modal-head h3 { margin: 0; font-size: 1.05rem; color: #0f2a44; }
    .ct-modal-head span { font-size: 0.75rem; color: #6b8299; }
    .ct-modal-close {
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
    .ct-modal-close:hover { background: #dbe6f1; }
    .ct-modal-body { padding: 1rem 1.3rem 1.4rem; overflow-y: auto; }
    .ct-modal-foot {
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
    .ct-modal-foot strong { color: #1e293b; font-weight: 600; }
    .ct-modal-pages { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; }

    .ct-table-wrap { overflow-x: auto; }
    .ct-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    .ct-table th {
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
    .ct-table td {
        padding: 0.7rem 0.6rem;
        border-bottom: 1px solid #edf2f8;
        color: #1e293b;
        vertical-align: middle;
    }
    .ct-table tbody tr:hover td { background: #f8fbff; }
    .ct-table th.ct-roll,
    .ct-table td.ct-roll {
        width: 3.2rem;
        color: #7a90a6;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .ct-table th.ct-amount,
    .ct-table td.ct-amount { text-align: right; font-variant-numeric: tabular-nums; }
    .ct-empty { text-align: center; color: #8ca3b8; padding: 2rem 1rem; font-style: italic; }

    .ct-print-options { display: flex; flex-direction: column; gap: 0.55rem; }
    .ct-print-options label {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        font-size: 0.88rem;
        color: #1e293b;
        cursor: pointer;
    }
    .ct-print-options input { width: 16px; height: 16px; accent-color: #0ea5e9; }
    .ct-print-hint { margin: 0.7rem 0 0; font-size: 0.78rem; font-weight: 600; color: #b91c1c; }

    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        .sidebar, .sidebar-toggle, .page-header, .page-footer,
        .filter-card, .ct-toolbar, .ct-actions, .ct-pagination, .ct-modal-bg,
        .table-tip-btn, .table-tip-panel, .no-print { display: none !important; }
        .print-hide-section { display: none !important; }
        body { background: #fff; }
        .main-content, .main-content.expanded { margin-left: 0 !important; }
        .page-container { min-height: 0; background: #fff; }
        .page-container > .container { max-width: none !important; padding: 0 !important; }
        .content-card, .table-container { box-shadow: none; border: none; }
        .contracts-table { width: 100%; font-size: 9pt; }
        .contracts-table thead { display: table-header-group; }
        .contracts-table th, .contracts-table td { border: 1px solid #9aa9b8; padding: 0.3rem 0.35rem; }
        .table-row:hover { background: transparent; }
        .ct-stat { box-shadow: none; cursor: default; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endsection

@section('content')
<div class="page-container py-8">
    <div class="container mx-auto px-4">
        @php
            $statCards = [
                [
                    'key' => 'total',
                    'label' => 'Total Contracts',
                    'value' => $totalContracts,
                    'sub' => 'All contracts in system',
                    'color' => '#0ea5e9',
                    'icon' => 'fa-file-contract',
                    'id' => 'ctModalTotal',
                    'hint' => 'Click to view every contract →',
                    'rows' => $statRows['total'],
                ],
                [
                    'key' => 'active',
                    'label' => 'Active',
                    'value' => $totalActive,
                    'sub' => 'Currently active contracts',
                    'color' => '#10b981',
                    'icon' => 'fa-check-circle',
                    'id' => 'ctModalActive',
                    'hint' => 'Click to view active contracts →',
                    'rows' => $statRows['active'],
                ],
                [
                    'key' => 'expiring',
                    'label' => 'Expiring Soon',
                    'value' => $totalExpiring,
                    'sub' => 'Require attention',
                    'color' => '#f59e0b',
                    'icon' => 'fa-clock',
                    'id' => 'ctModalExpiring',
                    'hint' => 'Click to view contracts expiring soon →',
                    'rows' => $statRows['expiring'],
                ],
                [
                    'key' => 'expired',
                    'label' => 'Expired',
                    'value' => $totalExpired,
                    'sub' => 'Past expiration date',
                    'color' => '#ef4444',
                    'icon' => 'fa-exclamation-circle',
                    'id' => 'ctModalExpired',
                    'hint' => 'Click to view expired contracts →',
                    'rows' => $statRows['expired'],
                ],
            ];
        @endphp

        <!-- Summary Stats -->
        <div class="ct-stats" data-print-section="cards">
            @foreach($statCards as $card)
                <button type="button" class="ct-stat" style="--card-color: {{ $card['color'] }};"
                        data-ct-modal="{{ $card['id'] }}" aria-haspopup="dialog"
                        aria-label="{{ $card['label'] }} details" title="{{ $card['hint'] }}">
                    <span class="ct-stat-main">
                        <span class="ct-stat-label">{{ $card['label'] }}</span>
                        <span class="ct-stat-value">{{ $card['value'] }}</span>
                        <span class="ct-stat-sub">{{ $card['sub'] }}</span>
                        <span class="ct-stat-hint">{{ $card['hint'] }}</span>
                    </span>
                    <span class="ct-stat-icon" aria-hidden="true"><i class="fas {{ $card['icon'] }}"></i></span>
                </button>
            @endforeach
        </div>

        <!-- Header -->
        <div class="content-card px-5 py-4 mb-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md shrink-0">
                        <i class="fas fa-file-contract text-white"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl font-semibold text-gray-800 leading-tight">All Contracts</h1>
                        <p class="text-sm text-gray-500 truncate">Manage and monitor all contracts in the system</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 shrink-0 no-print">
                    <button type="button" class="table-tip-btn" data-table-tip aria-expanded="false" aria-controls="tipContracts"><i class="fas fa-info-circle"></i> TIP: info</button>
                    <button type="button" class="inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-2 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-100"
                            data-ct-print-options data-print-default="cards">
                        <i class="fas fa-print mr-2"></i>
                        Print
                    </button>
                    <a href="{{ route('contracts.export-list', request()->only(['search', 'status', 'department'])) }}"
                       class="inline-flex items-center rounded-lg bg-green-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-green-700">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel
                    </a>
                    <a href="{{ route('contracts.export-list', array_merge(request()->only(['search', 'status', 'department']), ['format' => 'pdf'])) }}"
                       class="inline-flex items-center rounded-lg bg-red-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700">
                        <i class="fas fa-file-pdf mr-2"></i>
                        PDF
                    </a>
                    @if(auth()->user()->canCreateContracts())
                    <a href="{{ route('contracts.create') }}"
                       class="inline-flex items-center rounded-lg bg-blue-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>
                        New Contract
                    </a>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin','finance','gs_manager','cmd_manager']))
                    <a href="{{ route('contracts.reports') }}"
                       class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3.5 py-2 text-sm font-semibold text-blue-700 transition-colors hover:bg-blue-100">
                        <i class="fas fa-chart-bar mr-2"></i>
                        View Reports
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div id="tipContracts" class="table-tip-panel no-print">
            <h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4>
            <ul>
                <li>The search box filters by contract name automatically, about 600 ms after you stop typing.</li>
                <li>The "All Status" and "All Departments" dropdowns apply immediately; active filters appear as removable chips with "Clear all".</li>
                <li>ID, Contract Name, Supplier, Start Date, End Date, PG Amount and Status headers sort on click; the arrow shows direction.</li>
                <li>Every row is numbered in the No column; "Rows" above the table changes the page size between 5 and 100.</li>
                <li>Status badges are Active (green), Expiring (amber, with days left) and Expired (red); "Extended Nx" shows under End Date.</li>
                <li>Each row offers View, Edit and Delete icons, but Edit and Delete only appear when your account may change that contract.</li>
                <li>KPI cards open a detail list with a No column and its own pager; the footer shows numbered pages for the table.</li>
                <li>Print picks summary cards, the table, or both; Excel and PDF downloads reuse the current search and filters.</li>
            </ul>
        </div>

        <!-- Filters & Search -->
        <form method="GET" action="{{ route('contracts.index') }}" id="contractFilterForm" class="filter-card p-4 mb-4 no-print">
            @if(request('sort_by'))
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            @endif
            @if(request('sort_direction'))
            <input type="hidden" name="sort_direction" value="{{ request('sort_direction') }}">
            @endif
            @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif

            <!-- Top Row: Search on the left, dropdowns on the right -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="relative w-full md:w-96">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text"
                           id="contractSearch"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by contract name..."
                           autocomplete="off"
                           class="filter-control w-full pl-9 pr-4 py-2 text-sm transition-shadow">
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <select name="status"
                            onchange="this.form.submit()"
                            class="filter-control w-full md:w-auto px-3 py-2 text-sm cursor-pointer">
                        <option value="">All Status</option>
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>

                    @if(auth()->user()->canViewAllContracts())
                    <select name="department"
                            onchange="this.form.submit()"
                            class="filter-control w-full md:w-auto px-3 py-2 text-sm cursor-pointer">
                        <option value="">All Departments</option>
                        @foreach($departments as $key => $value)
                            <option value="{{ $key }}" {{ request('department') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    @endif
                </div>
            </div>

            <!-- Bottom Row: Active filters & Clear all -->
            @if(request()->filled('search')
                || (request()->filled('status') && array_key_exists(request('status'), $statuses))
                || (request()->filled('department') && auth()->user()->canViewAllContracts() && array_key_exists(request('department'), $departments)))
            <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100">
                <span class="text-sm text-gray-500 flex items-center gap-1.5">
                    <i class="fas fa-filter text-sm"></i> Active:
                </span>

                @if(request()->filled('search'))
                <span class="filter-pill inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-green-50 text-green-700">
                    Search: {{ request('search') }}
                    <a href="{{ route('contracts.index', array_merge(request()->except('search'), ['page' => 1])) }}"
                       class="ml-0.5 hover:text-green-900 transition-colors" title="Remove filter">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                </span>
                @endif

                @if(request()->filled('status') && array_key_exists(request('status'), $statuses))
                <span class="filter-pill inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-green-50 text-green-700">
                    Status: {{ $statuses[request('status')] }}
                    <a href="{{ route('contracts.index', array_merge(request()->except('status'), ['page' => 1])) }}"
                       class="ml-0.5 hover:text-green-900 transition-colors" title="Remove filter">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                </span>
                @endif

                @if(request()->filled('department') && auth()->user()->canViewAllContracts() && array_key_exists(request('department'), $departments))
                <span class="filter-pill inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-green-50 text-green-700">
                    Department: {{ $departments[request('department')] }}
                    <a href="{{ route('contracts.index', array_merge(request()->except('department'), ['page' => 1])) }}"
                       class="ml-0.5 hover:text-green-900 transition-colors" title="Remove filter">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                </span>
                @endif

                <a href="{{ route('contracts.index') }}"
                   class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors ml-1">
                    Clear all
                </a>
            </div>
            @endif
        </form>

        <!-- Contracts Table -->
        <div class="table-container" data-print-section="table">
            <div class="ct-toolbar no-print">
                <form method="GET" action="{{ route('contracts.index') }}" class="ct-perpage">
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                        @if(is_scalar($value))
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <label for="ctPerPage">Rows</label>
                    <select name="per_page" id="ctPerPage" onchange="this.form.submit()">
                        @foreach([5, 10, 25, 50, 100] as $size)
                            <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </form>
                <div class="ct-actions">
                    <button type="button" class="ct-action-btn" data-ct-print-options data-print-default="table" title="Print this table only">
                        <i class="fas fa-print"></i> Print table
                    </button>
                </div>
            </div>

            @if($contracts->count() > 0)
                <table class="contracts-table text-left">
                    <thead class="table-header">
                        @php
                            $sortLink = function (string $column, string $label, ?string $icon = null) use ($sortBy, $sortDirection) {
                                $nextDirection = ($sortBy === $column && $sortDirection === 'asc') ? 'desc' : 'asc';
                                $url = route('contracts.index', array_merge(request()->query(), [
                                    'sort_by' => $column,
                                    'sort_direction' => $nextDirection,
                                    'page' => 1,
                                ]));
                                $sortIcon = 'fa-sort text-gray-400';
                                if ($sortBy === $column) {
                                    $sortIcon = ($sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down') . ' text-blue-600';
                                }
                                $html = '<a href="' . e($url) . '" class="inline-flex max-w-full items-center gap-1 transition-colors hover:text-gray-700">';
                                if ($icon) {
                                    $html .= '<i class="fas ' . e($icon) . ' mr-1 shrink-0"></i>';
                                }
                                $html .= '<span class="min-w-0 truncate">' . e($label) . '</span>';
                                $html .= '<i class="fas ' . $sortIcon . ' sort-arrow shrink-0"></i></a>';
                                return $html;
                            };
                        @endphp
                        <tr>
                            <th style="width:3.5%">No</th>
                            <th class="table-th-sort" style="width:5%">{!! $sortLink('id', 'ID') !!}</th>
                            <th class="table-th-sort" style="width:16%">{!! $sortLink('name', 'Contract Name') !!}</th>
                            <th class="table-th-sort" style="width:9%">{!! $sortLink('supplier_name', 'Supplier') !!}</th>
                            <th style="width:10%">Created By</th>
                            <th class="table-th-sort" style="width:8.5%">{!! $sortLink('start_date', 'Start Date') !!}</th>
                            <th class="table-th-sort" style="width:8.5%">{!! $sortLink('end_date', 'End Date') !!}</th>
                            <th class="table-th-sort text-right" style="width:8.5%">{!! $sortLink('performance_guarantee_amount', 'PG Amount') !!}</th>
                            <th style="width:8.5%">PG End Date</th>
                            <th class="table-th-sort text-center" style="width:8%">{!! $sortLink('status', 'Status') !!}</th>
                            <th class="text-right" style="width:14.5%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contracts as $contract)
                        <tr class="table-row">
                            <td class="table-cell text-xs text-gray-400 font-mono whitespace-nowrap">{{ ($contracts->firstItem() ?? 1) + $loop->index }}</td>
                            <td class="table-cell text-xs text-gray-500 font-mono whitespace-nowrap">#{{ $contract->id }}</td>
                            <td class="table-cell">
                                <a href="{{ route('contracts.show', $contract) }}"
                                   title="{{ $contract->name }}"
                                   class="contract-link block truncate text-sm font-semibold text-gray-900">
                                    {{ $contract->name }}
                                </a>
                                @if($contract->description)
                                <p class="mt-0.5 truncate text-xs text-gray-500" title="{{ $contract->description }}">
                                    {{ $contract->description }}
                                </p>
                                @endif
                            </td>
                            <td class="table-cell">
                                <span class="block truncate text-sm text-gray-700" title="{{ $contract->supplier_name }}">{{ $contract->supplier_name ?? 'N/A' }}</span>
                            </td>
                            <td class="table-cell">
                                <div class="block truncate text-sm text-gray-900" title="{{ $contract->creator->name }}">{{ $contract->creator->name }}</div>
                                <div class="block truncate text-xs text-gray-400" title="{{ $contract->creator->getRoleDisplayName() }}">{{ $contract->creator->getRoleDisplayName() }}</div>
                            </td>
                            <td class="table-cell text-xs text-gray-600 whitespace-nowrap">{{ $contract->start_date->format('Y-m-d') }}</td>
                            <td class="table-cell text-xs text-gray-600 whitespace-nowrap">
                                {{ $contract->end_date->format('Y-m-d') }}
                                @if($contract->extended_count > 0)
                                <span class="mt-0.5 block truncate text-[10px] font-medium text-amber-600" title="Extended {{ $contract->extended_count }}x">Extended {{ $contract->extended_count }}x</span>
                                @endif
                            </td>
                            <td class="table-cell text-right text-xs font-medium text-gray-900 whitespace-nowrap">
                                @if($contract->performance_guarantee_amount)
                                ${{ number_format($contract->performance_guarantee_amount, 2) }}
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-xs text-gray-600 whitespace-nowrap">
                                @if($contract->performance_guarantee_end_date)
                                {{ $contract->performance_guarantee_end_date->format('Y-m-d') }}
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-center whitespace-nowrap">
                                <span class="status-badge
                                    @if($contract->status === 'active') bg-emerald-50 text-emerald-700 border-emerald-100
                                    @elseif($contract->status === 'expiring') bg-amber-50 text-amber-700 border-amber-100
                                    @elseif($contract->status === 'expired') bg-red-50 text-red-700 border-red-100
                                    @else bg-gray-50 text-gray-600 border-gray-200 @endif">
                                    {{ ucfirst($contract->status) }}
                                </span>
                                @if($contract->status === 'expiring')
                                <div class="mt-1 truncate text-[10px] text-amber-600" title="{{ $contract->getDaysUntilContractEnd() }} days left">
                                    {{ $contract->getDaysUntilContractEnd() }} days left
                                </div>
                                @endif
                            </td>
                            <td class="table-cell text-right">
                                <div class="flex items-center justify-end gap-0.5">
                                    <a href="{{ route('contracts.show', $contract) }}"
                                       class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="View Contract">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($contract->canUserEdit(auth()->user()))
                                    <a href="{{ route('contracts.edit', $contract) }}"
                                       class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif

                                    @if($contract->canUserDelete(auth()->user()))
                                    <form method="POST"
                                          action="{{ route('contracts.destroy', $contract) }}"
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this contract?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            <!-- Pagination Footer -->
            @php
                $paginator = $contracts->appends(request()->query());
                $ctCurrent = $paginator->currentPage();
                $ctLast = $paginator->lastPage();
                $ctPages = [];
                for ($ctPage = 1; $ctPage <= $ctLast; $ctPage++) {
                    if ($ctPage === 1 || $ctPage === $ctLast || abs($ctPage - $ctCurrent) <= 1) {
                        $ctPages[] = $ctPage;
                    } elseif (end($ctPages) !== '...') {
                        $ctPages[] = '...';
                    }
                }
            @endphp
            <div class="ct-pagination no-print">
                <span>
                    Showing <strong>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</strong>
                    of <strong>{{ $paginator->total() }}</strong>
                </span>
                <div class="ct-pages">
                    @if($ctCurrent > 1)
                        <a class="ct-page-btn" href="{{ $paginator->url($ctCurrent - 1) }}" rel="prev">« Prev</a>
                    @else
                        <button type="button" class="ct-page-btn" disabled>« Prev</button>
                    @endif
                    @foreach($ctPages as $ctPage)
                        @if($ctPage === '...')
                            <span class="ct-page-dots">...</span>
                        @else
                            <a class="ct-page-btn {{ $ctPage === $ctCurrent ? 'ct-active' : '' }}" href="{{ $paginator->url($ctPage) }}" @if($ctPage === $ctCurrent) aria-current="page" @endif>{{ $ctPage }}</a>
                        @endif
                    @endforeach
                    @if($ctCurrent < $ctLast)
                        <a class="ct-page-btn" href="{{ $paginator->url($ctCurrent + 1) }}" rel="next">Next »</a>
                    @else
                        <button type="button" class="ct-page-btn" disabled>Next »</button>
                    @endif
                </div>
            </div>

            @else
            <!-- Empty State -->
            <div class="empty-state p-16 text-center">
                <div class="mb-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-gray-300 to-gray-400 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-file-contract text-4xl text-gray-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-3">No contracts found</h3>
                    <p class="text-gray-500 text-lg max-w-md mx-auto">
                        @if(request()->has('search') || request()->has('status') || request()->has('department'))
                        Try adjusting your filters or search terms to find what you're looking for
                        @else
                        Get started by creating your first contract to begin managing your agreements
                        @endif
                    </p>
                </div>
                @if(auth()->user()->canCreateContracts())
                <a href="{{ route('contracts.create') }}"
                   class="action-button bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Contract
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

@foreach($statCards as $card)
    <div class="ct-modal-bg" id="{{ $card['id'] }}" role="dialog" aria-modal="true" aria-labelledby="{{ $card['id'] }}Title">
        <div class="ct-modal">
            <div class="ct-modal-head">
                <div>
                    <h3 id="{{ $card['id'] }}Title">{{ $card['label'] }}</h3>
                    <span class="ct-modal-sub">{{ $card['rows']->count() }} record(s) &middot; {{ $card['sub'] }}</span>
                </div>
                <button type="button" class="ct-modal-close" aria-label="Close" data-ct-close>×</button>
            </div>
            <div class="ct-modal-body">
                @if($card['rows']->isEmpty())
                    <div class="ct-empty">No contracts in this group.</div>
                @else
                    <div class="ct-table-wrap">
                        <table class="ct-table">
                            <thead>
                                <tr>
                                    <th class="ct-roll">No</th>
                                    <th>Contract</th>
                                    <th>Supplier</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th class="ct-amount">PG Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($card['rows'] as $row)
                                    <tr>
                                        <td class="ct-roll">{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('contracts.show', $row) }}" class="font-medium text-[#1C75BC] hover:underline">{{ $row->name }}</a>
                                            <div class="text-[11px] text-gray-400">#{{ $row->id }}{{ $row->department ? ' · ' . ucfirst(str_replace('_', ' ', $row->department)) : '' }}</div>
                                        </td>
                                        <td>{{ $row->supplier_name ?? 'N/A' }}</td>
                                        <td>{{ $row->start_date?->format('M d, Y') ?? '-' }}</td>
                                        <td>
                                            {{ $row->end_date?->format('M d, Y') ?? '-' }}
                                            @if($row->extended_count > 0)
                                                <div class="text-[11px] text-amber-600">Extended {{ $row->extended_count }}x</div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="status-badge
                                                @if($row->status === 'active') bg-emerald-50 text-emerald-700 border-emerald-100
                                                @elseif($row->status === 'expiring') bg-amber-50 text-amber-700 border-amber-100
                                                @elseif($row->status === 'expired') bg-red-50 text-red-700 border-red-100
                                                @else bg-gray-50 text-gray-600 border-gray-200 @endif">
                                                {{ ucfirst($row->status) }}
                                            </span>
                                            @if($row->status === 'expiring')
                                                <div class="text-[11px] text-amber-600 mt-0.5">{{ $row->getDaysUntilContractEnd() }} days left</div>
                                            @endif
                                        </td>
                                        <td class="ct-amount">
                                            @if($row->performance_guarantee_amount)
                                                ${{ number_format($row->performance_guarantee_amount, 2) }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="ct-modal-foot">
                <span class="ct-modal-range"></span>
                <div class="ct-modal-pages"></div>
            </div>
        </div>
    </div>
@endforeach

<div class="ct-modal-bg" id="ctModalPrint" role="dialog" aria-modal="true" aria-labelledby="ctPrintTitle">
    <div class="ct-modal ct-modal-sm">
        <div class="ct-modal-head">
            <div>
                <h3 id="ctPrintTitle">Print Contracts</h3>
                <span>Choose which sections appear on the printed page</span>
            </div>
            <button type="button" class="ct-modal-close" aria-label="Close" data-ct-close>×</button>
        </div>
        <div class="ct-modal-body">
            <div class="ct-print-options">
                <label><input type="checkbox" class="ct-print-check" value="cards"> Summary cards</label>
                <label><input type="checkbox" class="ct-print-check" value="table" checked> Contracts table</label>
            </div>
            <p id="ctPrintHint" class="ct-print-hint" style="display:none;">Select at least one section to print.</p>
        </div>
        <div class="ct-modal-foot" style="display:flex;">
            <span></span>
            <div class="ct-modal-pages">
                <button type="button" class="ct-action-btn" data-ct-close>Cancel</button>
                <button type="button" class="inline-flex items-center px-3 py-1.5 bg-[#0ea5e9] text-white text-xs font-semibold rounded-lg hover:bg-[#0284c7]" id="ctPrintGo">
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
        const form = document.getElementById('contractFilterForm');
        const input = document.getElementById('contractSearch');
        if (form && input) {
            let timer = null;
            let appliedValue = input.value;

            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    if (input.value === appliedValue) return;
                    appliedValue = input.value;
                    form.submit();
                }, 600);
            });

            form.addEventListener('submit', function () {
                clearTimeout(timer);
            });
        }
    })();

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
            if (!document.querySelector('.ct-modal-bg.open')) {
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
            var table = modal.querySelector('.ct-modal-body .ct-table');
            if (!table) return;

            var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
            if (!rows.length) return;

            var foot = modal.querySelector('.ct-modal-foot');
            var rangeNode = foot && foot.querySelector('.ct-modal-range');
            var pagesNode = foot && foot.querySelector('.ct-modal-pages');
            if (!foot || !rangeNode || !pagesNode) return;

            var state = modal.ctPager;
            if (!state) {
                state = modal.ctPager = { rows: rows, page: 1, foot: foot, range: rangeNode, pages: pagesNode };
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
                    dots.className = 'ct-page-dots';
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
                var active = state.pages.querySelector('.ct-active');
                if (active) active.focus();
            }
        }

        function pagerButton(label, disabled, active, onClick) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'ct-page-btn' + (active ? ' ct-active' : '');
            button.textContent = label;
            button.disabled = disabled;
            button.addEventListener('click', onClick);
            return button;
        }

        function goToModalPage(state, page) {
            state.page = page;
            paintModalPage(state, true);
            var body = state.foot.parentNode.querySelector('.ct-modal-body');
            if (body) body.scrollTop = 0;
        }

        var printModal = document.getElementById('ctModalPrint');
        var printChecks = Array.prototype.slice.call(document.querySelectorAll('.ct-print-check'));
        var printHint = document.getElementById('ctPrintHint');

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

        Array.prototype.forEach.call(document.querySelectorAll('[data-ct-modal]'), function (button) {
            button.addEventListener('click', function () {
                var modal = document.getElementById(button.getAttribute('data-ct-modal'));
                initModalPager(modal);
                openModalEl(modal);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('.ct-modal-bg'), function (backdrop) {
            backdrop.addEventListener('click', function (event) {
                if (event.target === backdrop) closeModal(backdrop);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-ct-close]'), function (button) {
            button.addEventListener('click', function () {
                closeModal(button.closest('.ct-modal-bg'));
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-ct-print-options]'), function (button) {
            button.addEventListener('click', function () {
                openPrintOptions(button.getAttribute('data-print-default'));
            });
        });

        var printGo = document.getElementById('ctPrintGo');
        if (printGo) {
            printGo.addEventListener('click', printSelectedSections);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;
            var open = document.querySelectorAll('.ct-modal-bg.open');
            if (open.length) {
                closeModal(open[open.length - 1]);
            }
        });
    })();
</script>
@endsection
