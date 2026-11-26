@extends('layouts.app')

@section('title', 'Contract Closing')

@section('styles')
<style>
    .cc-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .cc-stat {
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
    .cc-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
        border-left-color: var(--card-color, #0d9488);
    }
    .cc-stat:focus-visible { outline: 2px solid var(--card-color, #0d9488); outline-offset: 2px; }
    .cc-stat-main { min-width: 0; }
    .cc-stat-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
    }
    .cc-stat-value {
        display: block;
        font-size: 1.7rem;
        font-weight: 700;
        color: #0f172a;
        margin-top: 0.3rem;
        line-height: 1;
    }
    .cc-stat-hint {
        display: block;
        margin-top: 0.55rem;
        font-size: 0.68rem;
        font-weight: 600;
        color: #8aa2b8;
        opacity: 0;
        transition: opacity 0.15s ease, color 0.15s ease;
    }
    .cc-stat:hover .cc-stat-hint,
    .cc-stat:focus-visible .cc-stat-hint { opacity: 1; color: #0f766e; }
    .cc-stat-icon {
        flex: none;
        padding-top: 0.1rem;
        font-size: 1.9rem;
        line-height: 1;
        color: var(--card-color, #0d9488);
        opacity: 0.22;
    }

    .cc-action-btn {
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
    .cc-action-btn:hover { background: #f1f5f9; }
    .cc-actions { display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap; }
    .cc-action-btn-active { background: #ccfbf1; border-color: #99f6e4; color: #0f766e; }

    .cc-perpage { display: flex; align-items: center; gap: 0.35rem; }
    .cc-perpage label { font-size: 0.72rem; font-weight: 600; color: #6b8299; margin: 0; }
    .cc-perpage select {
        padding: 0.35rem 0.5rem;
        border: 1px solid #cfdcea;
        border-radius: 7px;
        font-size: 0.75rem;
        background: #fff;
        color: #1e293b;
    }

    .cc-pagination {
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
    .cc-pagination strong { color: #1e293b; font-weight: 600; }
    .cc-pages { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; margin-left: auto; }
    .cc-page-btn {
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
    .cc-page-btn:hover:not(:disabled) { background: #f1f5f9; }
    .cc-page-btn.cc-active { background: #0d9488; border-color: #0d9488; color: #fff; }
    .cc-page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .cc-page-dots { padding: 0.35rem 0.3rem; color: #6b8299; }

    .cc-modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(10, 25, 45, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1700;
        padding: 1rem;
    }
    .cc-modal-bg.open { display: flex; }
    .cc-modal {
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
    .cc-modal.cc-modal-sm { max-width: 480px; }
    .cc-modal.cc-modal-md { max-width: 640px; }
    .cc-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.3rem;
        border-bottom: 1px solid #edf2f8;
    }
    .cc-modal-head h3 { margin: 0; font-size: 1.05rem; color: #0f2a44; }
    .cc-modal-head span { font-size: 0.75rem; color: #6b8299; }
    .cc-modal-close {
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
    .cc-modal-close:hover { background: #dbe6f1; }
    .cc-modal-body { padding: 1rem 1.3rem 1.4rem; overflow-y: auto; }
    .cc-modal-foot {
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
    .cc-modal-foot strong { color: #1e293b; font-weight: 600; }
    .cc-modal-pages { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; }

    .cc-table-wrap { overflow-x: auto; }
    .cc-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    .cc-table th {
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
    .cc-table td {
        padding: 0.7rem 0.6rem;
        border-bottom: 1px solid #edf2f8;
        color: #1e293b;
        vertical-align: top;
    }
    .cc-table tbody tr:hover td { background: #f8fbff; }
    .cc-table th.cc-roll,
    .cc-table td.cc-roll {
        width: 3.2rem;
        color: #7a90a6;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .cc-empty { text-align: center; color: #8ca3b8; padding: 2rem 1rem; font-style: italic; }

    .cc-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.68rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .cc-badge.green { background: #dcfce7; color: #166534; }
    .cc-badge.amber { background: #fef3c7; color: #92400e; }
    .cc-modal-sub { font-size: 0.72rem; color: #7a90a6; }

    .cc-print-options { display: flex; flex-direction: column; gap: 0.55rem; }
    .cc-print-options label {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        font-size: 0.88rem;
        color: #1e293b;
        cursor: pointer;
    }
    .cc-print-options input { width: 16px; height: 16px; accent-color: #0d9488; }
    .cc-print-hint { margin: 0.7rem 0 0; font-size: 0.78rem; font-weight: 600; color: #b91c1c; }

    .cc-tip {
        display: none;
        margin: 1rem 1.5rem 0;
        padding: 0.9rem 1.1rem;
        border: 1px solid #bfdbfe;
        border-left: 4px solid #3b82f6;
        border-radius: 10px;
        background: #eff6ff;
        font-size: 0.82rem;
        color: #1e3a5f;
    }
    .cc-tip.open { display: block; }
    .cc-tip h4 {
        margin: 0 0 0.5rem;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #1d4ed8;
    }
    .cc-tip ul { margin: 0; padding-left: 1.1rem; }
    .cc-tip li { margin-bottom: 0.3rem; line-height: 1.45; }
    .cc-tip li:last-child { margin-bottom: 0; }
    .cc-tip strong { color: #0f2a44; }

    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        .sidebar, .sidebar-toggle, .page-header, .page-footer, .no-print,
        .cc-modal-bg { display: none !important; }
        .print-hide-section { display: none !important; }
        body { background: #fff; }
        .main-content, .main-content.expanded { margin-left: 0 !important; }
        .cc-page { max-width: none !important; margin: 0 !important; padding: 0 !important; }
        .cc-stat { box-shadow: none; cursor: default; }
        .cc-table-card { box-shadow: none; border: 1px solid #c9d7e6; }
        .cc-table-scroll { overflow: visible !important; }
        .cc-main-table thead { display: table-header-group; }
        .cc-main-table th, .cc-main-table td { border: 1px solid #9aa9b8; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endsection

@section('content')
@php
    $statCards = [
        ['key' => 'total', 'label' => 'Total Closings', 'color' => '#0d9488', 'icon' => 'fa-file-signature', 'id' => 'ccModalTotal', 'hint' => 'Click to view all closings →', 'view' => 'View all contract closings'],
        ['key' => 'pending', 'label' => 'Pending', 'color' => '#f59e0b', 'icon' => 'fa-hourglass-half', 'id' => 'ccModalPending', 'hint' => 'Click to view the pending queue →', 'view' => 'View pending contract closings'],
        ['key' => 'complete', 'label' => 'Complete', 'color' => '#10b981', 'icon' => 'fa-circle-check', 'id' => 'ccModalComplete', 'hint' => 'Click to view completed closings →', 'view' => 'View completed contract closings'],
        ['key' => 'today', 'label' => 'Closing Today', 'color' => '#3b82f6', 'icon' => 'fa-calendar-day', 'id' => 'ccModalToday', 'hint' => 'Click to view today&rsquo;s closings →', 'view' => 'View contract closings dated today'],
    ];
@endphp

<div class="cc-page container mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-file-signature text-[#0d9488] mr-3"></i>
                Contract Closing
            </h1>
            <p class="text-gray-600 mt-2">Track daily contract closings from the closing request through completion.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 no-print">
            <button type="button" data-cc-print-options data-print-default="table"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <a href="{{ route('contract-closures.export.excel', request()->query()) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-excel mr-2"></i>Excel
            </a>
            <a href="{{ route('contract-closures.export.pdf', request()->query()) }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-file-pdf mr-2"></i>PDF
            </a>
            @if(auth()->user()->canCreateContractClosures())
            <button type="button" onclick="openClosureModal()"
                    class="inline-flex items-center px-4 py-2 bg-[#0d9488] text-white font-medium rounded-lg hover:bg-[#0f766e] transition">
                <i class="fas fa-plus mr-2"></i>
                New Closing Request
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

    <!-- Summary cards -->
    <div class="cc-stats" data-print-section="cards">
        @foreach($statCards as $card)
            @php $statClosures = $statGroups[$card['key']]; @endphp
            <button type="button" class="cc-stat" style="--card-color: {{ $card['color'] }};"
                    data-cc-modal="{{ $card['id'] }}" aria-haspopup="dialog"
                    aria-label="{{ $card['view'] }}" title="{{ $card['view'] }}">
                <span class="cc-stat-main">
                    <span class="cc-stat-label">{{ $card['label'] }}</span>
                    <span class="cc-stat-value">{{ $statClosures->count() }}</span>
                    <span class="cc-stat-hint">{!! $card['hint'] !!}</span>
                </span>
                <span class="cc-stat-icon" aria-hidden="true"><i class="fas {{ $card['icon'] }}"></i></span>
            </button>
        @endforeach
    </div>

    <!-- Table -->
    <div class="cc-table-card bg-white rounded-lg shadow-sm border border-gray-200" data-print-section="table">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 px-6 py-4 border-b border-gray-200">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Contract Closings</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    @if($search !== '')
                        {{ $closures->total() }} result(s) for &ldquo;{{ $search }}&rdquo;
                    @else
                        {{ $closures->total() }} record(s)
                    @endif
                    &middot; page {{ $closures->currentPage() }} of {{ $closures->lastPage() }}
                </p>
            </div>
            <div class="cc-actions no-print">
                <button type="button" class="cc-action-btn" data-cc-tip aria-expanded="false" aria-controls="tipContractClosures">
                    <i class="fas fa-info-circle"></i> TIP: info
                </button>
                <button type="button" class="cc-action-btn" data-cc-print-options data-print-default="table" title="Print this table only">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>

        <div class="px-6 py-3 border-b border-gray-100 no-print">
            <form method="GET" action="{{ route('contract-closures.index') }}" class="flex flex-wrap items-end gap-2">
                <input type="hidden" name="per_page" value="{{ $closures->perPage() }}">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ $search }}" autocomplete="off"
                           aria-label="Search"
                           placeholder="Search contract or supplier..."
                           class="w-full sm:w-64 rounded-lg border border-gray-300 pl-9 pr-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d9488]">
                </div>
                <select name="status" aria-label="Status"
                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d9488]">
                    <option value="">All statuses</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="flex items-center gap-1">
                    <span class="text-xs font-semibold text-gray-500">From</span>
                    <input type="date" name="from" value="{{ request('from') }}" aria-label="Closing date from"
                           class="rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d9488]">
                </div>
                <div class="flex items-center gap-1">
                    <span class="text-xs font-semibold text-gray-500">To</span>
                    <input type="date" name="to" value="{{ request('to') }}" aria-label="Closing date to"
                           class="rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d9488]">
                </div>
                <button type="submit"
                        class="px-3 py-1.5 bg-[#0d9488] text-white text-sm font-medium rounded-lg hover:bg-[#0f766e] transition">
                    <i class="fas fa-filter mr-1"></i>Filter
                </button>
                <a href="{{ route('contract-closures.index') }}"
                   class="px-3 py-1.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-rotate-left mr-1"></i>Reset
                </a>
            </form>
            <form method="GET" action="{{ route('contract-closures.index') }}" class="cc-perpage mt-2">
                @foreach(request()->except(['per_page', 'page']) as $key => $value)
                    @if(is_scalar($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <label for="ccPerPage">Rows</label>
                <select name="per_page" id="ccPerPage" onchange="this.form.submit()">
                    @foreach([5, 10, 25, 50] as $size)
                        <option value="{{ $size }}" @selected($closures->perPage() === $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div id="tipContractClosures" class="cc-tip no-print">
            <h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4>
            <ul>
                <li>Search matches contract or supplier name; <strong>Status</strong>, <strong>From</strong> and <strong>To</strong> narrow the closing date range, then press <strong>Filter</strong>.</li>
                <li>Each row lists the contract (with supplier), department, contract end date, closing date, status and actions.</li>
                <li><strong>Complete</strong> turns a pending closing into a completed one after a confirmation prompt, and only appears for users allowed to complete closures.</li>
                <li>Green <strong>Complete</strong> badges show who completed the closing and when; amber badges are still pending.</li>
                <li>The KPI cards open the full list regardless of the search, filters or page size; <strong>Rows</strong> changes the page size.</li>
                <li>Excel, PDF and Print follow the current filters.</li>
            </ul>
        </div>

        <div class="cc-table-scroll overflow-x-auto">
            <table class="cc-main-table min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-4 py-3 font-medium cc-roll">No</th>
                        <th class="px-6 py-3 font-medium">Contract</th>
                        <th class="px-6 py-3 font-medium">Department</th>
                        <th class="px-6 py-3 font-medium">Contract End Date</th>
                        <th class="px-6 py-3 font-medium">Closing Date</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium no-print">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($closures as $closure)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-4 py-4 text-gray-400 cc-roll">{{ ($closures->firstItem() ?? 1) + $loop->index }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $closure->contract->name ?? 'Deleted contract' }}</div>
                            <div class="text-xs text-gray-500">{{ $closure->contract->supplier_name ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            {{ $closure->contract ? $closure->contract->getDepartmentDisplayName() : '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                            {{ $closure->contract?->end_date?->format('M d, Y') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap">{{ $closure->closing_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($closure->isComplete()) bg-green-100 text-green-800
                                @else bg-amber-100 text-amber-800 @endif">
                                @if($closure->isComplete())
                                    <i class="fas fa-check mr-1"></i>
                                @endif
                                {{ $closure->getStatusLabel() }}
                            </span>
                            @if($closure->isComplete())
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $closure->completer->name ?? 'Unknown' }}
                                    &middot; {{ $closure->completed_at?->format('M d, Y H:i') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 no-print">
                            <div class="flex flex-wrap items-center gap-2">
                                @if(!$closure->isComplete() && auth()->user()->canCompleteContractClosures())
                                <form method="POST" action="{{ route('contract-closures.complete', $closure) }}"
                                      onsubmit="return confirm('Mark this contract closing as complete?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-3 py-1 text-xs font-medium bg-green-600 text-white rounded hover:bg-green-700">
                                        <i class="fas fa-check mr-1"></i>Complete
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('contract-closures.show', $closure) }}"
                                   class="inline-flex items-center px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition"
                                   title="View closing details">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <i class="fas fa-file-signature text-4xl text-gray-300"></i>
                            @if($search !== '' || request()->hasAny(['status', 'from', 'to']))
                                <p class="mt-4 text-gray-700 font-medium">No contract closings match the current filters</p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Try adjusting the search, status or date range, or
                                    <a href="{{ route('contract-closures.index') }}" class="text-[#0d9488] hover:underline">reset the filters</a>.
                                </p>
                            @else
                                <p class="mt-4 text-gray-700 font-medium">No contract closings yet</p>
                                <p class="mt-1 text-sm text-gray-500">Create a closing request to start tracking daily closings.</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($closures->total() > 0)
            @php
                $ccCurrent = $closures->currentPage();
                $ccLast = $closures->lastPage();
                $ccPages = [];
                for ($ccPage = 1; $ccPage <= $ccLast; $ccPage++) {
                    if ($ccPage === 1 || $ccPage === $ccLast || abs($ccPage - $ccCurrent) <= 1) {
                        $ccPages[] = $ccPage;
                    } elseif (end($ccPages) !== '...') {
                        $ccPages[] = '...';
                    }
                }
            @endphp
            <div class="cc-pagination no-print">
                <span>
                    Showing <strong>{{ $closures->firstItem() }}–{{ $closures->lastItem() }}</strong>
                    of <strong>{{ $closures->total() }}</strong>
                </span>
                <div class="cc-pages">
                    @if($ccCurrent > 1)
                        <a class="cc-page-btn" href="{{ $closures->url($ccCurrent - 1) }}" rel="prev">« Prev</a>
                    @else
                        <button type="button" class="cc-page-btn" disabled>« Prev</button>
                    @endif
                    @foreach($ccPages as $ccPage)
                        @if($ccPage === '...')
                            <span class="cc-page-dots">...</span>
                        @else
                            <a class="cc-page-btn {{ $ccPage === $ccCurrent ? 'cc-active' : '' }}" href="{{ $closures->url($ccPage) }}" @if($ccPage === $ccCurrent) aria-current="page" @endif>{{ $ccPage }}</a>
                        @endif
                    @endforeach
                    @if($ccCurrent < $ccLast)
                        <a class="cc-page-btn" href="{{ $closures->url($ccCurrent + 1) }}" rel="next">Next »</a>
                    @else
                        <button type="button" class="cc-page-btn" disabled>Next »</button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <p class="text-xs text-gray-500 mt-4">
        <i class="fas fa-info-circle mr-1"></i>
        A closing stays Pending until Finance or an administrator marks it Complete.
    </p>
</div>

@if(auth()->user()->canCreateContractClosures())
<!-- New Closing Request modal -->
<div id="closureModal" class="cc-modal-bg" role="dialog" aria-modal="true" aria-labelledby="closureModalTitle">
    <div class="cc-modal cc-modal-md">
        <div class="cc-modal-head">
            <div>
                <h3 id="closureModalTitle"><i class="fas fa-file-signature text-[#0d9488] mr-2"></i>New Closing Request</h3>
                <span>The closing stays Pending until Finance or an administrator completes it.</span>
            </div>
            <button type="button" class="cc-modal-close" aria-label="Close" onclick="closeClosureModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('contract-closures.store') }}">
            @csrf
            <div class="cc-modal-body">
                <div class="space-y-4">
                    <div>
                        <label for="contract_id" class="block text-sm font-medium text-gray-700 mb-1">Contract <span class="text-red-500">*</span></label>
                        <select id="contract_id" name="contract_id" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0d9488]">
                            <option value="">Select a contract</option>
                            @foreach($contracts as $contract)
                                <option value="{{ $contract->id }}" @selected((string) old('contract_id') === (string) $contract->id)>
                                    {{ $contract->name }} &mdash; {{ $contract->getDepartmentDisplayName() }}@if($contract->end_date) &mdash; ends {{ $contract->end_date->format('M d, Y') }}@endif
                                </option>
                            @endforeach
                        </select>
                        @error('contract_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        @if($contracts->isEmpty())
                            <p class="text-xs text-gray-500 mt-1">Every contract available to you already has a closing record.</p>
                        @endif
                    </div>
                    <div>
                        <label for="closing_date" class="block text-sm font-medium text-gray-700 mb-1">Closing Date <span class="text-red-500">*</span></label>
                        <input type="date" id="closing_date" name="closing_date"
                               value="{{ old('closing_date', now()->format('Y-m-d')) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0d9488]"
                               required>
                        @error('closing_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks <span class="text-gray-400 font-normal">(optional)</span></label>
                        <textarea id="remarks" name="remarks" rows="3" maxlength="1000"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0d9488]"
                                  placeholder="Reason or notes for closing this contract">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button type="button" onclick="closeClosureModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-white transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-[#0d9488] text-white font-medium rounded-lg hover:bg-[#0f766e] transition">
                    Save Closing Request
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@foreach($statCards as $card)
    @php $modalClosures = $statGroups[$card['key']]; @endphp
    <div class="cc-modal-bg" id="{{ $card['id'] }}" role="dialog" aria-modal="true" aria-labelledby="{{ $card['id'] }}Title">
        <div class="cc-modal">
            <div class="cc-modal-head">
                <div>
                    <h3 id="{{ $card['id'] }}Title">{{ $card['label'] }}</h3>
                    <span>{{ $modalClosures->count() }} record(s)</span>
                </div>
                <button type="button" class="cc-modal-close" aria-label="Close" data-cc-close>×</button>
            </div>
            <div class="cc-modal-body">
                @if($modalClosures->isEmpty())
                    <div class="cc-empty">No contract closings in this group.</div>
                @else
                    <div class="cc-table-wrap">
                        <table class="cc-table">
                            <thead>
                                <tr>
                                    <th class="cc-roll">No</th>
                                    <th>Contract</th>
                                    <th>Department</th>
                                    <th>Contract End Date</th>
                                    <th>Closing Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modalClosures as $closure)
                                    <tr>
                                        <td class="cc-roll">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="font-semibold">{{ $closure->contract->name ?? 'Deleted contract' }}</div>
                                            <div class="cc-modal-sub">{{ $closure->contract->supplier_name ?? '' }}</div>
                                        </td>
                                        <td>{{ $closure->contract ? $closure->contract->getDepartmentDisplayName() : '-' }}</td>
                                        <td class="whitespace-nowrap">{{ $closure->contract?->end_date?->format('M d, Y') ?? '-' }}</td>
                                        <td class="whitespace-nowrap">{{ $closure->closing_date?->format('M d, Y') ?? '-' }}</td>
                                        <td>
                                            <span class="cc-badge {{ $closure->isComplete() ? 'green' : 'amber' }}">
                                                @if($closure->isComplete())
                                                    <i class="fas fa-check"></i>
                                                @endif
                                                {{ $closure->getStatusLabel() }}
                                            </span>
                                            @if($closure->isComplete())
                                                <div class="cc-modal-sub">
                                                    {{ $closure->completer->name ?? 'Unknown' }}
                                                    &middot; {{ $closure->completed_at?->format('M d, Y H:i') }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="cc-modal-foot">
                <span class="cc-modal-range"></span>
                <div class="cc-modal-pages"></div>
            </div>
        </div>
    </div>
@endforeach

<div class="cc-modal-bg" id="ccModalPrint" role="dialog" aria-modal="true" aria-labelledby="ccPrintTitle">
    <div class="cc-modal cc-modal-sm">
        <div class="cc-modal-head">
            <div>
                <h3 id="ccPrintTitle">Print Contract Closings</h3>
                <span>Choose which sections appear on the printed page</span>
            </div>
            <button type="button" class="cc-modal-close" aria-label="Close" data-cc-close>×</button>
        </div>
        <div class="cc-modal-body">
            <div class="cc-print-options">
                <label><input type="checkbox" class="cc-print-check" value="cards"> Summary cards</label>
                <label><input type="checkbox" class="cc-print-check" value="table" checked> Contract closings table</label>
            </div>
            <p id="ccPrintHint" class="cc-print-hint" style="display:none;">Select at least one section to print.</p>
        </div>
        <div class="cc-modal-foot" style="display:flex;">
            <span></span>
            <div class="cc-modal-pages">
                <button type="button" class="cc-action-btn" data-cc-close>Cancel</button>
                <button type="button" class="inline-flex items-center px-3 py-1.5 bg-[#0d9488] text-white text-xs font-semibold rounded-lg hover:bg-[#0f766e]" id="ccPrintGo">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openClosureModal() {
        var modal = document.getElementById('closureModal');
        if (!modal) return;
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
        var select = document.getElementById('contract_id');
        if (select) select.focus();
    }

    function closeClosureModal() {
        var modal = document.getElementById('closureModal');
        if (!modal) return;
        modal.classList.remove('open');
        if (!document.querySelector('.cc-modal-bg.open')) {
            document.body.style.overflow = '';
        }
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
            if (!document.querySelector('.cc-modal-bg.open')) {
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
            var table = modal.querySelector('.cc-modal-body .cc-table');
            if (!table) return;

            var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
            if (!rows.length) return;

            var foot = modal.querySelector('.cc-modal-foot');
            var rangeNode = foot && foot.querySelector('.cc-modal-range');
            var pagesNode = foot && foot.querySelector('.cc-modal-pages');
            if (!foot || !rangeNode || !pagesNode) return;

            var state = modal.ccPager;
            if (!state) {
                state = modal.ccPager = { rows: rows, page: 1, foot: foot, range: rangeNode, pages: pagesNode };
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
                    dots.className = 'cc-page-dots';
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
                var active = state.pages.querySelector('.cc-active');
                if (active) active.focus();
            }
        }

        function pagerButton(label, disabled, active, onClick) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'cc-page-btn' + (active ? ' cc-active' : '');
            button.textContent = label;
            button.disabled = disabled;
            button.addEventListener('click', onClick);
            return button;
        }

        function goToModalPage(state, page) {
            state.page = page;
            paintModalPage(state, true);
            var body = state.foot.parentNode.querySelector('.cc-modal-body');
            if (body) body.scrollTop = 0;
        }

        var printModal = document.getElementById('ccModalPrint');
        var printChecks = Array.prototype.slice.call(document.querySelectorAll('.cc-print-check'));
        var printHint = document.getElementById('ccPrintHint');

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

        Array.prototype.forEach.call(document.querySelectorAll('[data-cc-modal]'), function (button) {
            button.addEventListener('click', function () {
                var modal = document.getElementById(button.getAttribute('data-cc-modal'));
                initModalPager(modal);
                openModalEl(modal);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('.cc-modal-bg'), function (backdrop) {
            backdrop.addEventListener('click', function (event) {
                if (event.target === backdrop) closeModal(backdrop);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-cc-close]'), function (button) {
            button.addEventListener('click', function () {
                closeModal(button.closest('.cc-modal-bg'));
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-cc-print-options]'), function (button) {
            button.addEventListener('click', function () {
                openPrintOptions(button.getAttribute('data-print-default'));
            });
        });

        var printGo = document.getElementById('ccPrintGo');
        if (printGo) {
            printGo.addEventListener('click', printSelectedSections);
        }

        var tipButton = document.querySelector('[data-cc-tip]');
        var tipBox = document.getElementById('tipContractClosures');
        if (tipButton && tipBox) {
            tipButton.addEventListener('click', function () {
                var open = tipBox.classList.toggle('open');
                tipButton.setAttribute('aria-expanded', open ? 'true' : 'false');
                tipButton.classList.toggle('cc-action-btn-active', open);
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            closeClosureModal();
            var open = document.querySelectorAll('.cc-modal-bg.open');
            if (open.length) {
                closeModal(open[open.length - 1]);
            }
        });
    })();

    @if($errors->any())
    document.addEventListener('DOMContentLoaded', openClosureModal);
    @endif
</script>
@endsection
