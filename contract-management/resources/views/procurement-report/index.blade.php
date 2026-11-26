@extends('layouts.app')

@section('title', 'Procurement Report')

@section('styles')
<style>
    .pr-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .pr-stat {
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
    .pr-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
        border-left-color: var(--card-color, #0ea5e9);
    }
    .pr-stat:focus-visible { outline: 2px solid var(--card-color, #0ea5e9); outline-offset: 2px; }
    .pr-stat-main { min-width: 0; }
    .pr-stat-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
    }
    .pr-stat-value {
        display: block;
        font-size: 1.3rem;
        font-weight: 700;
        color: #0f172a;
        margin-top: 0.3rem;
        line-height: 1.15;
        white-space: nowrap;
    }
    .pr-stat-value small { font-size: 0.7rem; font-weight: 600; color: #64748b; margin-left: 0.2rem; }
    .pr-stat-sub { display: block; margin-top: 0.35rem; font-size: 0.74rem; font-weight: 500; color: #7a90a6; }
    .pr-stat-hint {
        display: block;
        margin-top: 0.45rem;
        font-size: 0.68rem;
        font-weight: 600;
        color: #8aa2b8;
        opacity: 0;
        transition: opacity 0.15s ease, color 0.15s ease;
    }
    .pr-stat:hover .pr-stat-hint,
    .pr-stat:focus-visible .pr-stat-hint { opacity: 1; color: #0ea5e9; }
    .pr-stat-icon {
        flex: none;
        padding-top: 0.1rem;
        font-size: 1.9rem;
        line-height: 1;
        color: var(--card-color, #0ea5e9);
        opacity: 0.22;
    }

    .pr-action-btn {
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
    .pr-action-btn:hover { background: #f1f5f9; }
    .pr-actions { display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap; }
    .pr-action-btn-active { background: #e0f2fe; border-color: #bae6fd; color: #0369a1; }

    .pr-perpage { display: flex; align-items: center; gap: 0.35rem; }
    .pr-perpage label { font-size: 0.72rem; font-weight: 600; color: #6b8299; margin: 0; }
    .pr-perpage select {
        padding: 0.35rem 0.5rem;
        border: 1px solid #cfdcea;
        border-radius: 7px;
        font-size: 0.75rem;
        background: #fff;
        color: #1e293b;
    }

    .pr-pagination {
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
    .pr-pagination strong { color: #1e293b; font-weight: 600; }
    .pr-pages { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; margin-left: auto; }
    .pr-page-btn {
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
    .pr-page-btn:hover:not(:disabled) { background: #f1f5f9; }
    .pr-page-btn.pr-active { background: #0ea5e9; border-color: #0ea5e9; color: #fff; }
    .pr-page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .pr-page-dots { padding: 0.35rem 0.3rem; color: #6b8299; }

    .pr-modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(10, 25, 45, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1700;
        padding: 1rem;
    }
    .pr-modal-bg.open { display: flex; }
    .pr-modal {
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
    .pr-modal.pr-modal-sm { max-width: 480px; }
    .pr-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.3rem;
        border-bottom: 1px solid #edf2f8;
    }
    .pr-modal-head h3 { margin: 0; font-size: 1.05rem; color: #0f2a44; }
    .pr-modal-head span { font-size: 0.75rem; color: #6b8299; }
    .pr-modal-close {
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
    .pr-modal-close:hover { background: #dbe6f1; }
    .pr-modal-body { padding: 1rem 1.3rem 1.4rem; overflow-y: auto; }
    .pr-modal-foot {
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
    .pr-modal-foot strong { color: #1e293b; font-weight: 600; }
    .pr-modal-pages { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; }

    .pr-table-wrap { overflow-x: auto; }
    .pr-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    .pr-table th {
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
    .pr-table td {
        padding: 0.7rem 0.6rem;
        border-bottom: 1px solid #edf2f8;
        color: #1e293b;
        vertical-align: middle;
    }
    .pr-table tbody tr:hover td { background: #f8fbff; }
    .pr-table th.pr-roll,
    .pr-table td.pr-roll {
        width: 3.2rem;
        color: #7a90a6;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .pr-table th.pr-amount,
    .pr-table td.pr-amount { text-align: right; font-variant-numeric: tabular-nums; }
    .pr-empty { text-align: center; color: #8ca3b8; padding: 2rem 1rem; font-style: italic; }
    .pr-modal-sub { font-size: 0.72rem; color: #7a90a6; }

    .pr-print-options { display: flex; flex-direction: column; gap: 0.55rem; }
    .pr-print-options label {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        font-size: 0.88rem;
        color: #1e293b;
        cursor: pointer;
    }
    .pr-print-options input { width: 16px; height: 16px; accent-color: #0ea5e9; }
    .pr-print-hint { margin: 0.7rem 0 0; font-size: 0.78rem; font-weight: 600; color: #b91c1c; }

    .pr-filter-bar {
        display: flex;
        align-items: flex-end;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .pr-filter-bar input, .pr-filter-bar select {
        padding: 0.45rem 0.7rem;
        border: 1px solid #cddaea;
        border-radius: 8px;
        font-size: 0.82rem;
        background: #fff;
        color: #1e293b;
    }
    .pr-filter-bar input:focus, .pr-filter-bar select:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.12);
    }

    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        .sidebar, .sidebar-toggle, .page-header, .page-footer,
        .pr-filter-bar, .pr-actions, .pr-pagination, .pr-modal-bg,
        .table-tip-btn, .table-tip-panel, .no-print { display: none !important; }
        .print-hide-section { display: none !important; }
        body { background: #fff; }
        .main-content, .main-content.expanded { margin-left: 0 !important; }
        .pr-page { max-width: none !important; margin: 0 !important; padding: 0 !important; }
        .print-card { box-shadow: none !important; }
        .pr-table { width: 100%; font-size: 9pt; }
        .pr-table thead { display: table-header-group; }
        .pr-table th, .pr-table td { border: 1px solid #9aa9b8; padding: 0.3rem 0.35rem; }
        .pr-table tr:hover td { background: transparent; }
        .pr-stat { box-shadow: none; cursor: default; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8 pr-page">

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-chart-pie text-[#0ea5e9] mr-3"></i>
                Procurement Report
            </h1>
            <p class="text-gray-600 mt-2">Total procurement by amount, split by purchase and by tender. Amounts in ETB.</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition"
                    data-pr-print-options data-print-default="cards">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <a href="{{ route('procurement-report.export', array_merge(['format' => 'csv'], request()->query())) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-excel mr-2"></i>Excel
            </a>
            <a href="{{ route('procurement-report.export', array_merge(['format' => 'pdf'], request()->query())) }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-file-pdf mr-2"></i>PDF
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    @php
        $statCards = [
            [
                'key' => 'total',
                'label' => 'Total Procurement',
                'color' => '#0ea5e9',
                'icon' => 'fa-chart-pie',
                'id' => 'prModalTotal',
                'amount' => $grandTotal,
                'hint' => 'Click to view every procurement record →',
                'rows' => $rows,
            ],
            [
                'key' => 'purchase',
                'label' => 'By Purchase',
                'color' => '#2563eb',
                'icon' => 'fa-bag-shopping',
                'id' => 'prModalPurchase',
                'amount' => $purchaseTotal,
                'hint' => 'Click to view purchase order amounts →',
                'rows' => $rows->where('category', 'Purchase')->values(),
            ],
            [
                'key' => 'tender',
                'label' => 'By Tender',
                'color' => '#0d9488',
                'icon' => 'fa-file-contract',
                'id' => 'prModalTender',
                'amount' => $tenderTotal,
                'hint' => 'Click to view tender contract amounts →',
                'rows' => $rows->where('category', 'Tender')->values(),
            ],
            [
                'key' => 'icb',
                'label' => 'ICB Tenders',
                'color' => '#6366f1',
                'icon' => 'fa-globe',
                'id' => 'prModalIcb',
                'amount' => $icbTotal,
                'hint' => 'Click to view ICB tender amounts →',
                'rows' => $rows->where('category', 'Tender')->where('type', 'ICB')->values(),
            ],
            [
                'key' => 'ncb',
                'label' => 'NCB Tenders',
                'color' => '#f59e0b',
                'icon' => 'fa-flag',
                'id' => 'prModalNcb',
                'amount' => $ncbTotal,
                'hint' => 'Click to view NCB tender amounts →',
                'rows' => $rows->where('category', 'Tender')->where('type', 'NCB')->values(),
            ],
        ];
    @endphp

    <!-- Total procurement -->
    <div class="pr-stats" data-print-section="cards">
        @foreach($statCards as $card)
            <button type="button" class="pr-stat" style="--card-color: {{ $card['color'] }};"
                    data-pr-modal="{{ $card['id'] }}" aria-haspopup="dialog"
                    aria-label="{{ $card['label'] }} details" title="{{ $card['hint'] }}">
                <span class="pr-stat-main">
                    <span class="pr-stat-label">{{ $card['label'] }}</span>
                    <span class="pr-stat-value">{{ number_format($card['amount'], 2) }} <small>ETB</small></span>
                    <span class="pr-stat-sub">{{ $card['rows']->count() }} record(s)</span>
                    <span class="pr-stat-hint">{{ $card['hint'] }}</span>
                </span>
                <span class="pr-stat-icon" aria-hidden="true"><i class="fas {{ $card['icon'] }}"></i></span>
            </button>
        @endforeach
    </div>

    @if($rows->isNotEmpty())
    <!-- Chart report -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6" data-print-section="charts">
        <div class="relative overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm p-5 pl-6 print-card">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-[#0ea5e9]"></span>
            <h2 class="text-sm font-semibold text-gray-800 flex items-center">
                <i class="fas fa-chart-pie text-[#0ea5e9] mr-2"></i>Spend Share
            </h2>
            <p class="text-xs text-gray-500 mt-1">Purchase vs tender by amount (ETB)</p>
            <div class="relative h-56 mt-3">
                <canvas id="procurementShareChart"></canvas>
            </div>
        </div>

        <div class="relative overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm p-5 pl-6 print-card">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-amber-500"></span>
            <h2 class="text-sm font-semibold text-gray-800 flex items-center">
                <i class="fas fa-building text-amber-500 mr-2"></i>Top Suppliers
            </h2>
            <p class="text-xs text-gray-500 mt-1">Highest tender amounts by supplier (ETB)</p>
            <div class="relative h-56 mt-3">
                @if(count($chartSuppliers['labels']))
                    <canvas id="procurementSuppliersChart"></canvas>
                @else
                    <div class="h-full flex items-center justify-center text-sm text-gray-400">
                        <i class="fas fa-inbox mr-2"></i>No supplier data for this filter
                    </div>
                @endif
            </div>
        </div>

        <div class="relative overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm p-5 pl-6 print-card">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-indigo-500"></span>
            <h2 class="text-sm font-semibold text-gray-800 flex items-center">
                <i class="fas fa-layer-group text-indigo-500 mr-2"></i>Material Group Spend
            </h2>
            <p class="text-xs text-gray-500 mt-1">Tender amounts by material group (ETB)</p>
            <div class="relative h-56 mt-3">
                @if(count($chartMaterialGroups['labels']))
                    <canvas id="procurementMaterialGroupChart"></canvas>
                @else
                    <div class="h-full flex items-center justify-center text-sm text-gray-400">
                        <i class="fas fa-inbox mr-2"></i>No material group data for this filter
                    </div>
                @endif
            </div>
        </div>

        <div class="relative overflow-hidden lg:col-span-3 bg-white rounded-lg border border-gray-200 shadow-sm p-5 pl-6 print-card">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-[#0d9488]"></span>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div>
                    <h2 class="text-sm font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-chart-column text-[#0d9488] mr-2"></i>Monthly Procurement Trend
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Purchase and tender amounts per month (ETB)</p>
                </div>
                <span class="text-xs text-gray-500">{{ count($chartMonthly['labels']) }} month(s)</span>
            </div>
            <div class="relative h-72 mt-3">
                @if(count($chartMonthly['labels']))
                    <canvas id="procurementMonthlyChart"></canvas>
                @else
                    <div class="h-full flex items-center justify-center text-sm text-gray-400">
                        <i class="fas fa-inbox mr-2"></i>No dated records for this filter
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <form method="GET" action="{{ route('procurement-report.index') }}"
          class="bg-white rounded-lg shadow-sm border border-gray-200 px-5 py-4 mb-4 no-print">
        <input type="hidden" name="per_page" value="{{ $paginatedRows->perPage() }}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="from" class="block text-sm font-medium text-gray-700 mb-1">From</label>
                <input type="date" id="from" name="from" value="{{ $from }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0ea5e9]">
            </div>
            <div>
                <label for="to" class="block text-sm font-medium text-gray-700 mb-1">To</label>
                <input type="date" id="to" name="to" value="{{ $to }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0ea5e9]">
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="category" name="category"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0ea5e9]">
                    <option value="">All categories</option>
                    <option value="purchase" @selected($category === 'purchase')>By Purchase</option>
                    <option value="tender" @selected($category === 'tender')>By Tender</option>
                </select>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-[#0ea5e9] text-white font-medium rounded-lg hover:bg-[#0284c7] transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('procurement-report.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-rotate-left mr-2"></i>Reset
                </a>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-3">
            Date range applies to the purchase order approval date and the contract start date.
        </p>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 print-card" data-print-section="table">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 px-6 py-4 border-b border-gray-200">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Procurement Records</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $paginatedRows->total() }} record(s) &middot; page {{ $paginatedRows->currentPage() }} of {{ $paginatedRows->lastPage() }}
                </p>
            </div>
            <div class="pr-actions no-print">
                <button type="button" class="table-tip-btn" data-table-tip aria-expanded="false" aria-controls="tipProcurementReport"><i class="fas fa-info-circle"></i> TIP: info</button>
                <button type="button" class="pr-action-btn" data-pr-print-options data-print-default="table" title="Print this table only">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>

        <div class="px-6 py-3 no-print">
            <form method="GET" action="{{ route('procurement-report.index') }}" class="pr-perpage">
                @foreach(request()->except(['per_page', 'page']) as $key => $value)
                    @if(is_scalar($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <label for="prPerPage">Rows</label>
                <select name="per_page" id="prPerPage" onchange="this.form.submit()">
                    @foreach([5, 10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected($paginatedRows->perPage() === $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div id="tipProcurementReport" class="table-tip-panel no-print"><h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4><ul><li>From and To dates filter purchase orders by approval date and contracts by start date; Category limits results to purchases or tenders.</li><li>KPI cards show total procurement, the purchase versus tender split, and ICB and NCB tender totals; click a card to see its records with a pager.</li><li>Four charts (spend share, top suppliers, material group, monthly trend) follow the same filters and hide when a group has no data.</li><li>Records list category, type, reference, supplier, date and amount, newest first, with a No column and a Rows selector.</li><li>Footer totals for purchase, tender and grand total cover every filtered record, not only the rows on this page.</li><li>Print picks summary cards, charts or the table; Excel and PDF reuse the active filters and date range.</li></ul></div>

        <div class="pr-table-wrap">
            <table class="pr-table">
                <thead>
                    <tr>
                        <th class="pr-roll">No</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Reference</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th class="pr-amount">Amount (ETB)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paginatedRows as $row)
                    <tr>
                        <td class="pr-roll">{{ ($paginatedRows->firstItem() ?? 1) + $loop->index }}</td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($row->category === 'Purchase') bg-blue-100 text-blue-800
                                @else bg-teal-100 text-teal-800 @endif">
                                {{ $row->category }}
                            </span>
                        </td>
                        <td>{{ $row->type }}</td>
                        <td style="font-weight:600;">{{ $row->reference }}</td>
                        <td>{{ $row->supplier }}</td>
                        <td>{{ $row->date?->format('M d, Y') ?? '-' }}</td>
                        <td class="pr-amount">{{ number_format($row->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="pr-empty">
                            <i class="fas fa-chart-pie" style="display:block; font-size:1.8rem; margin-bottom:0.4rem;"></i>
                            No procurement amounts found. Add amounts to purchase orders or contracts, or adjust the filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($paginatedRows->count() > 0)
                <tfoot style="background:#f3f7fc;">
                    <tr>
                        <td colspan="6" style="text-align:right; font-weight:600;">Total by Purchase</td>
                        <td class="pr-amount" style="font-weight:600;">{{ number_format($purchaseTotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align:right; font-weight:600;">Total by Tender</td>
                        <td class="pr-amount" style="font-weight:600;">{{ number_format($tenderTotal, 2) }}</td>
                    </tr>
                    <tr style="border-top:2px solid #d7e2ee;">
                        <td colspan="6" style="text-align:right; font-weight:700;">Total Procurement</td>
                        <td class="pr-amount" style="font-weight:700;">{{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        @if($paginatedRows->total() > 0)
            @php
                $prCurrent = $paginatedRows->currentPage();
                $prLast = $paginatedRows->lastPage();
                $prPages = [];
                for ($prPage = 1; $prPage <= $prLast; $prPage++) {
                    if ($prPage === 1 || $prPage === $prLast || abs($prPage - $prCurrent) <= 1) {
                        $prPages[] = $prPage;
                    } elseif (end($prPages) !== '...') {
                        $prPages[] = '...';
                    }
                }
            @endphp
            <div class="pr-pagination no-print">
                <span>
                    Showing <strong>{{ $paginatedRows->firstItem() }}–{{ $paginatedRows->lastItem() }}</strong>
                    of <strong>{{ $paginatedRows->total() }}</strong>
                </span>
                <div class="pr-pages">
                    @if($prCurrent > 1)
                        <a class="pr-page-btn" href="{{ $paginatedRows->url($prCurrent - 1) }}" rel="prev">« Prev</a>
                    @else
                        <button type="button" class="pr-page-btn" disabled>« Prev</button>
                    @endif
                    @foreach($prPages as $prPage)
                        @if($prPage === '...')
                            <span class="pr-page-dots">...</span>
                        @else
                            <a class="pr-page-btn {{ $prPage === $prCurrent ? 'pr-active' : '' }}" href="{{ $paginatedRows->url($prPage) }}" @if($prPage === $prCurrent) aria-current="page" @endif>{{ $prPage }}</a>
                        @endif
                    @endforeach
                    @if($prCurrent < $prLast)
                        <a class="pr-page-btn" href="{{ $paginatedRows->url($prCurrent + 1) }}" rel="next">Next »</a>
                    @else
                        <button type="button" class="pr-page-btn" disabled>Next »</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@foreach($statCards as $card)
    <div class="pr-modal-bg" id="{{ $card['id'] }}" role="dialog" aria-modal="true" aria-labelledby="{{ $card['id'] }}Title">
        <div class="pr-modal">
            <div class="pr-modal-head">
                <div>
                    <h3 id="{{ $card['id'] }}Title">{{ $card['label'] }}</h3>
                    <span class="pr-modal-sub">{{ $card['rows']->count() }} record(s) &middot; ETB {{ number_format($card['amount'], 2) }}</span>
                </div>
                <button type="button" class="pr-modal-close" aria-label="Close" data-pr-close>×</button>
            </div>
            <div class="pr-modal-body">
                @if($card['rows']->isEmpty())
                    <div class="pr-empty">No procurement records in this group.</div>
                @else
                    <div class="pr-table-wrap">
                        <table class="pr-table">
                            <thead>
                                <tr>
                                    <th class="pr-roll">No</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Reference</th>
                                    <th>Supplier</th>
                                    <th>Date</th>
                                    <th class="pr-amount">Amount (ETB)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($card['rows'] as $row)
                                    <tr>
                                        <td class="pr-roll">{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($row->category === 'Purchase') bg-blue-100 text-blue-800
                                                @else bg-teal-100 text-teal-800 @endif">
                                                {{ $row->category }}
                                            </span>
                                        </td>
                                        <td>{{ $row->type }}</td>
                                        <td style="font-weight:600;">{{ $row->reference }}</td>
                                        <td>{{ $row->supplier }}</td>
                                        <td>{{ $row->date?->format('M d, Y') ?? '-' }}</td>
                                        <td class="pr-amount">{{ number_format($row->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="pr-modal-foot">
                <span class="pr-modal-range"></span>
                <div class="pr-modal-pages"></div>
            </div>
        </div>
    </div>
@endforeach

<div class="pr-modal-bg" id="prModalPrint" role="dialog" aria-modal="true" aria-labelledby="prPrintTitle">
    <div class="pr-modal pr-modal-sm">
        <div class="pr-modal-head">
            <div>
                <h3 id="prPrintTitle">Print Procurement Report</h3>
                <span>Choose which sections appear on the printed page</span>
            </div>
            <button type="button" class="pr-modal-close" aria-label="Close" data-pr-close>×</button>
        </div>
        <div class="pr-modal-body">
            <div class="pr-print-options">
                <label><input type="checkbox" class="pr-print-check" value="cards"> Summary cards</label>
                <label><input type="checkbox" class="pr-print-check" value="charts"> Charts</label>
                <label><input type="checkbox" class="pr-print-check" value="table" checked> Records table</label>
            </div>
            <p id="prPrintHint" class="pr-print-hint" style="display:none;">Select at least one section to print.</p>
        </div>
        <div class="pr-modal-foot" style="display:flex;">
            <span></span>
            <div class="pr-modal-pages">
                <button type="button" class="pr-action-btn" data-pr-close>Cancel</button>
                <button type="button" class="inline-flex items-center px-3 py-1.5 bg-[#0ea5e9] text-white text-xs font-semibold rounded-lg hover:bg-[#0284c7]" id="prPrintGo">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') return;

        const formatAmount = (value) => Number(value || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        const compactTick = (value) => {
            const number = Number(value || 0);
            if (Math.abs(number) >= 1000000) return (number / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
            if (Math.abs(number) >= 1000) return (number / 1000).toFixed(0) + 'K';
            return number.toFixed(0);
        };

        const moneyTooltip = (ctx) => {
            const raw = ctx.parsed;
            const value = raw && typeof raw === 'object'
                ? (raw.y ?? raw.x ?? raw.r ?? 0)
                : raw;
            return (ctx.dataset.label ? ctx.dataset.label + ': ' : '') + 'ETB ' + formatAmount(value);
        };

        const shareCanvas = document.getElementById('procurementShareChart');
        if (shareCanvas) {
            new Chart(shareCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['By Purchase', 'By Tender'],
                    datasets: [{
                        data: @json([$chartShare['purchase'], $chartShare['tender']]),
                        backgroundColor: ['#0ea5e9', '#0d9488'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { callbacks: { label: moneyTooltip } },
                    },
                },
            });
        }

        const monthlyCanvas = document.getElementById('procurementMonthlyChart');
        if (monthlyCanvas) {
            new Chart(monthlyCanvas, {
                type: 'bar',
                data: {
                    labels: @json($chartMonthly['labels']),
                    datasets: [
                        {
                            label: 'Purchase',
                            data: @json($chartMonthly['purchase']),
                            backgroundColor: '#0ea5e9',
                            borderRadius: 4,
                            maxBarThickness: 30,
                        },
                        {
                            label: 'Tender',
                            data: @json($chartMonthly['tender']),
                            backgroundColor: '#0d9488',
                            borderRadius: 4,
                            maxBarThickness: 30,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { callbacks: { label: moneyTooltip } },
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            beginAtZero: true,
                            ticks: { callback: compactTick },
                            title: { display: true, text: 'ETB' },
                        },
                    },
                },
            });
        }

        const suppliersCanvas = document.getElementById('procurementSuppliersChart');
        if (suppliersCanvas) {
            new Chart(suppliersCanvas, {
                type: 'bar',
                data: {
                    labels: @json($chartSuppliers['labels']),
                    datasets: [{
                        label: 'Amount',
                        data: @json($chartSuppliers['amounts']),
                        backgroundColor: ['#f59e0b', '#fb923c', '#fbbf24', '#f97316', '#fcd34d'],
                        borderRadius: 4,
                        maxBarThickness: 26,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: moneyTooltip } },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { callback: compactTick },
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                callback: function (value) {
                                    const label = this.getLabelForValue(value);
                                    return label.length > 18 ? label.slice(0, 17) + '…' : label;
                                },
                            },
                        },
                    },
                },
            });
        }

        const materialCanvas = document.getElementById('procurementMaterialGroupChart');
        if (materialCanvas) {
            new Chart(materialCanvas, {
                type: 'polarArea',
                data: {
                    labels: @json($chartMaterialGroups['labels']),
                    datasets: [{
                        data: @json($chartMaterialGroups['amounts']),
                        backgroundColor: [
                            'rgba(14, 165, 233, 0.75)',
                            'rgba(13, 148, 136, 0.75)',
                            'rgba(99, 102, 241, 0.75)',
                            'rgba(245, 158, 11, 0.75)',
                            'rgba(236, 72, 153, 0.75)',
                            'rgba(34, 197, 94, 0.75)',
                        ],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { callbacks: { label: moneyTooltip } },
                    },
                    scales: {
                        r: {
                            ticks: { display: false },
                            grid: { color: 'rgba(148, 163, 184, 0.25)' },
                        },
                    },
                },
            });
        }
    });

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
            if (!document.querySelector('.pr-modal-bg.open')) {
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
            var table = modal.querySelector('.pr-modal-body .pr-table');
            if (!table) return;

            var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
            if (!rows.length) return;

            var foot = modal.querySelector('.pr-modal-foot');
            var rangeNode = foot && foot.querySelector('.pr-modal-range');
            var pagesNode = foot && foot.querySelector('.pr-modal-pages');
            if (!foot || !rangeNode || !pagesNode) return;

            var state = modal.prPager;
            if (!state) {
                state = modal.prPager = { rows: rows, page: 1, foot: foot, range: rangeNode, pages: pagesNode };
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
                    dots.className = 'pr-page-dots';
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
                var active = state.pages.querySelector('.pr-active');
                if (active) active.focus();
            }
        }

        function pagerButton(label, disabled, active, onClick) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'pr-page-btn' + (active ? ' pr-active' : '');
            button.textContent = label;
            button.disabled = disabled;
            button.addEventListener('click', onClick);
            return button;
        }

        function goToModalPage(state, page) {
            state.page = page;
            paintModalPage(state, true);
            var body = state.foot.parentNode.querySelector('.pr-modal-body');
            if (body) body.scrollTop = 0;
        }

        var printModal = document.getElementById('prModalPrint');
        var printChecks = Array.prototype.slice.call(document.querySelectorAll('.pr-print-check'));
        var printHint = document.getElementById('prPrintHint');

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

        Array.prototype.forEach.call(document.querySelectorAll('[data-pr-modal]'), function (button) {
            button.addEventListener('click', function () {
                var modal = document.getElementById(button.getAttribute('data-pr-modal'));
                initModalPager(modal);
                openModalEl(modal);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('.pr-modal-bg'), function (backdrop) {
            backdrop.addEventListener('click', function (event) {
                if (event.target === backdrop) closeModal(backdrop);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-pr-close]'), function (button) {
            button.addEventListener('click', function () {
                closeModal(button.closest('.pr-modal-bg'));
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-pr-print-options]'), function (button) {
            button.addEventListener('click', function () {
                openPrintOptions(button.getAttribute('data-print-default'));
            });
        });

        var printGo = document.getElementById('prPrintGo');
        if (printGo) {
            printGo.addEventListener('click', printSelectedSections);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;
            var open = document.querySelectorAll('.pr-modal-bg.open');
            if (open.length) {
                closeModal(open[open.length - 1]);
            }
        });
    })();
</script>
@endsection
