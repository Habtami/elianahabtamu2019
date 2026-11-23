@extends('layouts.app')

@section('title', 'Battery Report')
@section('page-title', 'Battery Report')

@section('styles')
<style>
    .rep-wrapper { max-width: 1200px; margin: 0 auto; padding: 1.5rem 1rem 2rem; }

    .rep-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .rep-toolbar h2 { font-size: 1.35rem; font-weight: 700; color: #0f2a44; margin: 0; }
    .rep-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }

    .rep-btn {
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
        text-decoration: none;
    }
    .rep-btn:hover { background: #155a9e; }
    .rep-btn.secondary { background: #e8eff7; color: #1a4a73; }
    .rep-btn.secondary:hover { background: #d5e2f0; }
    .rep-btn.rep-mini { padding: 0.3rem 0.65rem; font-size: 0.72rem; }

    /* FILTER */
    .rep-filter {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 0.8rem;
        background: #fff;
        border: 1px solid #e3ebf3;
        border-radius: 14px;
        padding: 0.9rem 1.1rem;
        margin-bottom: 0.8rem;
        box-shadow: 0 2px 8px rgba(0, 30, 60, 0.05);
    }
    .rep-filter-group { display: flex; flex-direction: column; gap: 0.3rem; }
    .rep-filter-group label {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #6b8299;
    }
    .rep-filter select,
    .rep-filter input[type="date"] {
        padding: 0.5rem 0.6rem;
        border: 1px solid #cfdcea;
        border-radius: 8px;
        font-size: 0.82rem;
        color: #1e293b;
        background: #fff;
        min-width: 160px;
    }
    .rep-filter-actions { display: flex; gap: 0.5rem; margin-left: auto; }
    .rep-filter-error { flex-basis: 100%; color: #b91c1c; font-size: 0.78rem; font-weight: 600; }
    .rep-range { margin: 0 0 1.1rem; font-size: 0.78rem; color: #5c748c; }
    .rep-range strong { color: #0f2a44; }

    /* CARDS */
    .rep-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 1rem;
        margin-bottom: 1.3rem;
    }
    .rep-card {
        display: block;
        width: 100%;
        text-align: left;
        font: inherit;
        background: #fff;
        border: 1px solid #e3ebf3;
        border-radius: 14px;
        padding: 1rem 1.2rem;
        box-shadow: 0 2px 8px rgba(0, 30, 60, 0.06);
        cursor: pointer;
        transition: transform 0.12s ease, box-shadow 0.12s ease, border-color 0.12s ease;
    }
    .rep-card:hover { transform: translateY(-2px); border-color: #b9d3ec; box-shadow: 0 8px 18px rgba(0, 40, 80, 0.12); }
    .rep-card:focus-visible { outline: 3px solid #93c5fd; outline-offset: 2px; }
    .rep-card .label {
        display: block;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #6b8299;
    }
    .rep-card .value {
        display: block;
        font-size: 1.7rem;
        font-weight: 700;
        color: #0f2a44;
        margin-top: 0.2rem;
        line-height: 1.1;
    }
    .rep-card.issues .value { color: #b45309; }
    .rep-card.issues.has-issues .value { color: #b91c1c; }
    .rep-card .chips { margin-top: 0.4rem; display: flex; gap: 0.35rem; flex-wrap: wrap; }
    .rep-card-hint {
        display: block;
        margin-top: 0.55rem;
        font-size: 0.68rem;
        font-weight: 600;
        color: #8aa2b8;
    }
    .rep-card:hover .rep-card-hint { color: #1a6fc4; }

    .rep-chip {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 0.15rem 0.55rem;
        border-radius: 20px;
        background: #f1f5f9;
        color: #475569;
    }
    .rep-chip.high { background: #fee2e2; color: #b91c1c; }
    .rep-chip.medium { background: #fef3c7; color: #92400e; }
    .rep-chip.low { background: #e0f2fe; color: #075985; }

    /* CHARTS */
    .rep-charts {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.3rem;
    }
    .rep-chart-card { padding: 0; overflow: hidden; cursor: default; }
    .rep-chart-card:hover { transform: none; box-shadow: 0 2px 8px rgba(0, 30, 60, 0.06); }
    .rep-chart-card.wide { grid-column: 1 / -1; }
    .rep-chart-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.6rem;
        flex-wrap: wrap;
        padding: 0.95rem 1.2rem;
        border-bottom: 1px solid #edf2f8;
    }
    .rep-chart-head h3 { margin: 0; font-size: 0.95rem; color: #0f2a44; }
    .rep-chart-head span { font-size: 0.72rem; color: #6b8299; }
    .rep-chart-body { padding: 1rem 1.2rem 1.2rem; }

    .rep-donut-wrap { display: flex; justify-content: center; }
    .rep-donut { width: 168px; height: 168px; }
    .rep-donut .rep-ring-bg { stroke: #eef2f7; }
    .rep-donut .rep-donut-total { font-size: 26px; font-weight: 700; fill: #0f2a44; }
    .rep-donut .rep-donut-cap { font-size: 10px; fill: #6b8299; text-transform: uppercase; letter-spacing: 0.06em; }

    .rep-legend { margin-top: 0.8rem; display: flex; flex-direction: column; gap: 0.45rem; }
    .rep-legend-row { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: #33506e; }
    .rep-legend-swatch { width: 11px; height: 11px; border-radius: 3px; flex-shrink: 0; }
    .rep-legend-label { flex: 1; }
    .rep-legend-value { font-weight: 700; color: #0f2a44; }
    .rep-legend-pct { color: #7a90a6; min-width: 42px; text-align: right; }

    .rep-hbars { display: flex; flex-direction: column; gap: 0.8rem; }
    .rep-hrow-head { display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.25rem; }
    .rep-hrow-label { color: #33506e; font-weight: 600; }
    .rep-hrow-value { color: #0f2a44; font-weight: 700; }
    .rep-hrow-track { height: 10px; background: #eef2f7; border-radius: 6px; overflow: hidden; }
    .rep-hrow-fill { height: 100%; background: #6aa9e9; border-radius: 6px; transition: width 0.3s ease; }
    .rep-hrow-fill.top { background: #1a6fc4; }

    .rep-trend { width: 100%; }
    .rep-trend svg { width: 100%; height: auto; display: block; }
    .rep-trend .rep-grid { stroke: #e6edf5; stroke-width: 1; }
    .rep-trend .rep-axis { font-size: 11px; fill: #7a90a6; }
    .rep-trend .rep-axis.strong { font-weight: 700; fill: #33506e; font-size: 10px; }
    .rep-trend .rep-bar { fill: #3b82f6; }
    .rep-trend .rep-line { fill: none; stroke: #1a6fc4; stroke-width: 2.5; stroke-linejoin: round; stroke-linecap: round; }
    .rep-trend .rep-area { fill: rgba(59, 130, 246, 0.18); stroke: none; }
    .rep-trend .rep-dot { fill: #fff; stroke: #1a6fc4; stroke-width: 2.5; }

    .rep-chart-switch { display: flex; gap: 0.25rem; background: #eef2f7; padding: 0.2rem; border-radius: 8px; }
    .rep-chart-switch button {
        border: none;
        background: transparent;
        color: #5c748c;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.3rem 0.65rem;
        border-radius: 6px;
        cursor: pointer;
    }
    .rep-chart-switch button.active { background: #fff; color: #1a6fc4; box-shadow: 0 1px 3px rgba(10, 40, 70, 0.15); }

    .rep-empty-chart { color: #8ca3b8; font-style: italic; font-size: 0.82rem; padding: 1.2rem 0; text-align: center; }

    /* TABLE */
    .rep-card.table-card { padding: 0; overflow: hidden; cursor: default; }
    .rep-card.table-card:hover { transform: none; box-shadow: 0 2px 8px rgba(0, 30, 60, 0.06); }
    .rep-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.6rem;
        padding: 1.1rem 1.3rem;
        border-bottom: 1px solid #edf2f8;
    }
    .rep-card-head h3 { margin: 0; font-size: 1rem; color: #0f2a44; }
    .rep-card-head > span { font-size: 0.78rem; color: #6b8299; }
    .rep-card-tools {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        font-size: 0.78rem;
        color: #6b8299;
    }
    .rep-perpage { display: flex; align-items: center; gap: 0.35rem; }
    .rep-perpage label { font-size: 0.72rem; font-weight: 600; color: #6b8299; }
    .rep-perpage select {
        padding: 0.35rem 0.5rem;
        border: 1px solid #cfdcea;
        border-radius: 7px;
        font-size: 0.75rem;
        background: #fff;
        color: #1e293b;
    }
    .rep-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.6rem;
        padding: 0.85rem 1.2rem;
        border-top: 1px solid #edf2f8;
    }
    .rep-page-info { font-size: 0.75rem; color: #6b8299; }
    .rep-pagination nav { margin-left: auto; }
    .rep-pagination nav span,
    .rep-pagination nav a {
        display: inline-block;
        min-width: 30px;
        padding: 0.3rem 0.55rem;
        margin: 0 0.1rem;
        border: 1px solid #d7e2ee;
        border-radius: 7px;
        font-size: 0.75rem;
        color: #1a4a73;
        text-align: center;
        text-decoration: none;
        background: #fff;
    }
    .rep-pagination nav a:hover { background: #e8eff7; }
    .rep-pagination nav span[aria-current="page"] span,
    .rep-pagination nav .bg-white {
        background: #1a6fc4 !important;
        color: #fff !important;
        border-color: #1a6fc4 !important;
    }

    .rep-table-wrap { overflow-x: auto; }
    .rep-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    .rep-table th {
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
    .rep-table td {
        padding: 0.7rem 0.6rem;
        border-bottom: 1px solid #edf2f8;
        color: #1e293b;
        vertical-align: top;
    }
    .rep-table tr:hover td { background: #f8fbff; }
    .rep-empty { text-align: center; color: #8ca3b8; padding: 2rem 1rem; font-style: italic; }

    .sev {
        display: inline-block;
        font-size: 0.66rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.2rem 0.55rem;
        border-radius: 20px;
        white-space: nowrap;
    }
    .sev.high { background: #fee2e2; color: #b91c1c; }
    .sev.medium { background: #fef3c7; color: #92400e; }
    .sev.low { background: #e0f2fe; color: #075985; }

    .rep-issue-list { margin: 0; padding: 0; list-style: none; }
    .rep-issue-list li { padding: 0.1rem 0; color: #334155; }
    .rep-issue-list li::before { content: "•"; margin-right: 0.4rem; color: #94a3b8; }

    .batt-status {
        font-size: 0.68rem;
        padding: 0.2rem 0.7rem;
        border-radius: 20px;
        font-weight: 600;
        white-space: nowrap;
    }
    .batt-status.in-service { background: #dcfce7; color: #166534; }
    .batt-status.returned { background: #fef9c3; color: #854d0e; }

    .rep-ok {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #166534;
        border-radius: 12px;
        padding: 1.2rem 1.4rem;
        font-weight: 600;
    }

    .rep-checks-title { margin: 0 0 0.5rem; font-size: 0.82rem; font-weight: 700; color: #33506e; }
    .rep-checks { margin: 0 0 0.9rem; padding-left: 1.2rem; color: #334155; font-size: 0.82rem; }
    .rep-checks li { padding: 0.15rem 0; }
    .rep-checks-note { margin: 0; font-size: 0.78rem; color: #6b8299; font-style: italic; }

    /* MODALS */
    .rep-modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(10, 25, 45, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 120;
        padding: 1rem;
    }
    .rep-modal-bg.open { display: flex; }
    .rep-modal {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 980px;
        max-height: 88vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 24px 60px rgba(8, 25, 45, 0.35);
        overflow: hidden;
    }
    .rep-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.3rem;
        border-bottom: 1px solid #edf2f8;
    }
    .rep-modal-head h3 { margin: 0; font-size: 1.05rem; color: #0f2a44; }
    .rep-modal-head span { font-size: 0.75rem; color: #6b8299; }
    .rep-modal-close {
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
    .rep-modal-close:hover { background: #dbe6f1; }
    .rep-modal-body { padding: 1rem 1.3rem 1.4rem; overflow-y: auto; }

    .rep-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(185px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.2rem;
    }
    .rep-detail-field {
        background: #f7fafd;
        border: 1px solid #e8eff7;
        border-radius: 10px;
        padding: 0.55rem 0.7rem;
    }
    .rep-detail-label {
        display: block;
        font-size: 0.66rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #7a90a6;
        margin-bottom: 0.15rem;
    }
    .rep-detail-value { display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; word-break: break-word; }
    .rep-detail-section { margin: 0 0 0.7rem; font-size: 0.9rem; color: #0f2a44; }

    @media (max-width: 760px) {
        .rep-charts { grid-template-columns: 1fr; }
        .rep-filter-actions { margin-left: 0; flex-basis: 100%; }
    }

    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        .sidebar, .sidebar-toggle, .page-header, .page-footer, .rep-actions,
        .rep-filter, .rep-chart-switch, .rep-card-hint, .rep-modal-bg, .no-print { display: none !important; }
        body { background: #fff; }
        .main-content, .main-content.expanded { margin-left: 0 !important; }
        .rep-wrapper { max-width: none; margin: 0; padding: 0; }
        .rep-card { box-shadow: none; border: 1px solid #c9d7e6; cursor: default; }
        .rep-charts { grid-template-columns: 1fr 1fr; }
        .rep-chart-card.wide { grid-column: 1 / -1; }
        .rep-table-wrap { overflow: visible; }
        .rep-table thead { display: table-header-group; }
        .rep-table th, .rep-table td { border: 1px solid #9aa9b8; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endsection

@section('content')
@php
    $dateBasis = $dateField === 'manufacture' ? 'Date of Manufacture' : 'Date Given';
    if ($from && $to) {
        $rangeLabel = \Carbon\Carbon::parse($from)->format('d M Y') . ' – ' . \Carbon\Carbon::parse($to)->format('d M Y');
    } elseif ($from) {
        $rangeLabel = 'From ' . \Carbon\Carbon::parse($from)->format('d M Y');
    } elseif ($to) {
        $rangeLabel = 'Up to ' . \Carbon\Carbon::parse($to)->format('d M Y');
    } else {
        $rangeLabel = 'All dates';
    }

    $cardModals = [
        ['id' => 'repModalTotal', 'title' => 'All Batteries', 'batteries' => $batteries],
        ['id' => 'repModalInService', 'title' => 'In Service Batteries', 'batteries' => $inServiceList],
        ['id' => 'repModalReturned', 'title' => 'Returned Batteries', 'batteries' => $returnedList],
    ];

    $detailData = $batteries->mapWithKeys(function ($battery) {
        return [$battery->id => [
            'serial' => $battery->serial_number ?: '—',
            'material_code' => $battery->material_code ?: '—',
            'type' => $battery->battery_type ?: '—',
            'brand' => $battery->brand ?: '—',
            'plate' => $battery->plate_number ?: '—',
            'driver' => $battery->driver_name ?: '—',
            'initial' => (int) $battery->initial_kilometer,
            'returned' => (int) $battery->return_kilometer,
            'status' => $battery->getStatusDisplayName(),
            'status_class' => $battery->isReturned() ? 'returned' : 'in-service',
            'manufacture' => $battery->date_of_manufacture ? $battery->date_of_manufacture->format('d M Y') : '—',
            'given_on' => $battery->created_at ? $battery->created_at->format('d M Y') : '—',
            'created_by' => $battery->creator->name ?? '—',
            'histories' => $battery->histories->map(function ($history) {
                return [
                    'date' => $history->created_at ? $history->created_at->format('d M Y, H:i') : '—',
                    'action' => $history->action_label,
                    'status' => $history->status_label ?? '—',
                    'driver' => $history->driver_name ?: '—',
                    'initial' => is_null($history->initial_kilometer) ? '—' : $history->initial_kilometer,
                    'returned' => is_null($history->return_kilometer) ? '—' : $history->return_kilometer,
                    'notes' => $history->notes ?: '',
                    'by' => $history->creator->name ?? '—',
                ];
            })->values(),
        ]];
    })->all();
@endphp

<div class="rep-wrapper">

    <div class="rep-toolbar">
        <h2>🔋 Battery Report</h2>
        <div class="rep-actions no-print">
            <a href="{{ route('batteries.report.export.excel', request()->only(['date_field', 'from', 'to'])) }}"
               class="rep-btn secondary" title="Export all issues in the selected range">📊 Excel</a>
            <a href="{{ route('batteries.report.export.pdf', request()->only(['date_field', 'from', 'to'])) }}"
               class="rep-btn secondary" title="Export all issues in the selected range">📄 PDF</a>
            <button type="button" class="rep-btn secondary" onclick="window.print()">🖨️ Print</button>
            @if(auth()->user()->canViewBatteries())
            <a href="{{ route('batteries.index') }}" class="rep-btn secondary">← Back to Register</a>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('batteries.report') }}" class="rep-filter no-print">
        <div class="rep-filter-group">
            <label for="repDateField">Date basis</label>
            <select name="date_field" id="repDateField">
                <option value="given" @selected(old('date_field', $dateField) === 'given')>Date Given</option>
                <option value="manufacture" @selected(old('date_field', $dateField) === 'manufacture')>Date of Manufacture</option>
            </select>
        </div>
        <div class="rep-filter-group">
            <label for="repFrom">From</label>
            <input type="date" name="from" id="repFrom" value="{{ old('from', $from) }}">
        </div>
        <div class="rep-filter-group">
            <label for="repTo">To</label>
            <input type="date" name="to" id="repTo" value="{{ old('to', $to) }}">
        </div>
        <div class="rep-filter-actions">
            <button type="submit" class="rep-btn">Apply</button>
            <a href="{{ route('batteries.report') }}" class="rep-btn secondary">Reset</a>
        </div>
        @error('to')
            <div class="rep-filter-error">{{ $message }}</div>
        @enderror
    </form>

    <p class="rep-range">
        Showing <strong>{{ $dateBasis }}</strong>:
        <strong>{{ $rangeLabel }}</strong>
        · {{ $totalBatteries }} record(s)
    </p>

    <div class="rep-cards">
        <button type="button" class="rep-card" data-rep-modal="repModalTotal" aria-haspopup="dialog">
            <span class="label">Total Batteries</span>
            <span class="value">{{ $totalBatteries }}</span>
            <span class="rep-card-hint">Click to view full list →</span>
        </button>
        <button type="button" class="rep-card" data-rep-modal="repModalInService" aria-haspopup="dialog">
            <span class="label">In Service</span>
            <span class="value">{{ $inServiceBatteries }}</span>
            <span class="rep-card-hint">Click to view full list →</span>
        </button>
        <button type="button" class="rep-card" data-rep-modal="repModalReturned" aria-haspopup="dialog">
            <span class="label">Returned</span>
            <span class="value">{{ $returnedBatteries }}</span>
            <span class="rep-card-hint">Click to view full list →</span>
        </button>
        <button type="button" class="rep-card issues {{ $rows->count() ? 'has-issues' : '' }}" data-rep-modal="repModalIssues" aria-haspopup="dialog">
            <span class="label">Batteries With Issues</span>
            <span class="value">{{ $rows->count() }}</span>
            <span class="chips">
                <span class="rep-chip high">{{ $issueCounts['high'] }} high</span>
                <span class="rep-chip medium">{{ $issueCounts['medium'] }} medium</span>
                <span class="rep-chip low">{{ $issueCounts['low'] }} low</span>
            </span>
            <span class="rep-card-hint">
                {{ $rows->count() ? 'Click to review issues →' : 'All clear — no data problems' }}
            </span>
        </button>
    </div>

    <div class="rep-charts">
        <div class="rep-card rep-chart-card">
            <div class="rep-chart-head">
                <h3>Status Split</h3>
                <span>In service vs returned</span>
            </div>
            <div class="rep-chart-body">
                <div id="repStatusChart" class="rep-donut-wrap"></div>
                <div id="repStatusLegend" class="rep-legend"></div>
            </div>
        </div>

        <div class="rep-card rep-chart-card">
            <div class="rep-chart-head">
                <h3>Issues by Category</h3>
                <span>{{ $rows->sum(fn ($row) => count($row['issues'])) }} finding(s)</span>
            </div>
            <div class="rep-chart-body">
                <div id="repCategoryChart" class="rep-hbars"></div>
            </div>
        </div>

        <div class="rep-card rep-chart-card wide">
            <div class="rep-chart-head">
                <h3>Batteries by Month <span style="font-weight:400;">({{ $dateBasis }})</span></h3>
                <div class="rep-chart-switch no-print" id="repTrendSwitch">
                    <button type="button" data-trend-style="bar" class="active">Bars</button>
                    <button type="button" data-trend-style="line">Line</button>
                    <button type="button" data-trend-style="area">Area</button>
                </div>
            </div>
            <div class="rep-chart-body">
                <div id="repTrendChart" class="rep-trend"></div>
            </div>
        </div>
    </div>

    @if($rows->isEmpty())
        <div class="rep-ok">✔ No issues found — all battery records look consistent.</div>
    @else
        <div class="rep-card table-card">
            <div class="rep-card-head">
                <h3>Issues Requiring Attention</h3>
                <div class="rep-card-tools">
                    <span>
                        Showing {{ $issuePaginator->firstItem() ?? 0 }}–{{ $issuePaginator->lastItem() ?? 0 }}
                        of {{ $issuePaginator->total() }}
                    </span>
                    <form method="GET" action="{{ route('batteries.report') }}" class="rep-perpage no-print">
                        @if($dateField)
                            <input type="hidden" name="date_field" value="{{ $dateField }}">
                        @endif
                        @if($from)
                            <input type="hidden" name="from" value="{{ $from }}">
                        @endif
                        @if($to)
                            <input type="hidden" name="to" value="{{ $to }}">
                        @endif
                        <label for="repPerPage">Rows</label>
                        <select name="per_page" id="repPerPage" onchange="this.form.submit()">
                            @foreach([5, 10, 25, 50] as $size)
                                <option value="{{ $size }}" @selected($issuePaginator->perPage() === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <div class="rep-table-wrap">
                <table class="rep-table">
                    <thead>
                        <tr>
                            <th>Severity</th>
                            <th>Serial No</th>
                            <th>Plate No</th>
                            <th>Type / Brand</th>
                            <th>Driver</th>
                            <th>Status</th>
                            <th>Issue(s)</th>
                            <th class="no-print">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($issuePaginator as $row)
                        @php $battery = $row['battery']; @endphp
                        <tr>
                            <td><span class="sev {{ $row['severity'] }}">{{ $row['severity'] }}</span></td>
                            <td style="font-weight:600;">{{ $battery->serial_number ?: '—' }}</td>
                            <td>{{ $battery->plate_number ?: '—' }}</td>
                            <td>{{ $battery->battery_type ?: '—' }}<br><span style="color:#6b8299;">{{ $battery->brand ?: '—' }}</span></td>
                            <td>{{ $battery->driver_name ?: '—' }}</td>
                            <td>
                                <span class="batt-status {{ $battery->isReturned() ? 'returned' : 'in-service' }}">
                                    {{ $battery->getStatusDisplayName() }}
                                </span>
                            </td>
                            <td>
                                <ul class="rep-issue-list">
                                    @foreach($row['issues'] as $issue)
                                        <li>{{ $issue['message'] }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="no-print">
                                <button type="button" class="rep-btn secondary rep-mini" data-battery-detail="{{ $battery->id }}">View</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($issuePaginator->hasPages())
                <div class="rep-pagination no-print">
                    <span class="rep-page-info">
                        Page {{ $issuePaginator->currentPage() }} of {{ $issuePaginator->lastPage() }}
                    </span>
                    {{ $issuePaginator->links() }}
                </div>
            @endif
        </div>
    @endif
</div>

@foreach($cardModals as $modal)
<div class="rep-modal-bg" id="{{ $modal['id'] }}" role="dialog" aria-modal="true" aria-labelledby="{{ $modal['id'] }}Title">
    <div class="rep-modal">
        <div class="rep-modal-head">
            <div>
                <h3 id="{{ $modal['id'] }}Title">{{ $modal['title'] }}</h3>
                <span>{{ $modal['batteries']->count() }} record(s)</span>
            </div>
            <button type="button" class="rep-modal-close" aria-label="Close" data-rep-close>×</button>
        </div>
        <div class="rep-modal-body">
            @if($modal['batteries']->isEmpty())
                <div class="rep-empty">No batteries in this list for the selected range.</div>
            @else
                <div class="rep-table-wrap">
                    <table class="rep-table">
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Plate</th>
                                <th>Type / Brand</th>
                                <th>Driver</th>
                                <th>Initial KM</th>
                                <th>Return KM</th>
                                <th>Status</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modal['batteries'] as $battery)
                                <tr>
                                    <td style="font-weight:600;">{{ $battery->serial_number ?: '—' }}</td>
                                    <td>{{ $battery->plate_number ?: '—' }}</td>
                                    <td>{{ $battery->battery_type ?: '—' }}<br><span style="color:#6b8299;">{{ $battery->brand ?: '—' }}</span></td>
                                    <td>{{ $battery->driver_name ?: '—' }}</td>
                                    <td>{{ $battery->initial_kilometer ?? '—' }}</td>
                                    <td>{{ $battery->return_kilometer ?? '—' }}</td>
                                    <td>
                                        <span class="batt-status {{ $battery->isReturned() ? 'returned' : 'in-service' }}">
                                            {{ $battery->getStatusDisplayName() }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="rep-btn secondary rep-mini" data-battery-detail="{{ $battery->id }}">View</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endforeach

<div class="rep-modal-bg" id="repModalIssues" role="dialog" aria-modal="true" aria-labelledby="repModalIssuesTitle">
    <div class="rep-modal">
        <div class="rep-modal-head">
            <div>
                <h3 id="repModalIssuesTitle">Batteries With Issues</h3>
                <span>{{ $rows->count() }} battery(ies) · {{ $issueCounts['high'] }} high, {{ $issueCounts['medium'] }} medium, {{ $issueCounts['low'] }} low</span>
            </div>
            <button type="button" class="rep-modal-close" aria-label="Close" data-rep-close>×</button>
        </div>
        <div class="rep-modal-body">
            @if($rows->isEmpty())
                <div class="rep-ok" style="margin-bottom:1rem;">✔ No issues found for the selected range.</div>
                <p class="rep-checks-title">This report checked every battery for:</p>
                <ul class="rep-checks">
                    <li>A serial number that is present and not duplicated</li>
                    <li>Return KM that is not lower than Initial KM</li>
                    <li>A return reading recorded on every returned battery</li>
                    <li>An assigned driver on every in-service battery</li>
                    <li>Plate number, material code, battery type, brand and manufacture date</li>
                    <li>At least one entry in the battery history</li>
                </ul>
                <p class="rep-checks-note">All {{ $totalBatteries }} battery record(s) in this range passed these checks.</p>
            @else
                <div class="rep-table-wrap">
                    <table class="rep-table">
                        <thead>
                            <tr>
                                <th>Severity</th>
                                <th>Serial No</th>
                                <th>Plate</th>
                                <th>Driver</th>
                                <th>Issue(s)</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                @php $battery = $row['battery']; @endphp
                                <tr>
                                    <td><span class="sev {{ $row['severity'] }}">{{ $row['severity'] }}</span></td>
                                    <td style="font-weight:600;">{{ $battery->serial_number ?: '—' }}</td>
                                    <td>{{ $battery->plate_number ?: '—' }}</td>
                                    <td>{{ $battery->driver_name ?: '—' }}</td>
                                    <td>
                                        <ul class="rep-issue-list">
                                            @foreach($row['issues'] as $issue)
                                                <li>{{ $issue['message'] }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        <button type="button" class="rep-btn secondary rep-mini" data-battery-detail="{{ $battery->id }}">View</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="rep-modal-bg" id="repModalDetail" role="dialog" aria-modal="true" aria-labelledby="repDetailTitle">
    <div class="rep-modal">
        <div class="rep-modal-head">
            <div>
                <h3 id="repDetailTitle">Battery Details</h3>
                <span>Full information</span>
            </div>
            <button type="button" class="rep-modal-close" aria-label="Close" data-rep-close>×</button>
        </div>
        <div class="rep-modal-body" id="repDetailBody"></div>
    </div>
</div>

<script type="application/json" id="rep-chart-data">@json($chartData)</script>
<script type="application/json" id="rep-battery-details">@json($detailData)</script>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';

    var chartData = {};
    var detailMap = {};
    try {
        chartData = JSON.parse(document.getElementById('rep-chart-data').textContent || '{}');
        detailMap = JSON.parse(document.getElementById('rep-battery-details').textContent || '{}');
    } catch (e) {
        chartData = {};
        detailMap = {};
    }

    var NS = 'http://www.w3.org/2000/svg';

    function svgEl(name, attrs) {
        var node = document.createElementNS(NS, name);
        if (attrs) {
            Object.keys(attrs).forEach(function (key) {
                node.setAttribute(key, attrs[key]);
            });
        }
        return node;
    }

    function svgText(x, y, text, cls, anchor) {
        var node = svgEl('text', { x: x, y: y });
        if (cls) node.setAttribute('class', cls);
        if (anchor) node.setAttribute('text-anchor', anchor);
        node.textContent = text;
        return node;
    }

    function emptyNote(message) {
        var div = document.createElement('div');
        div.className = 'rep-empty-chart';
        div.textContent = message;
        return div;
    }

    function renderDonut() {
        var wrap = document.getElementById('repStatusChart');
        var legend = document.getElementById('repStatusLegend');
        if (!wrap || !legend) return;

        wrap.textContent = '';
        legend.textContent = '';

        var segments = chartData.status || [];
        var total = segments.reduce(function (sum, seg) { return sum + seg.value; }, 0);

        if (!total) {
            wrap.appendChild(emptyNote('No batteries in the selected range.'));
            return;
        }

        var cx = 90;
        var cy = 90;
        var radius = 62;
        var circumference = 2 * Math.PI * radius;
        var svg = svgEl('svg', { viewBox: '0 0 180 180', 'class': 'rep-donut', role: 'img' });
        svg.appendChild(svgEl('circle', {
            cx: cx, cy: cy, r: radius, fill: 'none', 'stroke-width': 26, 'class': 'rep-ring-bg'
        }));

        var offset = 0;
        segments.forEach(function (seg) {
            var length = (seg.value / total) * circumference;
            if (length <= 0) return;
            svg.appendChild(svgEl('circle', {
                cx: cx, cy: cy, r: radius, fill: 'none',
                stroke: seg.color || '#94a3b8',
                'stroke-width': 26,
                'stroke-dasharray': length + ' ' + (circumference - length),
                'stroke-dashoffset': -offset,
                transform: 'rotate(-90 ' + cx + ' ' + cy + ')'
            }));
            offset += length;
        });

        svg.appendChild(svgText(cx, cy + 2, String(total), 'rep-donut-total', 'middle'));
        svg.appendChild(svgText(cx, cy + 22, 'batteries', 'rep-donut-cap', 'middle'));
        wrap.appendChild(svg);

        segments.forEach(function (seg) {
            var row = document.createElement('div');
            row.className = 'rep-legend-row';

            var swatch = document.createElement('span');
            swatch.className = 'rep-legend-swatch';
            swatch.style.background = seg.color || '#94a3b8';

            var label = document.createElement('span');
            label.className = 'rep-legend-label';
            label.textContent = seg.label;

            var value = document.createElement('span');
            value.className = 'rep-legend-value';
            value.textContent = seg.value;

            var pct = document.createElement('span');
            pct.className = 'rep-legend-pct';
            pct.textContent = Math.round((seg.value / total) * 100) + '%';

            row.appendChild(swatch);
            row.appendChild(label);
            row.appendChild(value);
            row.appendChild(pct);
            legend.appendChild(row);
        });
    }

    function renderCategories() {
        var wrap = document.getElementById('repCategoryChart');
        if (!wrap) return;

        wrap.textContent = '';
        var items = chartData.categories || [];

        if (!items.length) {
            wrap.appendChild(emptyNote('No issue findings to chart.'));
            return;
        }

        var max = items.reduce(function (top, item) { return Math.max(top, item.value); }, 1);

        items.forEach(function (item, index) {
            var row = document.createElement('div');
            row.className = 'rep-hrow';

            var head = document.createElement('div');
            head.className = 'rep-hrow-head';

            var label = document.createElement('span');
            label.className = 'rep-hrow-label';
            label.textContent = item.label;

            var value = document.createElement('span');
            value.className = 'rep-hrow-value';
            value.textContent = item.value;

            head.appendChild(label);
            head.appendChild(value);

            var track = document.createElement('div');
            track.className = 'rep-hrow-track';

            var fill = document.createElement('div');
            fill.className = 'rep-hrow-fill' + (index === 0 ? ' top' : '');
            fill.style.width = Math.max(4, Math.round((item.value / max) * 100)) + '%';

            track.appendChild(fill);
            row.appendChild(head);
            row.appendChild(track);
            wrap.appendChild(row);
        });
    }

    var trendStyle = 'bar';

    function renderTrend(style) {
        var wrap = document.getElementById('repTrendChart');
        if (!wrap) return;

        wrap.textContent = '';
        var points = chartData.trend || [];

        if (!points.length) {
            wrap.appendChild(emptyNote('No batteries in the selected range.'));
            return;
        }

        var W = 760;
        var H = 280;
        var padL = 46;
        var padR = 20;
        var padT = 24;
        var padB = 46;
        var innerW = W - padL - padR;
        var innerH = H - padT - padB;
        var maxVal = points.reduce(function (top, p) { return Math.max(top, p.value); }, 1);
        var baseY = padT + innerH;

        function xAt(index) {
            if (points.length === 1) return padL + innerW / 2;
            return padL + (index * innerW) / (points.length - 1);
        }

        function yAt(value) {
            return baseY - (value / maxVal) * innerH;
        }

        var svg = svgEl('svg', {
            viewBox: '0 0 ' + W + ' ' + H,
            preserveAspectRatio: 'xMidYMid meet',
            role: 'img'
        });

        var ticks = 4;
        for (var t = 0; t <= ticks; t++) {
            var tickValue = Math.round((maxVal / ticks) * t);
            var tickY = yAt(tickValue);
            svg.appendChild(svgEl('line', {
                x1: padL, y1: tickY, x2: W - padR, y2: tickY, 'class': 'rep-grid'
            }));
            svg.appendChild(svgText(padL - 10, tickY + 4, String(tickValue), 'rep-axis', 'end'));
        }

        if (style === 'bar') {
            var slot = innerW / Math.max(points.length, 1);
            var barW = Math.min(56, slot * 0.6);

            points.forEach(function (point, index) {
                var height = (point.value / maxVal) * innerH;
                if (point.value > 0) height = Math.max(3, height);
                var x = xAt(index) - barW / 2;
                svg.appendChild(svgEl('rect', {
                    x: x,
                    y: baseY - height,
                    width: barW,
                    height: height,
                    rx: 3,
                    'class': 'rep-bar'
                }));

                if (points.length <= 12) {
                    svg.appendChild(svgText(xAt(index), baseY - height - 7, String(point.value), 'rep-axis strong', 'middle'));
                }
            });
        } else {
            var coords = points.map(function (point, index) {
                return [xAt(index), yAt(point.value)];
            });
            var path = coords.map(function (coord, index) {
                return (index === 0 ? 'M' : 'L') + coord[0] + ' ' + coord[1];
            }).join(' ');

            if (style === 'area') {
                var areaPath = path
                    + ' L ' + coords[coords.length - 1][0] + ' ' + baseY
                    + ' L ' + coords[0][0] + ' ' + baseY + ' Z';
                svg.appendChild(svgEl('path', { d: areaPath, 'class': 'rep-area' }));
            }

            svg.appendChild(svgEl('path', { d: path, 'class': 'rep-line' }));

            coords.forEach(function (coord, index) {
                svg.appendChild(svgEl('circle', { cx: coord[0], cy: coord[1], r: 4, 'class': 'rep-dot' }));
                if (points.length <= 12) {
                    svg.appendChild(svgText(coord[0], coord[1] - 10, String(points[index].value), 'rep-axis strong', 'middle'));
                }
            });
        }

        var labelStep = Math.ceil(points.length / 10);
        points.forEach(function (point, index) {
            if (index % labelStep !== 0 && index !== points.length - 1) return;
            svg.appendChild(svgText(xAt(index), H - padB + 22, point.label, 'rep-axis', 'middle'));
        });

        wrap.appendChild(svg);
    }

    function bindTrendSwitch() {
        var buttons = document.querySelectorAll('[data-trend-style]');
        Array.prototype.forEach.call(buttons, function (button) {
            button.addEventListener('click', function () {
                trendStyle = button.getAttribute('data-trend-style');
                Array.prototype.forEach.call(buttons, function (other) {
                    other.classList.toggle('active', other === button);
                });
                renderTrend(trendStyle);
            });
        });
    }

    function openModal(id) {
        var modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('open');
        if (!document.querySelector('.rep-modal-bg.open')) {
            document.body.style.overflow = '';
        }
    }

    function detailField(label, value) {
        var cell = document.createElement('div');
        cell.className = 'rep-detail-field';

        var labelNode = document.createElement('span');
        labelNode.className = 'rep-detail-label';
        labelNode.textContent = label;

        var valueNode = document.createElement('span');
        valueNode.className = 'rep-detail-value';
        valueNode.textContent = value;

        cell.appendChild(labelNode);
        cell.appendChild(valueNode);
        return cell;
    }

    function historyTable(histories) {
        var table = document.createElement('table');
        table.className = 'rep-table';

        var head = document.createElement('thead');
        var headRow = document.createElement('tr');
        ['Date', 'Action', 'Status', 'Driver', 'Initial KM', 'Return KM', 'Recorded By', 'Notes'].forEach(function (text) {
            var th = document.createElement('th');
            th.textContent = text;
            headRow.appendChild(th);
        });
        head.appendChild(headRow);
        table.appendChild(head);

        var body = document.createElement('tbody');
        histories.forEach(function (entry) {
            var row = document.createElement('tr');
            [entry.date, entry.action, entry.status, entry.driver, entry.initial, entry.returned, entry.by, entry.notes || '—']
                .forEach(function (value) {
                    var td = document.createElement('td');
                    td.textContent = value;
                    row.appendChild(td);
                });
            body.appendChild(row);
        });
        table.appendChild(body);
        return table;
    }

    function showDetail(id) {
        var battery = detailMap[id];
        var body = document.getElementById('repDetailBody');
        var title = document.getElementById('repDetailTitle');
        if (!battery || !body || !title) return;

        title.textContent = 'Battery ' + battery.serial;
        body.textContent = '';

        var grid = document.createElement('div');
        grid.className = 'rep-detail-grid';

        grid.appendChild(detailField('Serial Number', battery.serial));
        grid.appendChild(detailField('Material Code', battery.material_code));
        grid.appendChild(detailField('Battery Type', battery.type));
        grid.appendChild(detailField('Brand', battery.brand));
        grid.appendChild(detailField('Plate Number', battery.plate));
        grid.appendChild(detailField('Assigned Driver', battery.driver));
        grid.appendChild(detailField('Initial KM', String(battery.initial)));
        grid.appendChild(detailField('Return KM', battery.returned > 0 ? String(battery.returned) : '—'));
        grid.appendChild(detailField('Difference', battery.returned > 0 ? (battery.returned - battery.initial) : '—'));
        grid.appendChild(detailField('Date of Manufacture', battery.manufacture));
        grid.appendChild(detailField('Date Given', battery.given_on));
        grid.appendChild(detailField('Registered By', battery.created_by));

        var statusCell = document.createElement('div');
        statusCell.className = 'rep-detail-field';
        var statusLabel = document.createElement('span');
        statusLabel.className = 'rep-detail-label';
        statusLabel.textContent = 'Status';
        var statusValue = document.createElement('span');
        statusValue.className = 'rep-detail-value';
        var statusChip = document.createElement('span');
        statusChip.className = 'batt-status ' + battery.status_class;
        statusChip.textContent = battery.status;
        statusValue.appendChild(statusChip);
        statusCell.appendChild(statusLabel);
        statusCell.appendChild(statusValue);
        grid.appendChild(statusCell);

        body.appendChild(grid);

        var heading = document.createElement('h4');
        heading.className = 'rep-detail-section';
        heading.textContent = 'History';
        body.appendChild(heading);

        if (!battery.histories || !battery.histories.length) {
            body.appendChild(emptyNote('No history records found.'));
        } else {
            var wrap = document.createElement('div');
            wrap.className = 'rep-table-wrap';
            wrap.appendChild(historyTable(battery.histories));
            body.appendChild(wrap);
        }

        openModal('repModalDetail');
    }

    function bindModals() {
        Array.prototype.forEach.call(document.querySelectorAll('[data-rep-modal]'), function (button) {
            button.addEventListener('click', function () {
                openModal(button.getAttribute('data-rep-modal'));
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('.rep-modal-bg'), function (backdrop) {
            backdrop.addEventListener('click', function (event) {
                if (event.target === backdrop) closeModal(backdrop);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-rep-close]'), function (button) {
            button.addEventListener('click', function () {
                closeModal(button.closest('.rep-modal-bg'));
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-battery-detail]'), function (button) {
            button.addEventListener('click', function () {
                showDetail(button.getAttribute('data-battery-detail'));
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;
            var open = document.querySelectorAll('.rep-modal-bg.open');
            if (open.length) {
                closeModal(open[open.length - 1]);
            }
        });
    }

    renderDonut();
    renderCategories();
    renderTrend(trendStyle);
    bindTrendSwitch();
    bindModals();
})();
</script>
@endsection
