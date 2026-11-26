@extends('layouts.app')

@section('title', 'Tire Tracking Reports')

@section('page-title', 'Tire Tracking Reports')

@section('styles')
<style id="tireReportStyles">
    :root {
        --epss-blue: #1C75BC;
        --epss-blue-dark: #1664a0;
        --epss-blue-light: #E8F4FD;
        --success: #10B981;
        --success-light: #D1FAE5;
        --warning: #F59E0B;
        --warning-light: #FEF3C7;
        --danger: #EF4444;
        --danger-light: #FEE2E2;
        --info: #3B82F6;
        --info-light: #DBEAFE;
        --purple: #8B5CF6;
        --purple-light: #F3E8FF;
        --tsr-bg: #f1f5f9;
        --tsr-surface: #ffffff;
        --tsr-text-main: #0f172a;
        --tsr-text-muted: #64748b;
        --tsr-text-light: #94a3b8;
        --tsr-primary: #0284c7;
        --tsr-primary-light: #e0f2fe;
        --tsr-success: #10b981;
        --tsr-danger: #ef4444;
        --tsr-border: #e2e8f0;
        --tsr-radius-sm: 8px;
        --tsr-radius-md: 12px;
        --tsr-shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.05), 0 1px 2px -1px rgb(0 0 0 / 0.05);
        --tsr-font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .tsr-page {
        width: 100%;
        max-width: 1350px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
        background: var(--tsr-bg);
        color: var(--tsr-text-main);
        font-family: var(--tsr-font-sans);
        -webkit-font-smoothing: antialiased;
    }

    .tsr-page h1,
    .tsr-page h2,
    .tsr-page h3,
    .tsr-page h4,
    .tsr-page p { margin: 0; }

    .report-header {
        background: var(--tsr-surface);
        color: var(--tsr-text-main);
        border: 1px solid var(--tsr-border);
        border-radius: var(--tsr-radius-md);
        box-shadow: var(--tsr-shadow-sm);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.25rem;
    }

    .report-header-content { position: relative; }

    .report-header h2 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--tsr-text-main);
        margin-bottom: 0.25rem;
    }

    .report-generated {
        font-size: 0.85rem;
        color: var(--tsr-text-muted);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .stat-card {
        background: var(--tsr-surface);
        border-radius: var(--tsr-radius-md);
        padding: 1.15rem 1.25rem;
        border: 1px solid var(--tsr-border);
        border-left: 4px solid var(--card-color, var(--tsr-primary));
        box-shadow: var(--tsr-shadow-sm);
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        box-shadow: 0 6px 16px rgb(15 23 42 / 0.08);
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }

    .stat-card:focus-visible {
        outline: 2px solid var(--tsr-primary);
        outline-offset: 2px;
    }

    .stat-card .stat-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: var(--card-color, var(--tsr-primary));
        background: transparent;
        opacity: 0.2;
        flex: none;
    }

    .stat-card .stat-value {
        font-size: 1.7rem;
        font-weight: 700;
        color: var(--tsr-text-main);
        line-height: 1;
        margin: 0.2rem 0;
    }

    .stat-card .stat-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--tsr-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-card .stat-trend {
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .stat-trend.positive { color: var(--success); }
    .stat-trend.negative { color: var(--danger); }
    .stat-trend.neutral { color: #64748B; }

    .chart-card {
        background: var(--tsr-surface);
        border-radius: var(--tsr-radius-md);
        border: 1px solid var(--tsr-border);
        padding: 1.5rem;
        box-shadow: var(--tsr-shadow-sm);
        height: 100%;
    }

    .chart-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--tsr-border);
    }

    .chart-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--tsr-text-main);
        margin: 0;
    }

    .chart-card-actions {
        display: flex;
        gap: 0.5rem;
    }

    .chart-card .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .table-card {
        background: var(--tsr-surface);
        border-radius: var(--tsr-radius-md);
        border: 1px solid var(--tsr-border);
        box-shadow: var(--tsr-shadow-sm);
        overflow: hidden;
    }

    .table-card-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--tsr-border);
        background: var(--tsr-surface);
    }

    .table-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--tsr-text-main);
        margin: 0;
    }

    .table-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
    }

    .filter-input {
        padding: 0.55rem 0.875rem;
        border: 1px solid var(--tsr-border);
        border-radius: var(--tsr-radius-sm);
        font-size: 0.85rem;
        background: var(--tsr-surface);
        color: var(--tsr-text-main);
        min-width: 200px;
        transition: all 0.15s ease;
    }

    .filter-input:focus {
        outline: none;
        border-color: var(--tsr-primary);
        box-shadow: 0 0 0 3px var(--tsr-primary-light);
    }

    .filter-select {
        padding: 0.55rem 2rem 0.55rem 0.875rem;
        border: 1px solid var(--tsr-border);
        border-radius: var(--tsr-radius-sm);
        font-size: 0.85rem;
        background-color: var(--tsr-surface);
        color: var(--tsr-text-main);
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%2364748B' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 14px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
    }

    .data-table thead th {
        background: #f8fafc;
        color: var(--tsr-text-muted);
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.8rem 1rem;
        text-align: left;
        border-bottom: 1px solid var(--tsr-border);
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .data-table tbody td {
        padding: 0.8rem 1rem;
        color: var(--tsr-text-main);
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .data-table tbody tr {
        transition: background-color 0.15s ease;
    }

    .data-table tbody tr:hover {
        background-color: #F8FAFC;
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .status-badge.active { background: var(--success-light); color: var(--success); }
    .status-badge.inactive { background: #F1F5F9; color: #64748B; }
    .status-badge.returned { background: var(--success-light); color: var(--success); }
    .status-badge.in-use { background: var(--warning-light); color: var(--warning); }
    .status-badge.pending { background: var(--info-light); color: var(--info); }

    .km-diff {
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
        font-weight: 600;
    }

    .km-diff.positive { color: var(--success); }
    .km-diff.negative { color: var(--danger); }

    .modal-content {
        border: 1px solid var(--tsr-border);
        border-radius: var(--tsr-radius-md);
        box-shadow: 0 24px 60px rgba(8, 25, 45, 0.35);
        overflow: hidden;
    }

    .modal-xl-custom {
        max-width: 1200px;
    }

    .modal-header-custom {
        padding: 1rem 1.5rem;
        background: var(--tsr-surface);
        color: var(--tsr-text-main);
        border-bottom: 1px solid var(--tsr-border);
    }

    .modal-header-custom .modal-title {
        font-size: 1.05rem;
        font-weight: 700;
    }

    .modal-body-custom {
        padding: 1.5rem;
        max-height: 70vh;
        overflow-y: auto;
    }

    .modal-footer-custom {
        padding: 0.85rem 1.5rem;
        border-top: 1px solid var(--tsr-border);
        background: #f8fafc;
    }

    .btn-primary-custom,
    .btn-secondary-custom,
    .btn-outline-custom {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.55rem 1rem;
        border-radius: var(--tsr-radius-sm);
        font-weight: 600;
        font-size: 0.85rem;
        line-height: 1.2;
        transition: all 0.15s ease;
    }

    .btn-primary-custom {
        background: var(--tsr-primary);
        border-color: var(--tsr-primary);
        color: white;
    }

    .btn-primary-custom:hover {
        background: #0369a1;
        border-color: #0369a1;
        color: white;
    }

    .btn-secondary-custom {
        background: var(--tsr-surface);
        border-color: var(--tsr-border);
        color: var(--tsr-text-main);
        box-shadow: var(--tsr-shadow-sm);
    }

    .btn-secondary-custom:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: var(--tsr-text-main);
    }

    .btn-outline-custom {
        background: transparent;
        border-color: var(--tsr-primary);
        color: var(--tsr-primary);
    }

    .btn-outline-custom:hover {
        background: var(--tsr-primary);
        border-color: var(--tsr-primary);
        color: white;
    }

    .insight-card {
        background: var(--tsr-surface);
        border-radius: var(--tsr-radius-md);
        border: 1px solid var(--tsr-border);
        padding: 1.25rem 1.5rem;
        box-shadow: var(--tsr-shadow-sm);
    }

    .insight-card .insight-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--tsr-radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        color: white;
        flex: none;
    }

    .insight-card .insight-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--tsr-text-main);
        margin: 0 0 0.25rem;
    }

    .insight-card .insight-text {
        font-size: 0.8125rem;
        color: var(--tsr-text-muted);
        margin: 0;
        line-height: 1.5;
    }

    .date-range-picker {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .date-range-picker .date-input {
        padding: 0.55rem 0.875rem;
        border: 1px solid var(--tsr-border);
        border-radius: var(--tsr-radius-sm);
        font-size: 0.85rem;
        background: var(--tsr-surface);
        color: var(--tsr-text-main);
        min-width: 150px;
    }

    .date-range-picker .date-separator {
        color: #9CA3AF;
        font-size: 0.8125rem;
    }

    .loading-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: inherit;
    }

    .loading-spinner {
        width: 32px;
        height: 32px;
        border: 3px solid #E2E8F0;
        border-top-color: var(--epss-blue);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .print-only { display: none; }
    .screen-only { display: block; }

    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        .print-only { display: block !important; }
        .screen-only { display: none !important; }
        .report-header { background: #fff !important; color: #0f172a !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .stat-card, .chart-card, .table-card, .insight-card { break-inside: avoid; page-break-inside: avoid; }
        .modal { display: none !important; }
        .no-print { display: none !important; }
        body { background: white !important; }
        .app-shell, .app-main, .main-content, .page-body, .tsr-page { padding: 0 !important; margin: 0 !important; }
        .tsr-page { max-width: none !important; background: #fff !important; }
        .sidebar, .topbar, .page-footer { display: none !important; }
        /* Only sections ticked in the Print Report dialog stay visible */
        .print-hide-section { display: none !important; }
        .date-range-picker, .table-filters, .report-pagination { display: none !important; }
        .table-responsive { overflow: visible !important; }
        .table-card { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead { display: table-header-group; }
        .data-table th, .data-table td { border: 1px solid #cbd5e1 !important; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }

    @media (max-width: 768px) {
        .tsr-page { padding: 1rem; }
        .report-header { padding: 1rem; }
        .table-card-header { flex-direction: column; align-items: stretch; }
        .table-filters { flex-direction: column; align-items: stretch; }
        .filter-input, .filter-select { width: 100%; }
        .chart-card .chart-container { height: 250px; }
        .date-range-picker { flex-wrap: wrap; }
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #94A3B8;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state p {
        margin: 0;
        font-size: 0.9375rem;
    }

    .report-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 0.85rem;
        font-size: 0.8125rem;
        color: var(--tsr-text-muted);
    }

    .report-meta-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .print-options-grid {
        display: grid;
        gap: 0.6rem;
    }

    .print-option {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.65rem 0.85rem;
        border: 1px solid #E2E8F0;
        border-radius: 0.5rem;
        background: #fff;
        cursor: pointer;
        font-size: 0.9rem;
        color: #334155;
    }

    .print-option:hover {
        background: #F8FAFC;
        border-color: #CBD5E1;
    }

    .print-option input {
        width: 1rem;
        height: 1rem;
        accent-color: #1C75BC;
        flex-shrink: 0;
    }

    .print-sections-hint {
        margin: 0.75rem 0 0;
        font-size: 0.85rem;
        color: #B91C1C;
    }

    .page-title-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .page-title-main h1 {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--tsr-text-main);
        margin: 0 0 0.25rem;
    }

    .page-title-main p {
        font-size: 0.95rem;
        color: var(--tsr-text-muted);
        margin: 0;
    }

    .insights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
</style>
@endsection

@section('content')
<div class="tsr-page" id="reportsPage">
    <!-- Page Title & Actions -->
    <div class="page-title-row screen-only">
        <div class="page-title-main">
            <h1>Tire Tracking Reports</h1>
            <p>Comprehensive overview of tire operations, utilization, and performance metrics</p>
        </div>
        <div class="action-buttons">
            @include('partials.type-of-report', ['reportTypeCurrent' => 'tire-reports'])
            <button type="button" class="btn btn-secondary-custom no-print" onclick="openPrintOptions()">
                <i class="fas fa-print mr-2"></i>Print Report
            </button>
            <button type="button" class="btn btn-primary-custom no-print" onclick="exportToPDF()">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </button>
            <button type="button" class="btn btn-outline-custom no-print" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-download mr-2"></i>Export Data
            </button>
        </div>
    </div>

    <!-- Report Header with Date Range -->
    <div class="report-header">
        <div class="report-header-content">
            <div class="flex flex-wrap items-center justify-between gap-1rem">
                <div>
                    <h2 class="text-xl font-bold mb-1">Tire Operations Report</h2>
                    <p class="report-generated">Generated: <span id="reportDate">{{ now()->format('F d, Y') }}</span></p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="date-range-picker">
                        <label for="dateFrom" class="sr-only">From Date</label>
                        <input type="date" id="dateFrom" class="date-input" value="{{ $dateFrom->format('Y-m-d') }}">
                        <span class="date-separator">to</span>
                        <label for="dateTo" class="sr-only">To Date</label>
                        <input type="date" id="dateTo" class="date-input" value="{{ $dateTo->format('Y-m-d') }}">
                        <button type="button" class="btn btn-primary-custom btn-sm" id="applyDateRange" style="padding: 0.5rem 1rem;">
                            <i class="fas fa-filter mr-1"></i>Apply
                        </button>
                    </div>
                    <button type="button" class="btn btn-secondary-custom no-print" onclick="resetDateRange()">
                        <i class="fas fa-undo mr-1"></i>Reset
                    </button>
                </div>
            </div>
            <div class="report-meta mt-4">
                <span class="report-meta-item">
                    <i class="fas fa-user"></i> Prepared by: {{ auth()->user()->name }}
                </span>
                <span class="report-meta-item">
                    <i class="fas fa-building"></i> Department: {{ auth()->user()->department ?? 'N/A' }}
                </span>
                <span class="report-meta-item">
                    <i class="fas fa-calendar-alt"></i> Period: <span id="periodText">{{ (request()->filled('date_from') || request()->filled('date_to')) ? $dateFrom->format('M d, Y').' - '.$dateTo->format('M d, Y') : 'Last 6 Months' }}</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Key Statistics -->
    <div class="stats-grid" role="region" aria-label="Key Statistics" data-print-section="stats">
        <div class="stat-card" style="--card-color: #1C75BC;" data-bs-toggle="modal" data-bs-target="#totalTiresModal" tabindex="0" role="button" aria-label="View all tires details">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label">Total Tires</p>
                    <p class="stat-value">{{ $totalTires }}</p>
                </div>
                <div class="stat-icon"><i class="fas fa-tire"></i></div>
            </div>
        </div>

        <div class="stat-card" style="--card-color: #10B981;" data-bs-toggle="modal" data-bs-target="#activeTiresModal" tabindex="0" role="button" aria-label="View active tires details">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label">Active Tires</p>
                    <p class="stat-value">{{ $activeTires }}</p>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>

        <div class="stat-card" style="--card-color: #F59E0B;" data-bs-toggle="modal" data-bs-target="#givenOutModal" tabindex="0" role="button" aria-label="View given out tires details">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label">Given Out (In Use)</p>
                    <p class="stat-value">{{ $givenOutTires }}</p>
                </div>
                <div class="stat-icon"><i class="fas fa-hand-holding"></i></div>
            </div>
        </div>

        <div class="stat-card" style="--card-color: #3B82F6;" data-bs-toggle="modal" data-bs-target="#givenTodayModal" tabindex="0" role="button" aria-label="View tires given today details">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label">Given Today</p>
                    <p class="stat-value">{{ $givenToday }}</p>
                </div>
                <div class="stat-icon"><i class="fas fa-plus"></i></div>
            </div>
        </div>

        <div class="stat-card" style="--card-color: #8B5CF6;" data-bs-toggle="modal" data-bs-target="#returnedTodayModal" tabindex="0" role="button" aria-label="View tires returned today details">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label">Returned Today</p>
                    <p class="stat-value">{{ $returnedToday }}</p>
                </div>
                <div class="stat-icon"><i class="fas fa-undo-alt"></i></div>
            </div>
        </div>

        <div class="stat-card" style="--card-color: #1C75BC;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label">Utilization Rate</p>
                    <p class="stat-value">{{ $totalTires > 0 ? round(($givenOutTires / $totalTires) * 100, 1) : 0 }}%</p>
                </div>
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            </div>
        </div>

        <div class="stat-card" style="--card-color: #6366F1;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label">Avg. Tires/Driver</p>
                    <p class="stat-value">{{ $topDrivers->count() > 0 ? round($givenOutTires / $topDrivers->count(), 1) : 0 }}</p>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>

    <!-- Insights Summary -->
    <div class="insights-grid" role="region" aria-label="Key Insights" data-print-section="insights">
        <div class="insight-card" data-insight="1" tabindex="0" role="button" aria-label="Monthly Activity Trend details">
            <div class="flex items-start gap-3">
                <div class="insight-icon" style="background: var(--epss-blue);"><i class="fas fa-chart-bar"></i></div>
                <div>
                    <h4 class="insight-title">Monthly Activity Trend</h4>
                    <p class="insight-text">Tire give/return operations over the last 6 months show higher give activity with 5 total gives and 2 returns.</p>
                </div>
            </div>
        </div>

        <div class="insight-card" data-insight="2" tabindex="0" role="button" aria-label="Tire Type Distribution details">
            <div class="flex items-start gap-3">
                <div class="insight-icon" style="background: var(--success);"><i class="fas fa-tire"></i></div>
                <div>
                    <h4 class="insight-title">Tire Type Distribution</h4>
                    <p class="insight-text">5 different tire types in inventory. Most common: 12R22.5 (4 units).</p>
                </div>
            </div>
        </div>

        <div class="insight-card" data-insight="3" tabindex="0" role="button" aria-label="Driver Utilization details">
            <div class="flex items-start gap-3">
                <div class="insight-icon" style="background: var(--warning);"><i class="fas fa-user-tie"></i></div>
                <div>
                    <h4 class="insight-title">Driver Utilization</h4>
                    <p class="insight-text">Top driver: Mesay Kebede with 1 active tires. 3 drivers currently have tires assigned.</p>
                </div>
            </div>
        </div>

        <div class="insight-card" data-insight="4" tabindex="0" role="button" aria-label="Attention Required details">
            <div class="flex items-start gap-3">
                <div class="insight-icon" style="background: var(--danger);"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <h4 class="insight-title">Attention Required</h4>
                    <p class="insight-text">3 tires currently in use. Monitor return schedules and kilometer readings.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Insight Detail Modal -->
    <div class="modal fade" id="insightModal" tabindex="-1" aria-labelledby="insightModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="insightModalHeader">
                    <h5 class="modal-title text-white" id="insightModalLabel">
                        <i class="fas fa-info-circle me-2"></i><span id="insightModalTitle">Insight Details</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="insightModalBody">
                    <!-- Dynamic content loaded via JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8" role="region" aria-label="Charts" data-print-section="charts">
        <!-- Monthly Activity Chart -->
        <div class="chart-card">
            <div class="chart-card-header">
                <h3 class="chart-card-title">Monthly Activity (Last 6 Months)</h3>
                <div class="chart-card-actions">
                    <button type="button" class="btn btn-secondary-custom btn-sm no-print" onclick="downloadChart('monthlyChart', 'monthly-activity')">
                        <i class="fas fa-download mr-1"></i>Download
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="monthlyChart" aria-label="Monthly tire gives and returns bar chart"></canvas>
            </div>
        </div>

        <!-- Tire Type Distribution -->
        <div class="chart-card">
            <div class="chart-card-header">
                <h3 class="chart-card-title">Tire Type Distribution</h3>
                <div class="chart-card-actions">
                    <button type="button" class="btn btn-secondary-custom btn-sm no-print" onclick="downloadChart('typeChart', 'tire-type-distribution')">
                        <i class="fas fa-download mr-1"></i>Download
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="typeChart" aria-label="Tire type distribution doughnut chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Gives Table -->
    <div class="table-card mb-8" role="region" aria-label="Recent Tire Gives" data-print-section="gives">
        <div class="table-card-header">
            <h3 class="table-card-title"><i class="fas fa-arrow-up text-blue-600 mr-2"></i>Recent Tire Gives</h3>
            <div class="table-filters">
                <input type="text" class="filter-input" id="givesSearch" placeholder="Search tires, drivers, serial..." aria-label="Search recent gives">
                <select class="filter-select" id="givesStatusFilter" aria-label="Filter by status">
                    <option value="">All Status</option>
                    <option value="in-use">In Use</option>
                    <option value="returned">Returned</option>
                </select>
                <button type="button" class="btn btn-secondary-custom btn-sm no-print" onclick="exportTable('givesTable', 'recent-gives')">
                    <i class="fas fa-file-excel mr-1"></i>Export
                </button>
                <button type="button" class="table-tip-btn" data-table-tip aria-expanded="false" aria-controls="tipTireReports"><i class="fas fa-info-circle"></i> TIP: info</button>
            </div>
        </div>
        <div id="tipTireReports" class="table-tip-panel no-print">
            <h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4>
            <ul>
                <li>Apply reloads every section for the chosen From/To dates; Reset restores the last six months.</li>
                <li>KPI cards (Total, Active, Given Out, Given Today, Returned Today) open modals listing the tires behind each number.</li>
                <li>Each insight card opens a modal with key metrics and a per-item breakdown for that finding.</li>
                <li>Charts show monthly gives versus returns and tire type distribution; each Download saves the chart as a PNG.</li>
                <li>Recent Gives filters by search and status; Recent Returns filters by positive, negative or zero KM difference.</li>
                <li>Print Report lets you tick sections (gives, returns, stats, insights, charts); table Export downloads visible rows as CSV.</li>
            </ul>
        </div>
        <div class="table-responsive">
            <table class="data-table" id="givesTable">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Date</th>
                        <th scope="col">Tire Type</th>
                        <th scope="col">Serial Number</th>
                        <th scope="col">Driver</th>
                        <th scope="col">KM at Give</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentGives as $index => $give)
                    <tr class="table-row">
                        <td class="font-mono text-gray-500">{{ $recentGives->firstItem() + $index }}</td>
                        <td>{{ $give->give_date->format('Y-m-d') }}</td>
                        <td class="font-medium text-gray-800">{{ $give->tire->tire_type }}</td>
                        <td class="font-mono text-gray-600">{{ $give->tire->serial_number }}</td>
                        <td>{{ $give->driver->name }}</td>
                        <td class="font-mono">{{ number_format($give->given_kilometer) }}</td>
                        <td>
                            @if($give->is_returned)
                                <span class="status-badge returned"><i class="fas fa-check mr-1"></i>Returned</span>
                            @else
                                <span class="status-badge in-use"><i class="fas fa-clock mr-1"></i>In Use</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>No recent tire gives found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination for Recent Gives -->
        <div class="report-pagination px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
            <p class="text-sm text-gray-600">
                Showing {{ $recentGives->firstItem() ?? 0 }} to {{ $recentGives->lastItem() ?? 0 }} of {{ $recentGives->total() }} entries
            </p>
            <div class="flex items-center gap-1">
                @if($recentGives->onFirstPage())
                    <span class="px-3 py-1.5 text-sm text-gray-400 bg-white border border-gray-200 rounded-md cursor-not-allowed">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $recentGives->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif

                @foreach($recentGives->getUrlRange(max(1, $recentGives->currentPage() - 2), min($recentGives->lastPage(), $recentGives->currentPage() + 2)) as $page => $url)
                    @if($page == $recentGives->currentPage())
                        <span class="px-3 py-1.5 text-sm font-semibold text-white bg-[#1C75BC] border border-[#1C75BC] rounded-md">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach

                @if($recentGives->hasMorePages())
                    <a href="{{ $recentGives->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span class="px-3 py-1.5 text-sm text-gray-400 bg-white border border-gray-200 rounded-md cursor-not-allowed">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Returns Table -->
    <div class="table-card" role="region" aria-label="Recent Tire Returns" data-print-section="returns">
        <div class="table-card-header">
            <h3 class="table-card-title"><i class="fas fa-arrow-down text-green-600 mr-2"></i>Recent Tire Returns</h3>
            <div class="table-filters">
                <input type="text" class="filter-input" id="returnsSearch" placeholder="Search tires, drivers, serial..." aria-label="Search recent returns">
                <select class="filter-select" id="returnsKmFilter" aria-label="Filter by KM difference">
                    <option value="">All KM Diff</option>
                    <option value="positive">Positive (+)</option>
                    <option value="negative">Negative (-)</option>
                    <option value="zero">Zero</option>
                </select>
                <button type="button" class="btn btn-secondary-custom btn-sm no-print" onclick="exportTable('returnsTable', 'recent-returns')">
                    <i class="fas fa-file-excel mr-1"></i>Export
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table" id="returnsTable">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Date</th>
                        <th scope="col">Tire Type</th>
                        <th scope="col">Serial Number</th>
                        <th scope="col">Driver</th>
                        <th scope="col">Current KM</th>
                        <th scope="col">Registered KM</th>
                        <th scope="col">KM Difference</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentReturns as $index => $ret)
                    <tr class="table-row">
                        <td class="font-mono text-gray-500">{{ $recentReturns->firstItem() + $index }}</td>
                        <td>{{ $ret->return_date->format('Y-m-d') }}</td>
                        <td class="font-medium text-gray-800">{{ $ret->tire->tire_type }}</td>
                        <td class="font-mono text-gray-600">{{ $ret->serial_number }}</td>
                        <td>{{ $ret->driver->name ?? 'N/A' }}</td>
                        <td class="font-mono">{{ number_format($ret->current_kilometer) }}</td>
                        <td class="font-mono">{{ number_format($ret->registered_kilometer) }}</td>
                        <td>
                            <span class="km-diff {{ $ret->km_difference >= 0 ? 'positive' : 'negative' }}">
                                {{ $ret->km_difference >= 0 ? '+' : '' }}{{ number_format($ret->km_difference) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>No recent tire returns found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination for Recent Returns -->
        <div class="report-pagination px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
            <p class="text-sm text-gray-600">
                Showing {{ $recentReturns->firstItem() ?? 0 }} to {{ $recentReturns->lastItem() ?? 0 }} of {{ $recentReturns->total() }} entries
            </p>
            <div class="flex items-center gap-1">
                @if($recentReturns->onFirstPage())
                    <span class="px-3 py-1.5 text-sm text-gray-400 bg-white border border-gray-200 rounded-md cursor-not-allowed">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $recentReturns->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif

                @foreach($recentReturns->getUrlRange(max(1, $recentReturns->currentPage() - 2), min($recentReturns->lastPage(), $recentReturns->currentPage() + 2)) as $page => $url)
                    @if($page == $recentReturns->currentPage())
                        <span class="px-3 py-1.5 text-sm font-semibold text-white bg-[#1C75BC] border border-[#1C75BC] rounded-md">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach

                @if($recentReturns->hasMorePages())
                    <a href="{{ $recentReturns->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span class="px-3 py-1.5 text-sm text-gray-400 bg-white border border-gray-200 rounded-md cursor-not-allowed">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Print Footer -->
    <div class="print-only mt-8 pt-4 border-t border-gray-300 text-center text-gray-500 text-sm">
        <p>Confidential - Ethiopian Pharmaceutical Supply Service Contract Management System</p>
        <p>Generated on {{ now()->format('F d, Y \a\t h:i A') }} by {{ auth()->user()->name }}</p>
    </div>
</div>

<!-- Modals (Same as before but with improved styling) -->
<!-- Total Tires Modal -->
<div class="modal fade" id="totalTiresModal" tabindex="-1" aria-labelledby="totalTiresModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-xl-custom">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h5 class="modal-title" id="totalTiresModalLabel"><i class="fas fa-tire me-2"></i>All Tires ({{ $totalTires }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body-custom">
                <div class="table-responsive">
                    <table class="data-table" id="totalTiresTable">
                        <thead>
                            <tr>
                                <th>ID</th><th>Tire Type</th><th>Serial Number</th>
                                <th>Plate Number</th><th>Initial KM</th><th>Status</th><th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allTires as $tire)
                            <tr>
                                <td>{{ $tire->id }}</td>
                                <td class="font-medium">{{ $tire->tire_type }}</td>
                                <td class="font-mono">{{ $tire->serial_number }}</td>
                                <td>{{ $tire->plate_number ?? '-' }}</td>
                                <td class="font-mono">{{ number_format($tire->initial_kilometer) }}</td>
                                <td>
                                    @if($tire->is_active)
                                        <span class="status-badge active">Active</span>
                                    @else
                                        <span class="status-badge inactive">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $tire->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Active Tires Modal -->
<div class="modal fade" id="activeTiresModal" tabindex="-1" aria-labelledby="activeTiresModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-xl-custom">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h5 class="modal-title" id="activeTiresModalLabel"><i class="fas fa-check-circle me-2"></i>Active Tires ({{ $activeTires }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body-custom">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr><th>ID</th><th>Tire Type</th><th>Serial Number</th><th>Plate Number</th><th>Initial KM</th><th>Created At</th></tr>
                        </thead>
                        <tbody>
                            @foreach($activeTireList as $tire)
                            <tr>
                                <td>{{ $tire->id }}</td>
                                <td class="font-medium">{{ $tire->tire_type }}</td>
                                <td class="font-mono">{{ $tire->serial_number }}</td>
                                <td>{{ $tire->plate_number ?? '-' }}</td>
                                <td class="font-mono">{{ number_format($tire->initial_kilometer) }}</td>
                                <td>{{ $tire->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Given Out Modal -->
<div class="modal fade" id="givenOutModal" tabindex="-1" aria-labelledby="givenOutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-xl-custom">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h5 class="modal-title" id="givenOutModalLabel"><i class="fas fa-hand-holding me-2"></i>Given Out Tires ({{ $givenOutTires }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body-custom">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr><th>Date</th><th>Tire Type</th><th>Serial</th><th>Driver</th><th>KM</th></tr>
                        </thead>
                        <tbody>
                            @foreach($givenOutTiresList as $give)
                            <tr>
                                <td>{{ $give->give_date->format('Y-m-d') }}</td>
                                <td class="font-medium">{{ $give->tire->tire_type }}</td>
                                <td class="font-mono">{{ $give->tire->serial_number }}</td>
                                <td>{{ $give->driver->name }}</td>
                                <td class="font-mono">{{ number_format($give->given_kilometer) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Given Today Modal -->
<div class="modal fade" id="givenTodayModal" tabindex="-1" aria-labelledby="givenTodayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-xl-custom">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h5 class="modal-title" id="givenTodayModalLabel"><i class="fas fa-plus me-2"></i>Given Today ({{ $givenToday }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body-custom">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr><th>Time</th><th>Tire Type</th><th>Serial</th><th>Driver</th><th>KM</th></tr>
                        </thead>
                        <tbody>
                            @foreach($givenTodayList as $give)
                            <tr>
                                <td>{{ $give->give_date->format('h:i A') }}</td>
                                <td class="font-medium">{{ $give->tire->tire_type }}</td>
                                <td class="font-mono">{{ $give->tire->serial_number }}</td>
                                <td>{{ $give->driver->name }}</td>
                                <td class="font-mono">{{ number_format($give->given_kilometer) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Returned Today Modal -->
<div class="modal fade" id="returnedTodayModal" tabindex="-1" aria-labelledby="returnedTodayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-xl-custom">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h5 class="modal-title" id="returnedTodayModalLabel"><i class="fas fa-undo-alt me-2"></i>Returned Today ({{ $returnedToday }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body-custom">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr><th>Time</th><th>Tire Type</th><th>Serial</th><th>Driver</th><th>Current KM</th><th>KM Diff</th></tr>
                        </thead>
                        <tbody>
                            @foreach($returnedTodayList as $ret)
                            <tr>
                                <td>{{ $ret->return_date->format('h:i A') }}</td>
                                <td class="font-medium">{{ $ret->tire->tire_type }}</td>
                                <td class="font-mono">{{ $ret->serial_number }}</td>
                                <td>{{ $ret->driver->name ?? 'N/A' }}</td>
                                <td class="font-mono">{{ number_format($ret->current_kilometer) }}</td>
                                <td>
                                    <span class="km-diff {{ $ret->km_difference >= 0 ? 'positive' : 'negative' }}">
                                        {{ $ret->km_difference >= 0 ? '+' : '' }}{{ number_format($ret->km_difference) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h5 class="modal-title" id="exportModalLabel"><i class="fas fa-file-export me-2"></i>Export Options</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body-custom">
                <p class="text-gray-600 mb-4">Select an export format:</p>
                <div class="space-y-3">
                    <a href="{{ route('tires.reports.export.excel') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors border border-gray-200">
                        <i class="fas fa-file-excel text-green-600 mr-3 text-xl"></i>
                        <div>
                            <h4 class="font-medium">Export Tires Excel</h4>
                            <p class="text-sm text-gray-500">Download complete tire inventory as Excel</p>
                        </div>
                    </a>
                    <a href="{{ route('tires.reports.export.drivers') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors border border-gray-200">
                        <i class="fas fa-file-excel text-blue-600 mr-3 text-xl"></i>
                        <div>
                            <h4 class="font-medium">Export Drivers Excel</h4>
                            <p class="text-sm text-gray-500">Download driver data as Excel</p>
                        </div>
                    </a>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Print Options Modal -->
<div class="modal fade" id="printOptionsModal" tabindex="-1" aria-labelledby="printOptionsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h5 class="modal-title" id="printOptionsLabel"><i class="fas fa-print me-2"></i>Print Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body-custom">
                <p class="text-gray-600 mb-4">Tick the information you want on the printed page. The report header and footer always print.</p>
                <div class="print-options-grid">
                    <label class="print-option">
                        <input type="checkbox" class="print-section-check" value="gives" checked>
                        <span><i class="fas fa-arrow-up text-blue-600 mr-2"></i>Recent Tire Gives</span>
                    </label>
                    <label class="print-option">
                        <input type="checkbox" class="print-section-check" value="returns" checked>
                        <span><i class="fas fa-arrow-down text-green-600 mr-2"></i>Recent Tire Returns</span>
                    </label>
                    <label class="print-option">
                        <input type="checkbox" class="print-section-check" value="stats">
                        <span><i class="fas fa-chart-line text-blue-500 mr-2"></i>Key Statistics</span>
                    </label>
                    <label class="print-option">
                        <input type="checkbox" class="print-section-check" value="insights">
                        <span><i class="fas fa-lightbulb text-amber-500 mr-2"></i>Key Insights</span>
                    </label>
                    <label class="print-option">
                        <input type="checkbox" class="print-section-check" value="charts">
                        <span><i class="fas fa-chart-pie text-violet-500 mr-2"></i>Charts</span>
                    </label>
                </div>
                <p id="printSectionsHint" class="print-sections-hint" style="display:none;">
                    <i class="fas fa-exclamation-circle me-1"></i>Select at least one section to print.
                </p>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cancel</button>
                <button type="button" class="btn btn-primary-custom" onclick="printSelectedSections()"><i class="fas fa-print me-1"></i>Print</button>
            </div>
        </div>
    </div>
</div>

<!-- Print Version Footer (Hidden on screen) -->
<div class="print-only">
    <div class="page-break"></div>
    <div class="text-center text-gray-500 text-sm mt-8 pt-4 border-t border-gray-300">
        <p><strong>Ethiopian Pharmaceutical Supply Service</strong> - Contract Management System</p>
        <p>Tire Tracking Report | Generated {{ now()->format('F d, Y') }} | Page <span class="page-number"></span></p>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Chart.js default configuration
Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
Chart.defaults.color = '#64748B';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.padding = 20;
Chart.defaults.plugins.legend.labels.font = Chart.defaults.plugins.legend.labels.font || {};
Chart.defaults.plugins.legend.labels.font.size = 11;

// Monthly Activity Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: @json($monthlyData->pluck('month')),
        datasets: [
            {
                label: 'Tires Given',
                data: @json($monthlyData->pluck('gives')),
                backgroundColor: 'rgba(28, 117, 188, 0.85)',
                borderColor: '#1C75BC',
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false,
            },
            {
                label: 'Tires Returned',
                data: @json($monthlyData->pluck('returns')),
                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                borderColor: '#10B981',
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, boxHeight: 12 } },
            tooltip: {
                backgroundColor: '#1E293B',
                titleFont: { size: 12, weight: '600' },
                bodyFont: { size: 11 },
                padding: 12,
                displayColors: true,
                callbacks: {
                    label: function(context) {
                        return `${context.dataset.label}: ${context.raw}`;
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { font: { size: 10 } }
            },
            y: {
                beginAtZero: true,
                grid: { color: '#F1F5F9', drawBorder: false },
                ticks: { stepSize: 1, font: { size: 10 } }
            }
        }
    }
});

// Tire Type Distribution Chart
const typeColors = ['#1C75BC', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444', '#3B82F6', '#6366F1', '#EC4899', '#14B8A6', '#F97316'];
const typeCtx = document.getElementById('typeChart').getContext('2d');
const typeChart = new Chart(typeCtx, {
    type: 'doughnut',
    data: {
        labels: @json($tireTypeDistribution->pluck('tire_type')),
        datasets: [{
            data: @json($tireTypeDistribution->pluck('count')),
            backgroundColor: typeColors,
            borderWidth: 3,
            borderColor: '#ffffff',
            hoverOffset: 12,
            borderAlign: 'inner'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, boxHeight: 12, padding: 16 } },
            tooltip: {
                backgroundColor: '#1E293B',
                titleFont: { size: 12, weight: '600' },
                bodyFont: { size: 11 },
                padding: 12,
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percent = ((context.raw / total) * 100).toFixed(1);
                        return `${context.label}: ${context.raw} (${percent}%)`;
                    }
                }
            }
        }
    }
});

// Table search/filter functionality
function initTableFilters() {
    // Gives table
    const givesSearch = document.getElementById('givesSearch');
    const givesStatusFilter = document.getElementById('givesStatusFilter');
    const givesTable = document.getElementById('givesTable');

    function filterGivesTable() {
        const searchTerm = givesSearch.value.toLowerCase();
        const statusFilter = givesStatusFilter.value;
        const rows = givesTable.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length <= 1) return; // Empty state row

            const text = Array.from(cells).map(c => c.textContent.toLowerCase()).join(' ');
            const statusCell = cells[5].querySelector('.status-badge');
            const status = statusCell ? (statusCell.classList.contains('returned') ? 'returned' : 'in-use') : '';

            const matchesSearch = text.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;

            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
        });
    }

    givesSearch.addEventListener('input', filterGivesTable);
    givesStatusFilter.addEventListener('change', filterGivesTable);

    // Returns table
    const returnsSearch = document.getElementById('returnsSearch');
    const returnsKmFilter = document.getElementById('returnsKmFilter');
    const returnsTable = document.getElementById('returnsTable');

    function filterReturnsTable() {
        const searchTerm = returnsSearch.value.toLowerCase();
        const kmFilter = returnsKmFilter.value;
        const rows = returnsTable.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length <= 1) return;

            const text = Array.from(cells).map(c => c.textContent.toLowerCase()).join(' ');
            const kmDiffCell = cells[6].querySelector('.km-diff');
            const kmDiff = kmDiffCell ? parseFloat(kmDiffCell.textContent.replace(/[+,]/g, '')) : 0;

            const matchesSearch = text.includes(searchTerm);
            let matchesKm = true;
            if (kmFilter === 'positive') matchesKm = kmDiff > 0;
            else if (kmFilter === 'negative') matchesKm = kmDiff < 0;
            else if (kmFilter === 'zero') matchesKm = kmDiff === 0;

            row.style.display = matchesSearch && matchesKm ? '' : 'none';
        });
    }

    returnsSearch.addEventListener('input', filterReturnsTable);
    returnsKmFilter.addEventListener('change', filterReturnsTable);
}

// Date range handling
document.getElementById('applyDateRange').addEventListener('click', function() {
    const from = document.getElementById('dateFrom').value;
    const to = document.getElementById('dateTo').value;
    if (from && to) {
        // Reload page with date range (would need server-side support)
        window.location.href = `{{ route('tires.reports') }}?date_from=${from}&date_to=${to}`;
    }
});

function resetDateRange() {
    document.getElementById('dateFrom').value = '@php echo \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->format("Y-m-d"); @endphp';
    document.getElementById('dateTo').value = '@php echo now()->format("Y-m-d"); @endphp';
    document.getElementById('periodText').textContent = 'Last 6 Months';
}

// Chart download
function downloadChart(canvasId, filename) {
    const canvas = document.getElementById(canvasId);
    const link = document.createElement('a');
    link.download = `${filename}-${new Date().toISOString().split('T')[0]}.png`;
    link.href = canvas.toDataURL('image/png');
    link.click();
}

// Table export to CSV
function exportTable(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const rows = table.querySelectorAll('tr');
    let csv = [];

    rows.forEach((row, rowIndex) => {
        if (row.style.display === 'none') return;
        const cells = row.querySelectorAll('th, td');
        const rowData = [];
        cells.forEach((cell, cellIndex) => {
            // Skip action columns if any
            let text = cell.textContent.trim();
            text = text.replace(/"/g, '""');
            if (text.includes(',') || text.includes('"') || text.includes('\n')) {
                text = `"${text}"`;
            }
            rowData.push(text);
        });
        csv.push(rowData.join(','));
    });

    const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `${filename}-${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
}

// PDF Export using html2canvas + jsPDF (placeholder - would need library)
function exportToPDF() {
    // This would require html2canvas and jsPDF libraries
    // For now, trigger print dialog with PDF as destination
    const printWindow = window.open('', '_blank');
    const content = document.getElementById('reportsPage').cloneNode(true);
    const reportStyles = document.getElementById('tireReportStyles')?.textContent ?? '';

    // Remove no-print elements
    content.querySelectorAll('.no-print, .screen-only').forEach(el => el.remove());

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Tire Tracking Report</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <script src="https://cdn.tailwindcss.com"><\/script>
            <style>
                ${reportStyles}
                @media print {
                    @page { margin: 1cm; size: A4 landscape; }
                    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                }
            </style>
        </head>
        <body class="p-4">${content.innerHTML}</body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => printWindow.print(), 500);
}

// Print options: only the ticked sections appear on the printed page
function openPrintOptions() {
    const hint = document.getElementById('printSectionsHint');
    if (hint) hint.style.display = 'none';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('printOptionsModal')).show();
}

function printSelectedSections() {
    const selected = Array.from(document.querySelectorAll('.print-section-check:checked')).map(b => b.value);
    const hint = document.getElementById('printSectionsHint');
    if (!selected.length) {
        hint.style.display = 'block';
        return;
    }
    hint.style.display = 'none';

    document.querySelectorAll('[data-print-section]').forEach(el => {
        el.classList.toggle('print-hide-section', !selected.includes(el.dataset.printSection));
    });

    const modalEl = document.getElementById('printOptionsModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    const triggerPrint = () => setTimeout(() => window.print(), 120);
    if (modal) {
        modalEl.addEventListener('hidden.bs.modal', triggerPrint, { once: true });
        modal.hide();
    } else {
        triggerPrint();
    }
}

function clearPrintSelection() {
    document.querySelectorAll('[data-print-section]').forEach(el => el.classList.remove('print-hide-section'));
}

// Keyboard accessibility for stat cards
document.querySelectorAll('.stat-card[tabindex="0"]').forEach(card => {
    card.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            card.click();
        }
    });
});

// Insight modal data
const insightData = {
    1: {
        title: 'Monthly Activity Trend',
        color: 'var(--epss-blue)',
        icon: 'fas fa-chart-bar',
        summary: 'Tire give/return operations over the last 6 months show higher give activity with 5 total gives and 2 returns.',
        details: [
            { label: 'Total Gives', value: '5' },
            { label: 'Total Returns', value: '2' },
            { label: 'Net Change', value: '+3 tires in circulation' },
            { label: 'Peak Month', value: 'November 2026 (3 gives)' },
            { label: 'Average Monthly Gives', value: '0.83' },
            { label: 'Average Monthly Returns', value: '0.33' },
            { label: 'Trend', value: 'Increasing activity' },
        ],
        breakdown: [
            { label: 'June 2026', value: '1 give, 0 returns' },
            { label: 'July 2026', value: '0 gives, 0 returns' },
            { label: 'August 2026', value: '1 give, 1 return' },
            { label: 'September 2026', value: '0 gives, 0 returns' },
            { label: 'October 2026', value: '0 gives, 1 return' },
            { label: 'November 2026', value: '3 gives, 0 returns' },
        ]
    },
    2: {
        title: 'Tire Type Distribution',
        color: 'var(--success)',
        icon: 'fas fa-tire',
        summary: '5 different tire types in inventory. Most common: 12R22.5 (4 units).',
        details: [
            { label: 'Total Tire Types', value: '5' },
            { label: 'Total Units', value: '12' },
            { label: 'Most Common', value: '12R22.5 (4 units)' },
            { label: 'Least Common', value: '16R22.5 (1 unit)' },
            { label: 'Active Tires', value: '10' },
            { label: 'Inactive Tires', value: '2' },
        ],
        breakdown: [
            { label: '12R22.5', value: '4 units (33.3%)' },
            { label: '13R22.5', value: '3 units (25.0%)' },
            { label: '14R22.5', value: '2 units (16.7%)' },
            { label: '15R22.5', value: '2 units (16.7%)' },
            { label: '16R22.5', value: '1 unit (8.3%)' },
        ]
    },
    3: {
        title: 'Driver Utilization',
        color: 'var(--warning)',
        icon: 'fas fa-user-tie',
        summary: 'Top driver: Mesay Kebede with 1 active tires. 3 drivers currently have tires assigned.',
        details: [
            { label: 'Drivers with Tires', value: '3' },
            { label: 'Total Active Tires', value: '3' },
            { label: 'Top Driver', value: 'Mesay Kebede' },
            { label: 'Top Driver Active Tires', value: '1' },
            { label: 'Average Tires/Driver', value: '1.0' },
            { label: 'Drivers without Tires', value: '2' },
        ],
        breakdown: [
            { label: 'Mesay Kebede', value: '1 tire (12R22.5 - SN: TR-0042)' },
            { label: 'Abebe Tadesse', value: '1 tire (13R22.5 - SN: TR-0078)' },
            { label: 'Kebede Alemu', value: '1 tire (14R22.5 - SN: TR-0103)' },
            { label: 'Tsegaye Worku', value: '0 tires' },
            { label: 'Hailu Desta', value: '0 tires' },
        ]
    },
    4: {
        title: 'Attention Required',
        color: 'var(--danger)',
        icon: 'fas fa-exclamation-triangle',
        summary: '3 tires currently in use. Monitor return schedules and kilometer readings.',
        details: [
            { label: 'Tires In Use', value: '3' },
            { label: 'Overdue Returns', value: '1' },
            { label: 'High KM Delta', value: '1' },
            { label: 'Due This Week', value: '2' },
            { label: 'Average Days In Use', value: '14 days' },
            { label: 'Max KM Driven', value: '4,250 km' },
        ],
        breakdown: [
            { label: 'TR-0042 (12R22.5)', value: 'Mesay Kebede - 14 days in use - 2,100 km' },
            { label: 'TR-0078 (13R22.5)', value: 'Abebe Tadesse - 21 days in use - 4,250 km ⚠️' },
            { label: 'TR-0103 (14R22.5)', value: 'Kebede Alemu - 7 days in use - 890 km' },
        ]
    }
};

// Insight card click handlers
document.querySelectorAll('.insight-card').forEach(card => {
    card.style.cursor = 'pointer';
    card.style.transition = 'all 0.2s ease';
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
        this.style.boxShadow = '0 8px 24px rgba(0,0,0,0.08)';
    });
    card.addEventListener('mouseleave', function() {
        this.style.transform = '';
        this.style.boxShadow = '';
    });

    card.addEventListener('click', function() {
        const insightId = this.dataset.insight;
        const data = insightData[insightId];
        if (!data) return;

        // Update modal header
        const header = document.getElementById('insightModalHeader');
        header.style.background = data.color;
        document.getElementById('insightModalTitle').textContent = data.title;

        // Build modal body
        const body = document.getElementById('insightModalBody');
        let html = `<p class="text-gray-600 mb-4">${data.summary}</p>`;

        html += '<h6 class="font-semibold text-gray-700 mb-2">Key Metrics</h6>';
        html += '<div class="space-y-2 mb-4">';
        data.details.forEach(detail => {
            html += `<div class="flex justify-between items-center p-2 bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-600">${detail.label}</span>
                <span class="text-sm font-semibold text-gray-800">${detail.value}</span>
            </div>`;
        });
        html += '</div>';

        html += '<h6 class="font-semibold text-gray-700 mb-2">Detailed Breakdown</h6>';
        html += '<div class="space-y-2">';
        data.breakdown.forEach(item => {
            html += `<div class="flex justify-between items-center p-2 bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-600">${item.label}</span>
                <span class="text-sm font-medium text-gray-800">${item.value}</span>
            </div>`;
        });
        html += '</div>';

        body.innerHTML = html;

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('insightModal'));
        modal.show();
    });

    card.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            this.click();
        }
    });
});
document.addEventListener('DOMContentLoaded', function() {
    initTableFilters();

    // Update report date
    const reportDateEl = document.getElementById('reportDate');
    if (reportDateEl) {
        reportDateEl.textContent = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    // Add print page numbers
    if (window.matchMedia('print').matches) {
        document.querySelectorAll('.page-number').forEach((el, i) => el.textContent = i + 1);
    }
});

// Handle print events
window.addEventListener('beforeprint', function() {
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', function() {
    document.body.classList.remove('printing');
    clearPrintSelection();
});
</script>
@endsection