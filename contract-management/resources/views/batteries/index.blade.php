@extends('layouts.app')

@section('title', 'Battery Given and Return')
@section('page-title', 'Battery Given and Return')

@section('styles')
<style>
    .batt-wrapper { max-width: 1200px; margin: 0 auto; padding: 1.5rem 1rem 2rem; }

    .batt-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.3rem;
    }
    .batt-toolbar h2 { font-size: 1.35rem; font-weight: 700; color: #0f2a44; margin: 0; }

    /* BUTTONS (reference palette) */
    .batt-btn {
        background: #1a6fc4;
        color: #fff;
        border: none;
        padding: 0.6rem 1.1rem;
        border-radius: 9px;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: background 0.2s;
    }
    .batt-btn:hover { background: #155a9e; }
    .batt-btn.secondary { background: #e8eff7; color: #1a4a73; }
    .batt-btn.secondary:hover { background: #d5e2f0; }
    .batt-btn.danger { background: #e05252; }
    .batt-btn.danger:hover { background: #c53e3e; }
    .batt-btn.small { padding: 0.35rem 0.7rem; font-size: 0.72rem; }

    /* CARD / TABLE */
    .batt-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.3rem;
        box-shadow: 0 2px 8px rgba(0, 30, 60, 0.06);
        border: 1px solid #e3ebf3;
        margin-bottom: 1.3rem;
    }
    .batt-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.8rem;
        margin-bottom: 1rem;
    }
    .batt-card-head h3 { font-size: 1rem; margin: 0; color: #0f2a44; }

    .batt-search {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .batt-search input, .batt-search select {
        padding: 0.5rem 0.7rem;
        border: 1px solid #cddaea;
        border-radius: 8px;
        font-size: 0.82rem;
        background: #fff;
        color: #1e293b;
    }
    .batt-search input:focus, .batt-search select:focus {
        outline: none;
        border-color: #1a6fc4;
        box-shadow: 0 0 0 3px rgba(26, 111, 196, 0.12);
    }

    .batt-table-wrap { overflow-x: auto; }
    .batt-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    .batt-table th {
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
    .batt-table td {
        padding: 0.6rem;
        border-bottom: 1px solid #edf2f8;
        color: #1e293b;
        vertical-align: middle;
    }
    .batt-table tr:hover td { background: #f8fbff; }
    .batt-empty { text-align: center; color: #8ca3b8; padding: 1.5rem; font-style: italic; }

    .batt-status {
        font-size: 0.68rem;
        padding: 0.2rem 0.7rem;
        border-radius: 20px;
        font-weight: 600;
        white-space: nowrap;
    }
    .batt-status.in-service { background: #dcfce7; color: #166534; }
    .batt-status.returned { background: #fef9c3; color: #854d0e; }

    /* MODAL */
    .batt-modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(10, 25, 45, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1700;
        padding: 1rem;
    }
    .batt-modal-bg.open { display: flex; }
    .batt-modal {
        background: #fff;
        border-radius: 16px;
        padding: 1.6rem;
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 45px rgba(10, 25, 45, 0.25);
    }
    .batt-modal h3 { margin: 0 0 1.2rem; color: #0f2a44; font-size: 1.1rem; }

    .batt-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }
    .batt-field label {
        display: block;
        font-size: 0.72rem;
        font-weight: 600;
        color: #46617d;
        margin-bottom: 0.3rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .batt-field input, .batt-field select {
        width: 100%;
        padding: 0.6rem 0.7rem;
        border: 1px solid #cddaea;
        border-radius: 8px;
        font-size: 0.85rem;
        background: #fff;
        color: #1e293b;
    }
    .batt-field input:focus, .batt-field select:focus {
        outline: none;
        border-color: #1a6fc4;
        box-shadow: 0 0 0 3px rgba(26, 111, 196, 0.12);
    }
    .batt-field-error {
        color: #b91c1c;
        font-size: 0.72rem;
        font-weight: 600;
        margin-top: 0.3rem;
    }
    .batt-form-actions { margin-top: 1.2rem; display: flex; gap: 0.6rem; }

    .batt-delete-msg {
        font-size: 0.9rem;
        color: #46617d;
        line-height: 1.5;
        margin: 0;
    }
    .batt-delete-msg strong { color: #0f2a44; }

    .batt-history-scroll {
        max-height: 55vh;
        overflow-y: auto;
        border: 1px solid #e3ebf3;
        border-radius: 10px;
        background: #f8fbff;
    }
    .batt-history-list { display: flex; flex-direction: column; gap: 0.6rem; padding: 0.9rem; }
    .batt-history-item {
        background: #fff;
        border: 1px solid #e3ebf3;
        border-radius: 10px;
        padding: 0.75rem 0.85rem;
    }
    .batt-history-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 0.35rem;
    }
    .batt-history-action { font-weight: 700; color: #0f2a44; font-size: 0.85rem; }
    .batt-history-date { font-size: 0.72rem; color: #6b8299; white-space: nowrap; }
    .batt-history-meta { font-size: 0.78rem; color: #46617d; margin-bottom: 0.3rem; }
    .batt-history-notes {
        font-size: 0.78rem;
        color: #334155;
        background: #f8fbff;
        border-left: 3px solid #1a6fc4;
        border-radius: 6px;
        padding: 0.35rem 0.5rem;
    }
    .batt-history-user { font-size: 0.7rem; color: #93a6b9; margin-top: 0.35rem; }
    .batt-history-user-name { color: #46617d; font-weight: 600; }
    .batt-history-role { color: #6b8299; }
    .batt-history-loading {
        padding: 1.2rem;
        text-align: center;
        color: #6b8299;
        font-size: 0.85rem;
    }

    .batt-errors {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        border-radius: 10px;
        padding: 0.7rem 0.9rem;
        font-size: 0.8rem;
        margin-bottom: 1rem;
    }
    .batt-errors ul { margin: 0; padding-left: 1.1rem; }

    /* SHARED REGISTER PATTERN */
    .bb-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .bb-stat {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
        width: 100%;
        text-align: left;
        font: inherit;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-left: 4px solid var(--card-color, #1a6fc4);
        border-radius: 12px;
        padding: 1.1rem 1.2rem;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .bb-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
        border-left-color: var(--card-color, #1a6fc4);
    }
    .bb-stat:focus-visible { outline: 2px solid var(--card-color, #1a6fc4); outline-offset: 2px; }
    .bb-stat-main { min-width: 0; }
    .bb-stat-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
    }
    .bb-stat-value {
        display: block;
        font-size: 1.7rem;
        font-weight: 700;
        color: #0f172a;
        margin-top: 0.3rem;
        line-height: 1;
    }
    .bb-stat-hint {
        display: block;
        margin-top: 0.55rem;
        font-size: 0.68rem;
        font-weight: 600;
        color: #8aa2b8;
        opacity: 0;
        transition: opacity 0.15s ease, color 0.15s ease;
    }
    .bb-stat:hover .bb-stat-hint,
    .bb-stat:focus-visible .bb-stat-hint { opacity: 1; color: #1a6fc4; }
    .bb-stat-icon {
        flex: none;
        padding-top: 0.1rem;
        font-size: 1.9rem;
        line-height: 1;
        color: var(--card-color, #1a6fc4);
        opacity: 0.22;
    }

    .bb-action-btn {
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
    .bb-action-btn:hover { background: #f1f5f9; }
    .bb-actions { display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap; }
    .bb-action-btn-active { background: #e0f2fe; border-color: #bae6fd; color: #0369a1; }

    .bb-perpage { display: flex; align-items: center; gap: 0.35rem; }
    .bb-perpage label { font-size: 0.72rem; font-weight: 600; color: #6b8299; margin: 0; }
    .bb-perpage select {
        padding: 0.35rem 0.5rem;
        border: 1px solid #cfdcea;
        border-radius: 7px;
        font-size: 0.75rem;
        background: #fff;
        color: #1e293b;
    }

    .bb-pagination {
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
    .bb-pagination strong { color: #1e293b; font-weight: 600; }
    .bb-pages { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; margin-left: auto; }
    .bb-page-btn {
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
    .bb-page-btn:hover:not(:disabled) { background: #f1f5f9; }
    .bb-page-btn.bb-active { background: #1a6fc4; border-color: #1a6fc4; color: #fff; }
    .bb-page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .bb-page-dots { padding: 0.35rem 0.3rem; color: #6b8299; }

    .bb-modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(10, 25, 45, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1700;
        padding: 1rem;
    }
    .bb-modal-bg.open { display: flex; }
    .bb-modal {
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
    .bb-modal.bb-modal-sm { max-width: 480px; }
    .bb-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.3rem;
        border-bottom: 1px solid #edf2f8;
    }
    .bb-modal-head h3 { margin: 0; font-size: 1.05rem; color: #0f2a44; }
    .bb-modal-head span { font-size: 0.75rem; color: #6b8299; }
    .bb-modal-close {
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
    .bb-modal-close:hover { background: #dbe6f1; }
    .bb-modal-body { padding: 1rem 1.3rem 1.4rem; overflow-y: auto; }
    .bb-modal-foot {
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
    .bb-modal-foot strong { color: #1e293b; font-weight: 600; }
    .bb-modal-pages { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; }

    .bb-table-wrap { overflow-x: auto; }
    .bb-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    .bb-table th {
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
    .bb-table td {
        padding: 0.7rem 0.6rem;
        border-bottom: 1px solid #edf2f8;
        color: #1e293b;
        vertical-align: middle;
    }
    .bb-table tbody tr:hover td { background: #f8fbff; }
    .bb-table th.bb-roll,
    .bb-table td.bb-roll {
        width: 3.2rem;
        color: #7a90a6;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .bb-empty { text-align: center; color: #8ca3b8; padding: 2rem 1rem; font-style: italic; }
    .bb-modal-sub { font-size: 0.72rem; color: #7a90a6; }

    .bb-print-options { display: flex; flex-direction: column; gap: 0.55rem; }
    .bb-print-options label {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        font-size: 0.88rem;
        color: #1e293b;
        cursor: pointer;
    }
    .bb-print-options input { width: 16px; height: 16px; accent-color: #1a6fc4; }
    .bb-print-hint { margin: 0.7rem 0 0; font-size: 0.78rem; font-weight: 600; color: #b91c1c; }

    .bb-filter-bar {
        display: flex;
        align-items: flex-end;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .bb-filter-bar input, .bb-filter-bar select {
        padding: 0.45rem 0.7rem;
        border: 1px solid #cddaea;
        border-radius: 8px;
        font-size: 0.82rem;
        background: #fff;
        color: #1e293b;
    }
    .bb-filter-bar input:focus, .bb-filter-bar select:focus {
        outline: none;
        border-color: #1a6fc4;
        box-shadow: 0 0 0 3px rgba(26, 111, 196, 0.12);
    }

    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        .sidebar, .sidebar-toggle, .page-header, .page-footer,
        .bb-stats-actions, .bb-filter-bar, .bb-actions, .batt-btn,
        .bb-pagination, .batt-modal-bg, .bb-modal-bg, .batt-actions, .no-print { display: none !important; }
        .print-hide-section { display: none !important; }
        body { background: #fff; }
        .main-content, .main-content.expanded { margin-left: 0 !important; }
        .batt-wrapper, .bb-page { max-width: none; margin: 0; padding: 0; }
        .batt-card, .bb-table-card { box-shadow: none; border: none; border-radius: 0; padding: 0; margin: 0; }
        .batt-table-wrap, .bb-table-scroll { overflow: visible; }
        .bb-table { width: 100%; font-size: 9pt; }
        .bb-table thead { display: table-header-group; }
        .bb-table th, .bb-table td { border: 1px solid #9aa9b8; padding: 0.3rem 0.35rem; }
        .bb-table tr:hover td { background: transparent; }
        .batt-status { padding: 0; border-radius: 0; background: transparent !important; }
        .bb-stat { box-shadow: none; cursor: default; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endsection

@section('content')
<div class="batt-wrapper bb-page">

    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-battery-full text-[#1a6fc4] mr-3"></i>
                Battery Given and Return
            </h1>
            <p class="text-gray-600 mt-2">Register batteries given to drivers and track their return, status and mileage.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 no-print">
            <button type="button" class="batt-btn secondary" data-bb-print-options data-print-default="table">
                <i class="fas fa-print"></i> Print
            </button>
            @if(auth()->user()->canExportBatteries())
            <a href="{{ route('batteries.export.excel', request()->query()) }}" class="batt-btn secondary" style="text-decoration:none;">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <a href="{{ route('batteries.export.pdf', request()->query()) }}" class="batt-btn secondary" style="text-decoration:none;">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            @endif
            @if(auth()->user()->canViewBatteryReports())
            <a href="{{ route('batteries.report') }}" class="batt-btn secondary" style="text-decoration:none;">
                <i class="fas fa-clipboard-list"></i> Report
            </a>
            @endif
            @if(auth()->user()->canGiveBatteries())
            <button type="button" class="batt-btn" onclick="openBatteryModal()">
                <i class="fas fa-plus"></i> Give Battery
            </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    @php
        $statCards = [
            ['key' => 'total', 'label' => 'Total Batteries', 'color' => '#1a6fc4', 'icon' => 'fa-battery-full', 'id' => 'bbModalTotal', 'hint' => 'Click to view all batteries →', 'view' => 'View all batteries'],
            ['key' => 'in_service', 'label' => 'In Service', 'color' => '#10b981', 'icon' => 'fa-circle-check', 'id' => 'bbModalInService', 'hint' => 'Click to view batteries in service →', 'view' => 'View batteries in service'],
            ['key' => 'returned', 'label' => 'Returned', 'color' => '#f59e0b', 'icon' => 'fa-rotate-left', 'id' => 'bbModalReturned', 'hint' => 'Click to view returned batteries →', 'view' => 'View returned batteries'],
            ['key' => 'variance', 'label' => 'KM Difference', 'color' => '#8b5cf6', 'icon' => 'fa-arrow-trend-up', 'id' => 'bbModalVariance', 'hint' => 'Click to view batteries with a KM difference →', 'view' => 'View batteries with a kilometre difference'],
        ];
    @endphp

    <div class="bb-stats" data-print-section="cards">
        @foreach($statCards as $card)
            @php $statBatteries = $statGroups[$card['key']]; @endphp
            <button type="button" class="bb-stat" style="--card-color: {{ $card['color'] }};"
                    data-bb-modal="{{ $card['id'] }}" aria-haspopup="dialog"
                    aria-label="{{ $card['view'] }}" title="{{ $card['view'] }}">
                <span class="bb-stat-main">
                    <span class="bb-stat-label">{{ $card['label'] }}</span>
                    <span class="bb-stat-value">{{ $statBatteries->count() }}</span>
                    <span class="bb-stat-hint">{{ $card['hint'] }}</span>
                </span>
                <span class="bb-stat-icon" aria-hidden="true"><i class="fas {{ $card['icon'] }}"></i></span>
            </button>
        @endforeach
    </div>

    <div class="batt-card bb-table-card" data-print-section="table">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-gray-200">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Given Batteries</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    @if(request('search'))
                        {{ $batteries->total() }} result(s) for &ldquo;{{ request('search') }}&rdquo;
                    @else
                        {{ $batteries->total() }} battery(ies)
                    @endif
                    &middot; page {{ $batteries->currentPage() }} of {{ $batteries->lastPage() }}
                </p>
            </div>
            <div class="bb-actions no-print">
                <button type="button" class="bb-action-btn" data-bb-tip aria-expanded="false" aria-controls="tipBatteries">
                    <i class="fas fa-info-circle"></i> TIP: info
                </button>
                <button type="button" class="bb-action-btn" data-bb-print-options data-print-default="table" title="Print this table only">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>

        <div class="py-3 no-print">
            <form method="GET" action="{{ route('batteries.index') }}" class="bb-filter-bar">
                <input type="hidden" name="per_page" value="{{ $batteries->perPage() }}">
                <input type="text" name="search" value="{{ request('search') }}" autocomplete="off"
                       aria-label="Search"
                       placeholder="Search serial, type, plate, driver..."
                       class="w-full sm:w-64">
                <select name="brand" aria-label="Brand">
                    <option value="">All Brands</option>
                    @foreach($brands as $key => $label)
                        <option value="{{ $key }}" {{ request('brand') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="batt-btn small">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('batteries.index') }}" class="batt-btn secondary small" style="text-decoration:none;">
                    <i class="fas fa-rotate-left"></i> Reset
                </a>
            </form>
            <form method="GET" action="{{ route('batteries.index') }}" class="bb-perpage mt-2">
                @foreach(request()->except(['per_page', 'page']) as $key => $value)
                    @if(is_scalar($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <label for="bbPerPage">Rows</label>
                <select name="per_page" id="bbPerPage" onchange="this.form.submit()">
                    @foreach([5, 10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected($batteries->perPage() === $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div id="tipBatteries" class="table-tip-panel no-print">
            <h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4>
            <ul>
                <li>Columns show serial, material code, battery type, brand, plate, driver, initial and return KM, difference and status.</li>
                <li>Search matches serial, type, plate and driver; the brand filter and Rows selector limit the register, then press Filter.</li>
                <li>Status stays "In service" until a battery is returned; Difference shows for returned rows or when return KM exceeds initial.</li>
                <li>Row actions are permission-based: History opens the audit trail, Return needs the exact serial, plus Edit and Del.</li>
                <li>Give Battery registers a battery and can add a new material code, type, brand or driver inline; difference recalculates automatically.</li>
                <li>The KPI cards open the full list regardless of search, brand or page size; Excel, PDF and Print follow the current filters.</li>
            </ul>
        </div>

        <div class="batt-table-wrap bb-table-scroll">
            <table class="batt-table bb-table">
                <thead>
                    <tr>
                        <th class="bb-roll">No</th>
                        <th>Date Mfg</th>
                        <th>Serial No</th>
                        <th>Material Code</th>
                        <th>Battery Type</th>
                        <th>Brand</th>
                        <th>Plate No</th>
                        <th>Driver</th>
                        <th>Initial KM</th>
                        <th>Return KM</th>
                        <th>Difference</th>
                        <th>Status</th>
                        <th class="no-print">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batteries as $battery)
                    <tr>
                        <td class="bb-roll">{{ ($batteries->firstItem() ?? 1) + $loop->index }}</td>
                        <td>{{ $battery->date_of_manufacture ? $battery->date_of_manufacture->format('Y-m-d') : '—' }}</td>
                        <td style="font-weight:600;">{{ $battery->serial_number }}</td>
                        <td>{{ $battery->material_code ?: '—' }}</td>
                        <td>{{ $battery->battery_type }}</td>
                        <td>{{ $battery->brand }}</td>
                        <td>{{ $battery->plate_number }}</td>
                        <td>{{ $battery->driver_name ?: '—' }}</td>
                        <td>{{ number_format($battery->initial_kilometer) }}</td>
                        <td>{{ number_format($battery->return_kilometer) }}</td>
                        <td>
                            @if($battery->status === 'returned' || $battery->return_kilometer > $battery->initial_kilometer)
                                @php
                                    $difference = $battery->return_kilometer - $battery->initial_kilometer;
                                @endphp
                                <span style="font-weight:600; color:{{ $difference >= 0 ? '#166534' : '#b91c1c' }};">
                                    {{ $difference >= 0 ? '+' : '' }}{{ number_format($difference) }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="batt-status {{ $battery->status === 'returned' ? 'returned' : 'in-service' }}">
                                {{ $battery->getStatusDisplayName() }}
                            </span>
                        </td>
                        <td class="no-print batt-actions" style="white-space:nowrap;">
                            <button type="button"
                                    class="batt-btn secondary small"
                                    data-action="{{ route('batteries.history', $battery) }}"
                                    data-serial="{{ $battery->serial_number }}"
                                    onclick="openHistoryModal(this)">History</button>
                            @if(auth()->user()->canReturnBatteries() && !$battery->isReturned())
                            <button type="button"
                                    class="batt-btn small"
                                    data-action="{{ route('batteries.return', $battery) }}"
                                    data-serial="{{ $battery->serial_number }}"
                                    data-init="{{ $battery->initial_kilometer }}"
                                    onclick="openReturnModal(this)">Return</button>
                            @endif
                            @if(auth()->user()->canEditBatteries())
                            <button type="button"
                                    class="batt-btn secondary small"
                                    data-action="{{ route('batteries.update', $battery) }}"
                                    data-date="{{ $battery->date_of_manufacture ? $battery->date_of_manufacture->format('Y-m-d') : '' }}"
                                    data-serial="{{ $battery->serial_number }}"
                                    data-material="{{ $battery->material_code }}"
                                    data-type="{{ $battery->battery_type }}"
                                    data-brand="{{ $battery->brand }}"
                                    data-plate="{{ $battery->plate_number }}"
                                    data-driver="{{ $battery->driver_name }}"
                                    data-init="{{ $battery->initial_kilometer }}"
                                    data-ret="{{ $battery->return_kilometer }}"
                                    data-status="{{ $battery->status }}"
                                    onclick="editBattery(this)">Edit</button>
                            @endif
                            @if(auth()->user()->canDeleteBatteries())
                            <button type="button"
                                    class="batt-btn danger small"
                                    data-action="{{ route('batteries.destroy', $battery) }}"
                                    data-serial="{{ $battery->serial_number }}"
                                    onclick="openDeleteModal(this)">Del</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="batt-empty">No batteries given yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($batteries->total() > 0)
            @php
                $bbCurrent = $batteries->currentPage();
                $bbLast = $batteries->lastPage();
                $bbPages = [];
                for ($bbPage = 1; $bbPage <= $bbLast; $bbPage++) {
                    if ($bbPage === 1 || $bbPage === $bbLast || abs($bbPage - $bbCurrent) <= 1) {
                        $bbPages[] = $bbPage;
                    } elseif (end($bbPages) !== '...') {
                        $bbPages[] = '...';
                    }
                }
            @endphp
            <div class="bb-pagination no-print">
                <span>
                    Showing <strong>{{ $batteries->firstItem() }}–{{ $batteries->lastItem() }}</strong>
                    of <strong>{{ $batteries->total() }}</strong>
                </span>
                <div class="bb-pages">
                    @if($bbCurrent > 1)
                        <a class="bb-page-btn" href="{{ $batteries->url($bbCurrent - 1) }}" rel="prev">« Prev</a>
                    @else
                        <button type="button" class="bb-page-btn" disabled>« Prev</button>
                    @endif
                    @foreach($bbPages as $bbPage)
                        @if($bbPage === '...')
                            <span class="bb-page-dots">...</span>
                        @else
                            <a class="bb-page-btn {{ $bbPage === $bbCurrent ? 'bb-active' : '' }}" href="{{ $batteries->url($bbPage) }}" @if($bbPage === $bbCurrent) aria-current="page" @endif>{{ $bbPage }}</a>
                        @endif
                    @endforeach
                    @if($bbCurrent < $bbLast)
                        <a class="bb-page-btn" href="{{ $batteries->url($bbCurrent + 1) }}" rel="next">Next »</a>
                    @else
                        <button type="button" class="bb-page-btn" disabled>Next »</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- MODAL: BATTERY -->
<div class="batt-modal-bg {{ $errors->any() && !$errors->has('return_serial') && !$errors->has('returned_kilometer') ? 'open' : '' }}" id="battModal">
    <div class="batt-modal">
        <h3 id="battModalTitle">🔋 Give Battery</h3>

        @if($errors->any())
        <div class="batt-errors">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @php $editingId = old('_battery_id'); @endphp
        <form method="POST"
              action="{{ $editingId ? route('batteries.update', $editingId) : route('batteries.store') }}"
              id="batteryForm"
              data-store-url="{{ route('batteries.store') }}">
            @csrf
            <input type="hidden" name="_battery_id" id="batteryId" value="{{ $editingId }}">
            <input type="hidden" name="_method" id="batteryMethod" value="PUT" {{ $editingId ? '' : 'disabled' }}>

            <div class="batt-form-grid">
                <div class="batt-field">
                    <label>Date Manufacturer</label>
                    <input type="date" name="date_of_manufacture" id="b_date" value="{{ old('date_of_manufacture') }}">
                </div>
                <div class="batt-field">
                    <label>Serial No</label>
                    <input name="serial_number" id="b_serial" value="{{ old('serial_number') }}" placeholder="BAT-0000" required>
                </div>
                <div class="batt-field">
                    <label>Material Code</label>
                    <select name="material_code" id="b_material" onchange="syncBatteryTypeFromMaterial()">
                        <option value="">Select material code</option>
                        @php $listedCodes = []; @endphp
                        @foreach($batteryTypes as $type)
                            @if($type['material_code'] && !in_array($type['material_code'], $listedCodes, true))
                                @php $listedCodes[] = $type['material_code']; @endphp
                                <option value="{{ $type['material_code'] }}"
                                        data-type="{{ $type['name'] }}"
                                        {{ old('material_code') === $type['material_code'] ? 'selected' : '' }}>
                                    {{ $type['material_code'] }} — {{ $type['name'] }}
                                </option>
                            @endif
                        @endforeach
                        <option value="__new__" {{ old('material_code') === '__new__' ? 'selected' : '' }}>+ Register new material code...</option>
                    </select>
                </div>
                <div class="batt-field">
                    <label>Battery Type</label>
                    <select name="battery_type" id="b_type" onchange="syncBatteryMaterial()" required>
                        <option value="">Select battery type</option>
                        @foreach($batteryTypes as $type)
                            <option value="{{ $type['name'] }}"
                                    data-code="{{ $type['material_code'] }}"
                                    {{ old('battery_type') === $type['name'] ? 'selected' : '' }}>
                                {{ $type['name'] }}{{ $type['material_code'] ? ' (' . $type['material_code'] . ')' : '' }}
                            </option>
                        @endforeach
                        <option value="__new__" {{ old('battery_type') === '__new__' ? 'selected' : '' }}>+ Register new type...</option>
                    </select>
                </div>
                <div class="batt-field" id="newTypeFields" style="display:none;">
                    <label>New Type Name</label>
                    <input name="new_battery_type" id="b_new_type" value="{{ old('new_battery_type') }}" placeholder="e.g. Car Battery 12V 85A">
                </div>
                <div class="batt-field" id="newTypeCodeField" style="display:none;">
                    <label>New Type Material Code</label>
                    <input name="new_battery_type_code" id="b_new_type_code" value="{{ old('new_battery_type_code') }}" placeholder="e.g. 1000000503">
                </div>
                <div class="batt-field">
                    <label>Battery Brand</label>
                    <select name="brand" id="b_brand" onchange="toggleBatteryBrandFields()">
                        @foreach($brands as $key => $label)
                            <option value="{{ $key }}" {{ old('brand') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                        <option value="__new__" {{ old('brand') === '__new__' ? 'selected' : '' }}>+ Register new brand...</option>
                    </select>
                </div>
                <div class="batt-field" id="newBrandField" style="display:none;">
                    <label>New Brand Name</label>
                    <input name="new_battery_brand" id="b_new_brand" value="{{ old('new_battery_brand') }}" placeholder="e.g. Yuasa">
                </div>
                <div class="batt-field">
                    <label>Plate No</label>
                    <input name="plate_number" id="b_plate" value="{{ old('plate_number') }}" placeholder="AA-123-BB" required>
                </div>
                <div class="batt-field">
                    <label>Driver Name</label>
                    <select name="driver_name" id="b_driver" onchange="toggleBatteryDriverFields()">
                        <option value="">Select driver</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->name }}" {{ old('driver_name') === $driver->name ? 'selected' : '' }}>{{ $driver->name }}</option>
                        @endforeach
                        <option value="__new__" {{ old('driver_name') === '__new__' ? 'selected' : '' }}>+ Register new driver...</option>
                    </select>
                </div>
                <div class="batt-field" id="newDriverField" style="display:none;">
                    <label>New Driver Name</label>
                    <input name="new_driver_name" id="b_new_driver" value="{{ old('new_driver_name') }}" placeholder="e.g. Abebe Kebede">
                </div>
                <div class="batt-field">
                    <label>Initial KM</label>
                    <input type="number" name="initial_kilometer" id="b_initkm" value="{{ old('initial_kilometer', 0) }}" min="0">
                </div>
                <div class="batt-field">
                    <label>Return KM</label>
                    <input type="number" name="return_kilometer" id="b_retkm" value="{{ old('return_kilometer', 0) }}" min="0">
                </div>
                <div class="batt-field">
                    <label>Difference (KM)</label>
                    <input type="text" id="b_diff" value="0" readonly style="background:#f3f7fc; font-weight:700; color:#166534;">
                    <div class="batt-field-error" id="b_diff_warning" style="display:none;">Return KM cannot be less than Initial KM.</div>
                </div>
                <div class="batt-field">
                    <label>Battery Status</label>
                    <select name="status" id="b_status">
                        @foreach($batteryStatuses as $key => $label)
                            <option value="{{ $key }}" {{ old('status', 'in_service') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="batt-form-actions">
                <button type="submit" class="batt-btn">💾 Save Battery</button>
                <button type="button" class="batt-btn secondary" onclick="closeBatteryModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: DELETE CONFIRMATION -->
<div class="batt-modal-bg" id="deleteModal">
    <div class="batt-modal" style="max-width: 420px;">
        <h3>🗑️ Delete Battery</h3>
        <p class="batt-delete-msg">
            Are you sure you want to delete battery <strong id="deleteSerial">—</strong>?
            This action cannot be undone.
        </p>
        <form method="POST" id="deleteForm">
            @csrf
            @method('DELETE')
            <div class="batt-form-actions">
                <button type="submit" class="batt-btn danger">Yes, Delete</button>
                <button type="button" class="batt-btn secondary" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: RETURN BATTERY -->
<div class="batt-modal-bg {{ ($errors->has('return_serial') || $errors->has('returned_kilometer')) ? 'open' : '' }}" id="returnModal">
    <div class="batt-modal" style="max-width: 460px;">
        <h3>↩️ Return Battery</h3>
        <p class="batt-delete-msg">
            Registered serial: <strong id="returnSerialDisplay">—</strong><br>
            The battery being returned must carry this exact serial number. If it does not match, the return is rejected.
        </p>
        <form method="POST" action="{{ old('return_action') }}" id="returnForm">
            @csrf
            @method('PATCH')
            <input type="hidden" name="return_action" id="returnActionInput" value="{{ old('return_action') }}">
            <input type="hidden" name="return_serial_expected" id="returnSerialInput" value="{{ old('return_serial_expected') }}">
            <input type="hidden" name="return_initial" id="return_initial" value="{{ old('return_initial', 0) }}">
            <div class="batt-form-grid" style="grid-template-columns: 1fr;">
                <div class="batt-field">
                    <label>Confirm Serial Number</label>
                    <input name="return_serial" id="return_serial" value="{{ old('return_serial') }}" placeholder="Type the serial number exactly as registered" autocomplete="off">
                    <div class="batt-field-error" id="return_serial_error" style="display:none;">Serial number does not match the registered battery. Return rejected.</div>
                    @error('return_serial')
                    <div class="batt-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="batt-field">
                    <label>Return KM</label>
                    <input type="number" name="returned_kilometer" id="returned_kilometer" value="{{ old('returned_kilometer') }}" min="0" placeholder="e.g. 87500">
                    <div class="batt-field-error" id="return_km_error" style="display:none;">Return KM cannot be less than Initial KM.</div>
                    @error('returned_kilometer')
                    <div class="batt-field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="batt-form-actions">
                <button type="submit" class="batt-btn">Confirm Return</button>
                <button type="button" class="batt-btn secondary" onclick="closeReturnModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: HISTORY -->
<div class="batt-modal-bg" id="historyModal">
    <div class="batt-modal" style="max-width: 760px;">
        <h3>🕘 Battery History: <span id="historySerial">—</span></h3>
        <div class="batt-history-scroll" id="historyContent">
            <div class="batt-history-loading">Loading…</div>
        </div>
        <div class="batt-form-actions">
            <button type="button" class="batt-btn secondary" onclick="closeHistoryModal()">Close</button>
        </div>
    </div>
</div>

@foreach($statCards as $card)
    @php $modalBatteries = $statGroups[$card['key']]; @endphp
    <div class="bb-modal-bg" id="{{ $card['id'] }}" role="dialog" aria-modal="true" aria-labelledby="{{ $card['id'] }}Title">
        <div class="bb-modal">
            <div class="bb-modal-head">
                <div>
                    <h3 id="{{ $card['id'] }}Title">{{ $card['label'] }}</h3>
                    <span>{{ $modalBatteries->count() }} record(s)</span>
                </div>
                <button type="button" class="bb-modal-close" aria-label="Close" data-bb-close>×</button>
            </div>
            <div class="bb-modal-body">
                @if($modalBatteries->isEmpty())
                    <div class="bb-empty">No batteries in this group.</div>
                @else
                    <div class="bb-table-wrap">
                        <table class="bb-table">
                            <thead>
                                <tr>
                                    <th class="bb-roll">No</th>
                                    <th>Serial No</th>
                                    <th>Material Code</th>
                                    <th>Battery Type</th>
                                    <th>Brand</th>
                                    <th>Plate No</th>
                                    <th>Driver</th>
                                    <th>Initial KM</th>
                                    <th>Return KM</th>
                                    <th>Difference</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modalBatteries as $battery)
                                    <tr>
                                        <td class="bb-roll">{{ $loop->iteration }}</td>
                                        <td class="whitespace-nowrap font-semibold">{{ $battery->serial_number }}</td>
                                        <td>{{ $battery->material_code ?: '—' }}</td>
                                        <td>{{ $battery->battery_type }}</td>
                                        <td>{{ $battery->brand }}</td>
                                        <td>{{ $battery->plate_number }}</td>
                                        <td>{{ $battery->driver_name ?: '—' }}</td>
                                        <td>{{ number_format($battery->initial_kilometer) }}</td>
                                        <td>{{ number_format($battery->return_kilometer) }}</td>
                                        <td>
                                            @php $bbDiff = (int) $battery->return_kilometer - (int) $battery->initial_kilometer; @endphp
                                            {{ $bbDiff > 0 ? '+' . number_format($bbDiff) : number_format($bbDiff) }}
                                        </td>
                                        <td>
                                            <span class="batt-status {{ $battery->status === 'returned' ? 'returned' : 'in-service' }}">
                                                {{ $battery->getStatusDisplayName() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="bb-modal-foot">
                <span class="bb-modal-range"></span>
                <div class="bb-modal-pages"></div>
            </div>
        </div>
    </div>
@endforeach

<div class="bb-modal-bg" id="bbModalPrint" role="dialog" aria-modal="true" aria-labelledby="bbPrintTitle">
    <div class="bb-modal bb-modal-sm">
        <div class="bb-modal-head">
            <div>
                <h3 id="bbPrintTitle">Print Batteries</h3>
                <span>Choose which sections appear on the printed page</span>
            </div>
            <button type="button" class="bb-modal-close" aria-label="Close" data-bb-close>×</button>
        </div>
        <div class="bb-modal-body">
            <div class="bb-print-options">
                <label><input type="checkbox" class="bb-print-check" value="cards"> Summary cards</label>
                <label><input type="checkbox" class="bb-print-check" value="table" checked> Batteries table</label>
            </div>
            <p id="bbPrintHint" class="bb-print-hint" style="display:none;">Select at least one section to print.</p>
        </div>
        <div class="bb-modal-foot" style="display:flex;">
            <span></span>
            <div class="bb-modal-pages">
                <button type="button" class="bb-action-btn" data-bb-close>Cancel</button>
                <button type="button" class="inline-flex items-center px-3 py-1.5 bg-[#1a6fc4] text-white text-xs font-semibold rounded-lg hover:bg-[#155a9e]" id="bbPrintGo">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const battModal = document.getElementById('battModal');
    const batteryForm = document.getElementById('batteryForm');
    const batteryId = document.getElementById('batteryId');
    const batteryMethod = document.getElementById('batteryMethod');
    const battModalTitle = document.getElementById('battModalTitle');
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const returnModal = document.getElementById('returnModal');
    const returnForm = document.getElementById('returnForm');
    const historyModal = document.getElementById('historyModal');

    function escapeHtml(value) {
        return String(value === null || value === undefined ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatKm(value) {
        return (value === null || value === undefined) ? '—' : Number(value).toLocaleString();
    }

    function openHistoryModal(button) {
        const d = button.dataset;
        const content = document.getElementById('historyContent');

        document.getElementById('historySerial').textContent = d.serial || '';
        content.innerHTML = '<div class="batt-history-loading">Loading history…</div>';
        historyModal.classList.add('open');

        fetch(d.action, { headers: { 'Accept': 'application/json' } })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                const rows = Array.isArray(data.history) ? data.history : [];
                let html = '<div class="batt-history-list">';

                if (rows.length === 0) {
                    html += '<div class="batt-history-loading">No history found</div>';
                } else {
                    rows.forEach(function (item) {
                        html += '<div class="batt-history-item">' +
                            '<div class="batt-history-top">' +
                                '<span class="batt-history-action">' + escapeHtml(item.action) + '</span>' +
                                '<span class="batt-history-date">' + escapeHtml(item.date) + '</span>' +
                            '</div>' +
                            '<div class="batt-history-meta">' +
                                'Status: ' + escapeHtml(item.status) +
                                ' &middot; Driver: ' + escapeHtml(item.driver) +
                                ' &middot; KM: ' + escapeHtml(formatKm(item.initial_km)) +
                                ' &rarr; ' + escapeHtml(formatKm(item.return_km)) +
                            '</div>' +
                            (item.notes ? '<div class="batt-history-notes">' + escapeHtml(item.notes) + '</div>' : '') +
                            '<div class="batt-history-user">by ' +
                                '<span class="batt-history-user-name">' + escapeHtml(item.user) + '</span>' +
                                (item.email ? ' &middot; ' + escapeHtml(item.email) : '') +
                                (item.role ? ' <span class="batt-history-role">(' + escapeHtml(item.role) + ')</span>' : '') +
                            '</div>' +
                        '</div>';
                    });
                }

                html += '</div>';
                content.innerHTML = html;
            })
            .catch(function (err) {
                content.innerHTML = '<div class="batt-history-loading" style="color:#b91c1c;">' +
                    'Error loading history: ' + escapeHtml(err.message) + '</div>';
            });
    }

    function closeHistoryModal() {
        historyModal.classList.remove('open');
    }

    function openDeleteModal(button) {
        deleteForm.action = button.dataset.action;
        document.getElementById('deleteSerial').textContent = button.dataset.serial || '';
        deleteModal.classList.add('open');
    }

    function closeDeleteModal() {
        deleteModal.classList.remove('open');
    }

    function openReturnModal(button) {
        const d = button.dataset;
        returnForm.action = d.action;
        document.getElementById('returnActionInput').value = d.action;
        document.getElementById('returnSerialInput').value = d.serial || '';
        document.getElementById('returnSerialDisplay').textContent = d.serial || '—';
        document.getElementById('return_initial').value = d.init || 0;
        document.getElementById('return_serial').value = '';
        document.getElementById('returned_kilometer').value = '';
        document.getElementById('return_serial_error').style.display = 'none';
        document.getElementById('return_km_error').style.display = 'none';
        returnModal.classList.add('open');
        setTimeout(function () { document.getElementById('return_serial').focus(); }, 50);
    }

    function closeReturnModal() {
        returnModal.classList.remove('open');
    }

    function openBatteryModal() {
        battModal.classList.add('open');
    }

    function closeBatteryModal() {
        battModal.classList.remove('open');
        resetBatteryForm();
    }

    function resetBatteryForm() {
        batteryForm.action = batteryForm.dataset.storeUrl;
        batteryId.value = '';
        batteryMethod.disabled = true;
        battModalTitle.textContent = '🔋 Give Battery';
        batteryForm.reset();
        document.getElementById('b_type').value = '';
        document.getElementById('b_material').value = '';
        document.getElementById('b_new_type').value = '';
        document.getElementById('b_new_type_code').value = '';
        document.getElementById('b_new_brand').value = '';
        document.getElementById('b_new_driver').value = '';
        document.getElementById('b_initkm').value = 0;
        document.getElementById('b_retkm').value = 0;
        document.getElementById('b_status').value = 'in_service';
        syncBatteryDifference();
        toggleBatteryTypeFields();
        toggleBatteryBrandFields();
        toggleBatteryDriverFields();
    }

    function toggleBatteryTypeFields() {
        const typeIsNew = document.getElementById('b_type').value === '__new__';
        const materialIsNew = document.getElementById('b_material').value === '__new__';
        const isNew = typeIsNew || materialIsNew;
        document.getElementById('newTypeFields').style.display = isNew ? '' : 'none';
        document.getElementById('newTypeCodeField').style.display = isNew ? '' : 'none';
        document.getElementById('b_new_type_code').required = materialIsNew;
    }

    function toggleBatteryBrandFields() {
        const isNew = document.getElementById('b_brand').value === '__new__';
        document.getElementById('newBrandField').style.display = isNew ? '' : 'none';
    }

    function toggleBatteryDriverFields() {
        const isNew = document.getElementById('b_driver').value === '__new__';
        document.getElementById('newDriverField').style.display = isNew ? '' : 'none';
    }

    function syncBatteryMaterial() {
        const select = document.getElementById('b_type');
        const option = select.options[select.selectedIndex];
        const material = document.getElementById('b_material');

        if (select.value === '__new__') {
            material.value = '__new__';
        } else {
            const code = option && option.dataset.code ? option.dataset.code : '';

            if (code && !Array.from(material.options).some(function (listed) { return listed.value === code; })) {
                const added = new Option(code + ' — ' + option.value, code);
                added.dataset.type = option.value;
                material.add(added);
            }

            material.value = code;
        }

        toggleBatteryTypeFields();
    }

    function syncBatteryTypeFromMaterial() {
        const material = document.getElementById('b_material');
        const select = document.getElementById('b_type');

        if (material.value === '__new__') {
            select.value = '__new__';
        } else {
            const option = material.options[material.selectedIndex];
            const typeName = option && option.dataset.type ? option.dataset.type : '';

            if (typeName && Array.from(select.options).some(function (listed) { return listed.value === typeName; })) {
                select.value = typeName;
            }
        }

        toggleBatteryTypeFields();
    }

    function syncBatteryDifference() {
        const initial = Number(document.getElementById('b_initkm').value) || 0;
        const returned = Number(document.getElementById('b_retkm').value) || 0;
        const difference = returned - initial;
        const invalid = returned > 0 && returned < initial;
        const field = document.getElementById('b_diff');

        field.value = (difference > 0 ? '+' : '') + difference.toLocaleString();
        field.style.color = (invalid || difference < 0) ? '#b91c1c' : '#166534';
        document.getElementById('b_diff_warning').style.display = invalid ? 'block' : 'none';
    }

    function editBattery(button) {
        const d = button.dataset;
        batteryForm.action = d.action;
        batteryId.value = d.action.split('/').pop();
        batteryMethod.disabled = false;
        battModalTitle.textContent = '🔋 Edit Battery';

        const select = document.getElementById('b_type');
        if (d.type && !Array.from(select.options).some(function (option) { return option.value === d.type; })) {
            const option = new Option(d.type, d.type, true, true);
            option.dataset.code = d.material || '';
            select.add(option);
        }
        select.value = d.type || '';

        const materialSelect = document.getElementById('b_material');
        if (d.material && !Array.from(materialSelect.options).some(function (option) { return option.value === d.material; })) {
            const materialOption = new Option(d.material + (d.type ? ' — ' + d.type : ''), d.material);
            if (d.type) {
                materialOption.dataset.type = d.type;
            }
            materialSelect.add(materialOption);
        }
        materialSelect.value = d.material || '';

        const driverSelect = document.getElementById('b_driver');
        if (d.driver && !Array.from(driverSelect.options).some(function (option) { return option.value === d.driver; })) {
            driverSelect.add(new Option(d.driver, d.driver, true, true));
        }
        driverSelect.value = d.driver || '';

        document.getElementById('b_date').value = d.date || '';
        document.getElementById('b_serial').value = d.serial || '';
        document.getElementById('b_brand').value = d.brand || '';
        document.getElementById('b_plate').value = d.plate || '';
        document.getElementById('b_initkm').value = d.init || 0;
        document.getElementById('b_retkm').value = d.ret || 0;
        document.getElementById('b_status').value = d.status || 'in_service';

        syncBatteryDifference();
        toggleBatteryTypeFields();
        toggleBatteryBrandFields();
        toggleBatteryDriverFields();

        openBatteryModal();
    }

    battModal.addEventListener('click', function (event) {
        if (event.target === battModal) {
            closeBatteryModal();
        }
    });

    deleteModal.addEventListener('click', function (event) {
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    });

    returnModal.addEventListener('click', function (event) {
        if (event.target === returnModal) {
            closeReturnModal();
        }
    });

    returnForm.addEventListener('submit', function (event) {
        const expected = document.getElementById('returnSerialInput').value;
        const entered = document.getElementById('return_serial').value.trim();
        const initial = Number(document.getElementById('return_initial').value) || 0;
        const returned = Number(document.getElementById('returned_kilometer').value) || 0;

        if (entered !== expected) {
            event.preventDefault();
            document.getElementById('return_serial_error').style.display = 'block';
            document.getElementById('return_serial').focus();
            return;
        }

        document.getElementById('return_serial_error').style.display = 'none';

        if (returned < initial) {
            event.preventDefault();
            document.getElementById('return_km_error').style.display = 'block';
            document.getElementById('returned_kilometer').focus();
        }
    });

    document.getElementById('return_serial').addEventListener('input', function () {
        document.getElementById('return_serial_error').style.display = 'none';
    });
    document.getElementById('returned_kilometer').addEventListener('input', function () {
        document.getElementById('return_km_error').style.display = 'none';
    });

    historyModal.addEventListener('click', function (event) {
        if (event.target === historyModal) {
            closeHistoryModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            if (battModal.classList.contains('open')) {
                closeBatteryModal();
            }
            if (deleteModal.classList.contains('open')) {
                closeDeleteModal();
            }
            if (returnModal.classList.contains('open')) {
                closeReturnModal();
            }
            if (historyModal.classList.contains('open')) {
                closeHistoryModal();
            }
        }
    });

    document.getElementById('b_date').value = document.getElementById('b_date').value || new Date().toISOString().split('T')[0];

    document.getElementById('b_initkm').addEventListener('input', syncBatteryDifference);
    document.getElementById('b_retkm').addEventListener('input', syncBatteryDifference);
    syncBatteryDifference();

    batteryForm.addEventListener('submit', function (event) {
        const initial = Number(document.getElementById('b_initkm').value) || 0;
        const returned = Number(document.getElementById('b_retkm').value) || 0;
        if (returned > 0 && returned < initial) {
            event.preventDefault();
            document.getElementById('b_retkm').focus();
        }
    });

    toggleBatteryTypeFields();
    toggleBatteryBrandFields();
    toggleBatteryDriverFields();

    if (returnModal.classList.contains('open')) {
        returnForm.action = document.getElementById('returnActionInput').value;
        document.getElementById('returnSerialDisplay').textContent = document.getElementById('returnSerialInput').value || '—';
    }

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
            if (!document.querySelector('.bb-modal-bg.open') && !document.querySelector('.batt-modal-bg.open')) {
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
            var table = modal.querySelector('.bb-modal-body .bb-table');
            if (!table) return;

            var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
            if (!rows.length) return;

            var foot = modal.querySelector('.bb-modal-foot');
            var rangeNode = foot && foot.querySelector('.bb-modal-range');
            var pagesNode = foot && foot.querySelector('.bb-modal-pages');
            if (!foot || !rangeNode || !pagesNode) return;

            var state = modal.bbPager;
            if (!state) {
                state = modal.bbPager = { rows: rows, page: 1, foot: foot, range: rangeNode, pages: pagesNode };
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
                    dots.className = 'bb-page-dots';
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
                var active = state.pages.querySelector('.bb-active');
                if (active) active.focus();
            }
        }

        function pagerButton(label, disabled, active, onClick) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'bb-page-btn' + (active ? ' bb-active' : '');
            button.textContent = label;
            button.disabled = disabled;
            button.addEventListener('click', onClick);
            return button;
        }

        function goToModalPage(state, page) {
            state.page = page;
            paintModalPage(state, true);
            var body = state.foot.parentNode.querySelector('.bb-modal-body');
            if (body) body.scrollTop = 0;
        }

        var printModal = document.getElementById('bbModalPrint');
        var printChecks = Array.prototype.slice.call(document.querySelectorAll('.bb-print-check'));
        var printHint = document.getElementById('bbPrintHint');

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

        Array.prototype.forEach.call(document.querySelectorAll('[data-bb-modal]'), function (button) {
            button.addEventListener('click', function () {
                var modal = document.getElementById(button.getAttribute('data-bb-modal'));
                initModalPager(modal);
                openModalEl(modal);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('.bb-modal-bg'), function (backdrop) {
            backdrop.addEventListener('click', function (event) {
                if (event.target === backdrop) closeModal(backdrop);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-bb-close]'), function (button) {
            button.addEventListener('click', function () {
                closeModal(button.closest('.bb-modal-bg'));
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-bb-print-options]'), function (button) {
            button.addEventListener('click', function () {
                openPrintOptions(button.getAttribute('data-print-default'));
            });
        });

        var printGo = document.getElementById('bbPrintGo');
        if (printGo) {
            printGo.addEventListener('click', printSelectedSections);
        }

        var tipButton = document.querySelector('[data-bb-tip]');
        var tipBox = document.getElementById('tipBatteries');
        if (tipButton && tipBox) {
            tipButton.addEventListener('click', function () {
                var open = tipBox.classList.toggle('open');
                tipButton.setAttribute('aria-expanded', open ? 'true' : 'false');
                tipButton.classList.toggle('bb-action-btn-active', open);
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;
            var open = document.querySelectorAll('.bb-modal-bg.open');
            if (open.length) {
                closeModal(open[open.length - 1]);
            }
        });
    })();
</script>
@endsection
