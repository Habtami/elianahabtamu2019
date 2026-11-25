@extends('layouts.app')

@section('title', 'Purchasing Daily Activity')

@section('styles')
<style>
    .pa-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .pa-stat {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
        width: 100%;
        text-align: left;
        font: inherit;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-left: 4px solid var(--card-color, #1c75bc);
        border-radius: 12px;
        padding: 1.1rem 1.2rem;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .pa-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
        border-left-color: var(--card-color, #1c75bc);
    }
    .pa-stat:focus-visible { outline: 2px solid var(--card-color, #1c75bc); outline-offset: 2px; }
    .pa-stat-main { min-width: 0; }
    .pa-stat-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
    }
    .pa-stat-value {
        display: block;
        font-size: 1.7rem;
        font-weight: 700;
        color: #0f172a;
        margin-top: 0.3rem;
        line-height: 1;
    }
    .pa-stat-hint {
        display: block;
        margin-top: 0.55rem;
        font-size: 0.68rem;
        font-weight: 600;
        color: #8aa2b8;
        opacity: 0;
        transition: opacity 0.15s ease, color 0.15s ease;
    }
    .pa-stat:hover .pa-stat-hint,
    .pa-stat:focus-visible .pa-stat-hint { opacity: 1; color: #b45309; }
    .pa-stat-icon {
        flex: none;
        padding-top: 0.1rem;
        font-size: 1.9rem;
        line-height: 1;
        color: var(--card-color, #1c75bc);
        opacity: 0.22;
    }

    .pa-action-btn {
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
    .pa-action-btn:hover { background: #f1f5f9; }
    .pa-actions { display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap; }

    .pa-perpage { display: flex; align-items: center; gap: 0.35rem; }
    .pa-perpage label { font-size: 0.72rem; font-weight: 600; color: #6b8299; margin: 0; }
    .pa-perpage select {
        padding: 0.35rem 0.5rem;
        border: 1px solid #cfdcea;
        border-radius: 7px;
        font-size: 0.75rem;
        background: #fff;
        color: #1e293b;
    }

    .pa-pagination {
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
    .pa-pagination strong { color: #1e293b; font-weight: 600; }
    .pa-pages { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; margin-left: auto; }
    .pa-page-btn {
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
    .pa-page-btn:hover:not(:disabled) { background: #f1f5f9; }
    .pa-page-btn.pa-active { background: #b45309; border-color: #b45309; color: #fff; }
    .pa-page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .pa-page-dots { padding: 0.35rem 0.3rem; color: #6b8299; }

    .pa-modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(10, 25, 45, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1700;
        padding: 1rem;
    }
    .pa-modal-bg.open { display: flex; }
    .pa-modal {
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
    .pa-modal.pa-modal-sm { max-width: 480px; }
    .pa-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.3rem;
        border-bottom: 1px solid #edf2f8;
    }
    .pa-modal-head h3 { margin: 0; font-size: 1.05rem; color: #0f2a44; }
    .pa-modal-head span { font-size: 0.75rem; color: #6b8299; }
    .pa-modal-close {
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
    .pa-modal-close:hover { background: #dbe6f1; }
    .pa-modal-body { padding: 1rem 1.3rem 1.4rem; overflow-y: auto; }
    .pa-modal-foot {
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
    .pa-modal-foot strong { color: #1e293b; font-weight: 600; }
    .pa-modal-pages { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; }

    .pa-table-wrap { overflow-x: auto; }
    .pa-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    .pa-table th {
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
    .pa-table td {
        padding: 0.7rem 0.6rem;
        border-bottom: 1px solid #edf2f8;
        color: #1e293b;
        vertical-align: top;
    }
    .pa-table tbody tr:hover td { background: #f8fbff; }
    .pa-table th.pa-roll,
    .pa-table td.pa-roll {
        width: 3.2rem;
        color: #7a90a6;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .pa-empty { text-align: center; color: #8ca3b8; padding: 2rem 1rem; font-style: italic; }

    .pa-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.68rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .pa-badge.green { background: #dcfce7; color: #166534; }
    .pa-badge.amber { background: #fef3c7; color: #92400e; }
    .pa-badge.indigo { background: #e0e7ff; color: #3730a3; }
    .pa-badge.sky { background: #e0f2fe; color: #075985; }
    .pa-badge.teal { background: #ccfbf1; color: #115e59; }
    .pa-badge.blue { background: #dbeafe; color: #1d4ed8; }
    .pa-modal-sub { font-size: 0.72rem; color: #7a90a6; }

    .pa-print-options { display: flex; flex-direction: column; gap: 0.55rem; }
    .pa-print-options label {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        font-size: 0.88rem;
        color: #1e293b;
        cursor: pointer;
    }
    .pa-print-options input { width: 16px; height: 16px; accent-color: #b45309; }
    .pa-print-hint { margin: 0.7rem 0 0; font-size: 0.78rem; font-weight: 600; color: #b91c1c; }

    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        .sidebar, .sidebar-toggle, .page-header, .page-footer, .no-print,
        .pa-modal-bg { display: none !important; }
        .print-hide-section { display: none !important; }
        body { background: #fff; }
        .main-content, .main-content.expanded { margin-left: 0 !important; }
        .pa-page { max-width: none !important; margin: 0 !important; padding: 0 !important; }
        .pa-stat { box-shadow: none; cursor: default; }
        .pa-table-card { box-shadow: none; border: 1px solid #c9d7e6; }
        .pa-table-scroll { overflow: visible !important; }
        .pa-main-table thead { display: table-header-group; }
        .pa-main-table th, .pa-main-table td { border: 1px solid #9aa9b8; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endsection

@section('content')
@php
    $statCards = [
        ['key' => 'total', 'label' => 'Total Activities', 'color' => '#1C75BC', 'icon' => 'fa-clipboard-list', 'id' => 'paModalTotal', 'hint' => 'Click to view all activities →', 'view' => 'View all purchasing activities'],
        ['key' => 'pending', 'label' => 'Pending', 'color' => '#f59e0b', 'icon' => 'fa-hourglass-half', 'id' => 'paModalPending', 'hint' => 'Click to view the pending queue →', 'view' => 'View pending purchasing activities'],
        ['key' => 'complete', 'label' => 'Complete', 'color' => '#10b981', 'icon' => 'fa-circle-check', 'id' => 'paModalComplete', 'hint' => 'Click to view completed activities →', 'view' => 'View completed purchasing activities'],
        ['key' => 'today', 'label' => "Today's Requests", 'color' => '#b45309', 'icon' => 'fa-calendar-day', 'id' => 'paModalToday', 'hint' => 'Click to view today\'s requests →', 'view' => "View today's purchasing requests"],
    ];
@endphp

<div class="pa-page container mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-clipboard-list text-[#b45309] mr-3"></i>
                Purchasing Daily Activity
            </h1>
            <p class="text-gray-600 mt-2">Register proforma and request-for-quotation activity by material group, unit and procurement type.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 no-print">
            <button type="button" data-pa-print-options data-print-default="table"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            @if(auth()->user()->canCreatePurchasingActivities())
            <button type="button" onclick="openActivityModal()"
                    class="inline-flex items-center px-4 py-2 bg-[#b45309] text-white font-medium rounded-lg hover:bg-[#92400e] transition">
                <i class="fas fa-plus mr-2"></i>
                New Activity
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

    <!-- Daily status cards -->
    <div class="pa-stats" data-print-section="cards">
        @foreach($statCards as $card)
            @php $statActivities = $statGroups[$card['key']]; @endphp
            <button type="button" class="pa-stat" style="--card-color: {{ $card['color'] }};"
                    data-pa-modal="{{ $card['id'] }}" aria-haspopup="dialog"
                    aria-label="{{ $card['view'] }}" title="{{ $card['view'] }}">
                <span class="pa-stat-main">
                    <span class="pa-stat-label">{{ $card['label'] }}</span>
                    <span class="pa-stat-value">{{ $statActivities->count() }}</span>
                    <span class="pa-stat-hint">{{ $card['hint'] }}</span>
                </span>
                <span class="pa-stat-icon" aria-hidden="true"><i class="fas {{ $card['icon'] }}"></i></span>
            </button>
        @endforeach
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('purchasing-activities.index') }}"
          class="bg-white rounded-lg shadow-sm border border-gray-200 px-5 py-4 mb-6 no-print">
        <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-4 items-end">
            <div>
                <label for="from" class="block text-sm font-medium text-gray-700 mb-1">Request From</label>
                <input type="date" id="from" name="from" value="{{ request('from') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
            </div>
            <div>
                <label for="to" class="block text-sm font-medium text-gray-700 mb-1">Request To</label>
                <input type="date" id="to" name="to" value="{{ request('to') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
            </div>
            <div>
                <label for="document_type" class="block text-sm font-medium text-gray-700 mb-1">Document</label>
                <select id="document_type" name="document_type"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                    <option value="">All documents</option>
                    @foreach($documentTypes as $key => $label)
                        <option value="{{ $key }}" @selected(request('document_type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="procurement_type" class="block text-sm font-medium text-gray-700 mb-1">Procurement Type</label>
                <select id="procurement_type" name="procurement_type"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                    <option value="">All types</option>
                    @foreach($procurementTypes as $key => $label)
                        <option value="{{ $key }}" @selected(request('procurement_type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="material_group" class="block text-sm font-medium text-gray-700 mb-1">Material Group</label>
                <select id="material_group" name="material_group"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                    <option value="">All groups</option>
                    @foreach($materialGroups as $group)
                        <option value="{{ $group }}" @selected(request('material_group') === $group)>{{ $group }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                    <option value="">All statuses</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3 mt-4">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-[#b45309] text-white font-medium rounded-lg hover:bg-[#92400e] transition">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <a href="{{ route('purchasing-activities.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-rotate-left mr-2"></i>Reset
            </a>
        </div>
    </form>

    <!-- Table -->
    <div class="pa-table-card bg-white rounded-lg shadow-sm border border-gray-200" data-print-section="table">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 px-6 py-4 border-b border-gray-200">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Daily Activity Register</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $activities->total() }} record(s)
                    &middot; page {{ $activities->currentPage() }} of {{ $activities->lastPage() }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 no-print">
                <div class="pa-actions">
                    <button type="button" class="pa-action-btn" data-pa-print-options data-print-default="table" title="Print this table only">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
                <form method="GET" action="{{ route('purchasing-activities.index') }}" class="pa-perpage">
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                        @if(is_scalar($value))
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <label for="paPerPage">Rows</label>
                    <select name="per_page" id="paPerPage" onchange="this.form.submit()">
                        @foreach([5, 10, 25, 50] as $size)
                            <option value="{{ $size }}" @selected($activities->perPage() === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="pa-table-scroll overflow-x-auto">
            <table class="pa-main-table min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-4 py-3 font-medium pa-roll">No</th>
                        <th class="px-6 py-3 font-medium">Request Date</th>
                        <th class="px-6 py-3 font-medium">Document</th>
                        <th class="px-6 py-3 font-medium">Material Group</th>
                        <th class="px-6 py-3 font-medium">Unit</th>
                        <th class="px-6 py-3 font-medium">Procurement Type</th>
                        <th class="px-6 py-3 font-medium">Remark</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium no-print">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($activities as $activity)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-4 py-4 text-gray-400 pa-roll">{{ ($activities->firstItem() ?? 1) + $loop->index }}</td>
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap">{{ $activity->request_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($activity->document_type === \App\Models\PurchasingActivity::DOCUMENT_PROFORMA) bg-indigo-100 text-indigo-800
                                @else bg-sky-100 text-sky-800 @endif">
                                {{ $activity->getDocumentTypeLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $activity->material_group }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $activity->unit }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($activity->procurement_type === \App\Models\PurchasingActivity::PROCUREMENT_TENDER) bg-teal-100 text-teal-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ $activity->getProcurementTypeLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $activity->remarks ?: '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($activity->isComplete()) bg-green-100 text-green-800
                                @else bg-amber-100 text-amber-800 @endif">
                                @if($activity->isComplete())
                                    <i class="fas fa-check mr-1"></i>
                                @endif
                                {{ $activity->getStatusLabel() }}
                            </span>
                            @if($activity->isComplete())
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $activity->completer->name ?? 'Unknown' }}
                                    &middot; {{ $activity->completed_at?->format('M d, Y H:i') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 no-print">
                            @if(!$activity->isComplete() && auth()->user()->canCompletePurchasingActivities())
                            <form method="POST" action="{{ route('purchasing-activities.complete', $activity) }}"
                                  onsubmit="return confirm('Mark this purchasing activity as complete?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center px-3 py-1 text-xs font-medium bg-green-600 text-white rounded hover:bg-green-700">
                                    <i class="fas fa-check mr-1"></i>Complete
                                </button>
                            </form>
                            @else
                                <span class="text-xs text-gray-400">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-16 text-center">
                            <i class="fas fa-clipboard-list text-4xl text-gray-300"></i>
                            <p class="mt-4 text-gray-700 font-medium">No purchasing activities found</p>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request()->hasAny(['from', 'to', 'status', 'document_type', 'procurement_type', 'material_group']))
                                    Try adjusting or resetting the filters.
                                @else
                                    Record a proforma or quotation request to start the daily register.
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->total() > 0)
            @php
                $paCurrent = $activities->currentPage();
                $paLast = $activities->lastPage();
                $paPages = [];
                for ($paPage = 1; $paPage <= $paLast; $paPage++) {
                    if ($paPage === 1 || $paPage === $paLast || abs($paPage - $paCurrent) <= 1) {
                        $paPages[] = $paPage;
                    } elseif (end($paPages) !== '...') {
                        $paPages[] = '...';
                    }
                }
            @endphp
            <div class="pa-pagination no-print">
                <span>
                    Showing <strong>{{ $activities->firstItem() }}–{{ $activities->lastItem() }}</strong>
                    of <strong>{{ $activities->total() }}</strong>
                </span>
                <div class="pa-pages">
                    @if($paCurrent > 1)
                        <a class="pa-page-btn" href="{{ $activities->url($paCurrent - 1) }}" rel="prev">« Prev</a>
                    @else
                        <button type="button" class="pa-page-btn" disabled>« Prev</button>
                    @endif
                    @foreach($paPages as $paPage)
                        @if($paPage === '...')
                            <span class="pa-page-dots">...</span>
                        @else
                            <a class="pa-page-btn {{ $paPage === $paCurrent ? 'pa-active' : '' }}" href="{{ $activities->url($paPage) }}" @if($paPage === $paCurrent) aria-current="page" @endif>{{ $paPage }}</a>
                        @endif
                    @endforeach
                    @if($paCurrent < $paLast)
                        <a class="pa-page-btn" href="{{ $activities->url($paCurrent + 1) }}" rel="next">Next »</a>
                    @else
                        <button type="button" class="pa-page-btn" disabled>Next »</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@if(auth()->user()->canCreatePurchasingActivities())
<!-- New Purchasing Activity modal -->
<div id="activityModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeActivityModal()"></div>
    <div class="relative mx-auto mt-16 w-full max-w-lg px-4">
        <div class="bg-white rounded-xl shadow-xl border border-gray-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-clipboard-list text-[#b45309] mr-2"></i>New Purchasing Activity
                </h3>
                <button type="button" onclick="closeActivityModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('purchasing-activities.store') }}" onsubmit="return validateActivityForm();">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label for="pa_document_type" class="block text-sm font-medium text-gray-700 mb-1">Document <span class="text-red-500">*</span></label>
                        <select id="pa_document_type" name="document_type" required onchange="toggleNewDocumentType()"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                            <option value="">Select document type</option>
                            @foreach($documentTypes as $key => $label)
                                <option value="{{ $key }}" @selected(old('document_type') === $key)>{{ $label }}</option>
                            @endforeach
                            <option value="__new__" @selected(old('document_type') === '__new__')>+ Register new document type...</option>
                        </select>
                        <div id="newDocumentTypeWrap" class="mt-2 hidden">
                            <div class="flex gap-2">
                                <input type="text" id="newDocumentTypeName" maxlength="191"
                                       value="{{ old('new_document_type') }}"
                                       placeholder="Enter new document type name"
                                       class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                                <button type="button" id="newDocumentTypeBtn" onclick="registerDocumentType()"
                                        class="inline-flex items-center px-3 py-2 bg-[#b45309] text-white text-sm font-medium rounded-lg hover:bg-[#92400e] transition disabled:opacity-60">
                                    <i class="fas fa-plus mr-1"></i>Register
                                </button>
                            </div>
                        </div>
                        <p id="newDocumentTypeMsg" class="text-sm mt-1 hidden"></p>
                        @error('document_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="request_date" class="block text-sm font-medium text-gray-700 mb-1">Request Date <span class="text-red-500">*</span></label>
                            <input type="date" id="request_date" name="request_date"
                                   value="{{ old('request_date', now()->format('Y-m-d')) }}"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]"
                                   required>
                            @error('request_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="pa_unit" class="block text-sm font-medium text-gray-700 mb-1">Unit <span class="text-red-500">*</span></label>
                            <select id="pa_unit" name="unit" required onchange="toggleNewUnit()"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                                <option value="">Select unit</option>
                                @foreach($units as $unitName)
                                    <option value="{{ $unitName }}" @selected(old('unit') === $unitName)>{{ $unitName }}</option>
                                @endforeach
                                <option value="__new__" @selected(old('unit') === '__new__')>+ Register new unit...</option>
                            </select>
                            <div id="newUnitWrap" class="mt-2 hidden">
                                <div class="flex gap-2">
                                    <input type="text" id="newUnitName" maxlength="50"
                                           value="{{ old('new_unit') }}"
                                           placeholder="e.g. Pcs, Kg, Liter"
                                           class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                                    <button type="button" id="newUnitBtn" onclick="registerUnit()"
                                            class="inline-flex items-center px-3 py-2 bg-[#b45309] text-white text-sm font-medium rounded-lg hover:bg-[#92400e] transition disabled:opacity-60">
                                        <i class="fas fa-plus mr-1"></i>Register
                                    </button>
                                </div>
                            </div>
                            <p id="newUnitMsg" class="text-sm mt-1 hidden"></p>
                            @error('unit')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="pa_material_group" class="block text-sm font-medium text-gray-700 mb-1">Material Group <span class="text-red-500">*</span></label>
                        <select id="pa_material_group" name="material_group" required onchange="toggleNewMaterialGroup()"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                            <option value="">Select material group</option>
                            @foreach($materialGroups as $group)
                                <option value="{{ $group }}" @selected(old('material_group') === $group)>{{ $group }}</option>
                            @endforeach
                            <option value="__new__" @selected(old('material_group') === '__new__')>+ Register new material group...</option>
                        </select>
                        <div id="newMaterialGroupWrap" class="mt-2 hidden">
                            <div class="flex gap-2">
                                <input type="text" id="newMaterialGroupName" maxlength="191"
                                       value="{{ old('new_material_group') }}"
                                       placeholder="Enter new material group name"
                                       class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                                <button type="button" id="newMaterialGroupBtn" onclick="registerMaterialGroup()"
                                        class="inline-flex items-center px-3 py-2 bg-[#b45309] text-white text-sm font-medium rounded-lg hover:bg-[#92400e] transition disabled:opacity-60">
                                    <i class="fas fa-plus mr-1"></i>Register
                                </button>
                            </div>
                        </div>
                        <p id="newMaterialGroupMsg" class="text-sm mt-1 hidden"></p>
                        @error('material_group')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pa_procurement_type" class="block text-sm font-medium text-gray-700 mb-1">Procurement Type <span class="text-red-500">*</span></label>
                        <select id="pa_procurement_type" name="procurement_type" required onchange="toggleNewProcurementType()"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                            <option value="">Select procurement type</option>
                            @foreach($procurementTypes as $key => $label)
                                <option value="{{ $key }}" @selected(old('procurement_type') === $key)>{{ $label }}</option>
                            @endforeach
                            <option value="__new__" @selected(old('procurement_type') === '__new__')>+ Register new procurement type...</option>
                        </select>
                        <div id="newProcurementTypeWrap" class="mt-2 hidden">
                            <div class="flex gap-2">
                                <input type="text" id="newProcurementTypeName" maxlength="191"
                                       value="{{ old('new_procurement_type') }}"
                                       placeholder="Enter new procurement type name"
                                       class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]">
                                <button type="button" id="newProcurementTypeBtn" onclick="registerProcurementType()"
                                        class="inline-flex items-center px-3 py-2 bg-[#b45309] text-white text-sm font-medium rounded-lg hover:bg-[#92400e] transition disabled:opacity-60">
                                    <i class="fas fa-plus mr-1"></i>Register
                                </button>
                            </div>
                        </div>
                        <p id="newProcurementTypeMsg" class="text-sm mt-1 hidden"></p>
                        @error('procurement_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pa_remarks" class="block text-sm font-medium text-gray-700 mb-1">
                            Remark <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <textarea id="pa_remarks" name="remarks" rows="3" maxlength="1000"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#b45309]"
                                  placeholder="Add any note about this request...">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="text-xs text-gray-500">
                        New activities start as Pending and can be marked Complete by Finance or an administrator.
                    </p>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                    <button type="button" onclick="closeActivityModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-white transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-[#b45309] text-white font-medium rounded-lg hover:bg-[#92400e] transition">
                        Save Activity
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@foreach($statCards as $card)
    @php $modalActivities = $statGroups[$card['key']]; @endphp
    <div class="pa-modal-bg" id="{{ $card['id'] }}" role="dialog" aria-modal="true" aria-labelledby="{{ $card['id'] }}Title">
        <div class="pa-modal">
            <div class="pa-modal-head">
                <div>
                    <h3 id="{{ $card['id'] }}Title">{{ $card['label'] }}</h3>
                    <span>{{ $modalActivities->count() }} record(s)</span>
                </div>
                <button type="button" class="pa-modal-close" aria-label="Close" data-pa-close>×</button>
            </div>
            <div class="pa-modal-body">
                @if($modalActivities->isEmpty())
                    <div class="pa-empty">No purchasing activities in this group.</div>
                @else
                    <div class="pa-table-wrap">
                        <table class="pa-table">
                            <thead>
                                <tr>
                                    <th class="pa-roll">No</th>
                                    <th>Request Date</th>
                                    <th>Document</th>
                                    <th>Material Group</th>
                                    <th>Unit</th>
                                    <th>Procurement Type</th>
                                    <th>Remark</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modalActivities as $activity)
                                    <tr>
                                        <td class="pa-roll">{{ $loop->iteration }}</td>
                                        <td class="whitespace-nowrap">{{ $activity->request_date?->format('M d, Y') ?? '-' }}</td>
                                        <td>
                                            <span class="pa-badge {{ $activity->document_type === \App\Models\PurchasingActivity::DOCUMENT_PROFORMA ? 'indigo' : 'sky' }}">
                                                {{ $activity->getDocumentTypeLabel() }}
                                            </span>
                                        </td>
                                        <td>{{ $activity->material_group }}</td>
                                        <td>{{ $activity->unit }}</td>
                                        <td>
                                            <span class="pa-badge {{ $activity->procurement_type === \App\Models\PurchasingActivity::PROCUREMENT_TENDER ? 'teal' : 'blue' }}">
                                                {{ $activity->getProcurementTypeLabel() }}
                                            </span>
                                        </td>
                                        <td>{{ $activity->remarks ?: '—' }}</td>
                                        <td>
                                            @if($activity->isComplete())
                                                <span class="pa-badge green"><i class="fas fa-check"></i>Complete</span>
                                                <div class="pa-modal-sub">
                                                    {{ $activity->completer->name ?? 'Unknown' }}
                                                    &middot; {{ $activity->completed_at?->format('M d, Y H:i') }}
                                                </div>
                                            @else
                                                <span class="pa-badge amber">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="pa-modal-foot">
                <span class="pa-modal-range"></span>
                <div class="pa-modal-pages"></div>
            </div>
        </div>
    </div>
@endforeach

<div class="pa-modal-bg" id="paModalPrint" role="dialog" aria-modal="true" aria-labelledby="paPrintTitle">
    <div class="pa-modal pa-modal-sm">
        <div class="pa-modal-head">
            <div>
                <h3 id="paPrintTitle">Print Purchasing Activities</h3>
                <span>Choose which sections appear on the printed page</span>
            </div>
            <button type="button" class="pa-modal-close" aria-label="Close" data-pa-close>×</button>
        </div>
        <div class="pa-modal-body">
            <div class="pa-print-options">
                <label><input type="checkbox" class="pa-print-check" value="cards"> Summary cards</label>
                <label><input type="checkbox" class="pa-print-check" value="table" checked> Activity register table</label>
            </div>
            <p id="paPrintHint" class="pa-print-hint" style="display:none;">Select at least one section to print.</p>
        </div>
        <div class="pa-modal-foot" style="display:flex;">
            <span></span>
            <div class="pa-modal-pages">
                <button type="button" class="pa-action-btn" data-pa-close>Cancel</button>
                <button type="button" class="inline-flex items-center px-3 py-1.5 bg-[#b45309] text-white text-xs font-semibold rounded-lg hover:bg-[#92400e]" id="paPrintGo">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openActivityModal() {
        const modal = document.getElementById('activityModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        const select = document.getElementById('pa_document_type');
        if (select) select.focus();
    }

    function closeActivityModal() {
        const modal = document.getElementById('activityModal');
        if (modal) modal.classList.add('hidden');
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
            if (!document.querySelector('.pa-modal-bg.open')) {
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
            var table = modal.querySelector('.pa-modal-body .pa-table');
            if (!table) return;

            var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
            if (!rows.length) return;

            var foot = modal.querySelector('.pa-modal-foot');
            var rangeNode = foot && foot.querySelector('.pa-modal-range');
            var pagesNode = foot && foot.querySelector('.pa-modal-pages');
            if (!foot || !rangeNode || !pagesNode) return;

            var state = modal.paPager;
            if (!state) {
                state = modal.paPager = { rows: rows, page: 1, foot: foot, range: rangeNode, pages: pagesNode };
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
                    dots.className = 'pa-page-dots';
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
                var active = state.pages.querySelector('.pa-active');
                if (active) active.focus();
            }
        }

        function pagerButton(label, disabled, active, onClick) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'pa-page-btn' + (active ? ' pa-active' : '');
            button.textContent = label;
            button.disabled = disabled;
            button.addEventListener('click', onClick);
            return button;
        }

        function goToModalPage(state, page) {
            state.page = page;
            paintModalPage(state, true);
            var body = state.foot.parentNode.querySelector('.pa-modal-body');
            if (body) body.scrollTop = 0;
        }

        var printModal = document.getElementById('paModalPrint');
        var printChecks = Array.prototype.slice.call(document.querySelectorAll('.pa-print-check'));
        var printHint = document.getElementById('paPrintHint');

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

        Array.prototype.forEach.call(document.querySelectorAll('[data-pa-modal]'), function (button) {
            button.addEventListener('click', function () {
                var modal = document.getElementById(button.getAttribute('data-pa-modal'));
                initModalPager(modal);
                openModalEl(modal);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('.pa-modal-bg'), function (backdrop) {
            backdrop.addEventListener('click', function (event) {
                if (event.target === backdrop) closeModal(backdrop);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-pa-close]'), function (button) {
            button.addEventListener('click', function () {
                closeModal(button.closest('.pa-modal-bg'));
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-pa-print-options]'), function (button) {
            button.addEventListener('click', function () {
                openPrintOptions(button.getAttribute('data-print-default'));
            });
        });

        var printGo = document.getElementById('paPrintGo');
        if (printGo) {
            printGo.addEventListener('click', printSelectedSections);
        }

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            closeActivityModal();
            var open = document.querySelectorAll('.pa-modal-bg.open');
            if (open.length) {
                closeModal(open[open.length - 1]);
            }
        });
    })();

    function toggleNewMaterialGroup() {
        const select = document.getElementById('pa_material_group');
        const wrap = document.getElementById('newMaterialGroupWrap');
        if (!select || !wrap) return;

        if (select.value === '__new__') {
            wrap.classList.remove('hidden');
            document.getElementById('newMaterialGroupName')?.focus();
            return;
        }

        wrap.classList.add('hidden');
        setNewMaterialGroupMsg('', false);
    }

    function setNewMaterialGroupMsg(text, isError) {
        const msg = document.getElementById('newMaterialGroupMsg');
        if (!msg) return;

        msg.textContent = text;
        msg.classList.toggle('hidden', !text);
        msg.classList.remove('text-red-600', 'text-green-600');

        if (text) {
            msg.classList.add(isError ? 'text-red-600' : 'text-green-600');
        }
    }

    async function registerMaterialGroup() {
        const select = document.getElementById('pa_material_group');
        const input = document.getElementById('newMaterialGroupName');
        const button = document.getElementById('newMaterialGroupBtn');
        const wrap = document.getElementById('newMaterialGroupWrap');
        if (!select || !input || !button) return;

        const name = input.value.trim();
        if (!name) {
            setNewMaterialGroupMsg('Enter the material group name.', true);
            input.focus();
            return;
        }

        button.disabled = true;
        setNewMaterialGroupMsg('', false);

        try {
            const response = await fetch('{{ route('purchasing-activities.material-groups.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ name: name }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
                throw new Error(firstError || data.message || 'Unable to register the material group.');
            }

            let option = Array.from(select.options).find((item) => item.value === data.name);

            if (!option) {
                option = new Option(data.name, data.name);

                const nextOption = Array.from(select.options).find((item) =>
                    item.value !== '' &&
                    item.value !== '__new__' &&
                    item.value.localeCompare(data.name, undefined, { sensitivity: 'base' }) > 0
                );

                select.insertBefore(
                    option,
                    nextOption || select.querySelector('option[value="__new__"]')
                );
            }

            select.value = data.name;
            input.value = '';
            wrap.classList.add('hidden');
            setNewMaterialGroupMsg(data.message + ' Selected for this activity.', false);
        } catch (error) {
            setNewMaterialGroupMsg(error.message, true);
        } finally {
            button.disabled = false;
        }
    }

    function toggleNewDocumentType() {
        const select = document.getElementById('pa_document_type');
        const wrap = document.getElementById('newDocumentTypeWrap');
        if (!select || !wrap) return;

        if (select.value === '__new__') {
            wrap.classList.remove('hidden');
            document.getElementById('newDocumentTypeName')?.focus();
            return;
        }

        wrap.classList.add('hidden');
        setNewDocumentTypeMsg('', false);
    }

    function setNewDocumentTypeMsg(text, isError) {
        const msg = document.getElementById('newDocumentTypeMsg');
        if (!msg) return;

        msg.textContent = text;
        msg.classList.toggle('hidden', !text);
        msg.classList.remove('text-red-600', 'text-green-600');

        if (text) {
            msg.classList.add(isError ? 'text-red-600' : 'text-green-600');
        }
    }

    async function registerDocumentType() {
        const select = document.getElementById('pa_document_type');
        const input = document.getElementById('newDocumentTypeName');
        const button = document.getElementById('newDocumentTypeBtn');
        const wrap = document.getElementById('newDocumentTypeWrap');
        if (!select || !input || !button) return;

        const name = input.value.trim();
        if (!name) {
            setNewDocumentTypeMsg('Enter the document type name.', true);
            input.focus();
            return;
        }

        button.disabled = true;
        setNewDocumentTypeMsg('', false);

        try {
            const response = await fetch('{{ route('purchasing-activities.document-types.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ name: name }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
                throw new Error(firstError || data.message || 'Unable to register the document type.');
            }

            let option = Array.from(select.options).find((item) => item.value === data.name);

            if (!option) {
                option = new Option(data.name, data.name);

                const nextOption = Array.from(select.options).find((item) =>
                    item.value !== '' &&
                    item.value !== '__new__' &&
                    item.value.localeCompare(data.name, undefined, { sensitivity: 'base' }) > 0
                );

                select.insertBefore(
                    option,
                    nextOption || select.querySelector('option[value="__new__"]')
                );
            }

            select.value = data.name;
            input.value = '';
            wrap.classList.add('hidden');
            setNewDocumentTypeMsg(data.message + ' Selected for this activity.', false);
        } catch (error) {
            setNewDocumentTypeMsg(error.message, true);
        } finally {
            button.disabled = false;
        }
    }

    function toggleNewProcurementType() {
        const select = document.getElementById('pa_procurement_type');
        const wrap = document.getElementById('newProcurementTypeWrap');
        if (!select || !wrap) return;

        if (select.value === '__new__') {
            wrap.classList.remove('hidden');
            document.getElementById('newProcurementTypeName')?.focus();
            return;
        }

        wrap.classList.add('hidden');
        setNewProcurementTypeMsg('', false);
    }

    function setNewProcurementTypeMsg(text, isError) {
        const msg = document.getElementById('newProcurementTypeMsg');
        if (!msg) return;

        msg.textContent = text;
        msg.classList.toggle('hidden', !text);
        msg.classList.remove('text-red-600', 'text-green-600');

        if (text) {
            msg.classList.add(isError ? 'text-red-600' : 'text-green-600');
        }
    }

    async function registerProcurementType() {
        const select = document.getElementById('pa_procurement_type');
        const input = document.getElementById('newProcurementTypeName');
        const button = document.getElementById('newProcurementTypeBtn');
        const wrap = document.getElementById('newProcurementTypeWrap');
        if (!select || !input || !button) return;

        const name = input.value.trim();
        if (!name) {
            setNewProcurementTypeMsg('Enter the procurement type name.', true);
            input.focus();
            return;
        }

        button.disabled = true;
        setNewProcurementTypeMsg('', false);

        try {
            const response = await fetch('{{ route('purchasing-activities.procurement-types.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ name: name }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
                throw new Error(firstError || data.message || 'Unable to register the procurement type.');
            }

            let option = Array.from(select.options).find((item) => item.value === data.name);

            if (!option) {
                option = new Option(data.name, data.name);

                const nextOption = Array.from(select.options).find((item) =>
                    item.value !== '' &&
                    item.value !== '__new__' &&
                    item.value.localeCompare(data.name, undefined, { sensitivity: 'base' }) > 0
                );

                select.insertBefore(
                    option,
                    nextOption || select.querySelector('option[value="__new__"]')
                );
            }

            select.value = data.name;
            input.value = '';
            wrap.classList.add('hidden');
            setNewProcurementTypeMsg(data.message + ' Selected for this activity.', false);
        } catch (error) {
            setNewProcurementTypeMsg(error.message, true);
        } finally {
            button.disabled = false;
        }
    }

    function toggleNewUnit() {
        const select = document.getElementById('pa_unit');
        const wrap = document.getElementById('newUnitWrap');
        if (!select || !wrap) return;

        if (select.value === '__new__') {
            wrap.classList.remove('hidden');
            document.getElementById('newUnitName')?.focus();
            return;
        }

        wrap.classList.add('hidden');
        setNewUnitMsg('', false);
    }

    function setNewUnitMsg(text, isError) {
        const msg = document.getElementById('newUnitMsg');
        if (!msg) return;

        msg.textContent = text;
        msg.classList.toggle('hidden', !text);
        msg.classList.remove('text-red-600', 'text-green-600');

        if (text) {
            msg.classList.add(isError ? 'text-red-600' : 'text-green-600');
        }
    }

    async function registerUnit() {
        const select = document.getElementById('pa_unit');
        const input = document.getElementById('newUnitName');
        const button = document.getElementById('newUnitBtn');
        const wrap = document.getElementById('newUnitWrap');
        if (!select || !input || !button) return;

        const name = input.value.trim();
        if (!name) {
            setNewUnitMsg('Enter the unit name.', true);
            input.focus();
            return;
        }

        button.disabled = true;
        setNewUnitMsg('', false);

        try {
            const response = await fetch('{{ route('purchasing-activities.units.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ name: name }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
                throw new Error(firstError || data.message || 'Unable to register the unit.');
            }

            let option = Array.from(select.options).find((item) => item.value === data.name);

            if (!option) {
                option = new Option(data.name, data.name);

                const nextOption = Array.from(select.options).find((item) =>
                    item.value !== '' &&
                    item.value !== '__new__' &&
                    item.value.localeCompare(data.name, undefined, { sensitivity: 'base' }) > 0
                );

                select.insertBefore(
                    option,
                    nextOption || select.querySelector('option[value="__new__"]')
                );
            }

            select.value = data.name;
            input.value = '';
            wrap.classList.add('hidden');
            setNewUnitMsg(data.message + ' Selected for this activity.', false);
        } catch (error) {
            setNewUnitMsg(error.message, true);
        } finally {
            button.disabled = false;
        }
    }

    function validateActivityForm() {
        const documentSelect = document.getElementById('pa_document_type');

        if (documentSelect && documentSelect.value === '__new__') {
            document.getElementById('newDocumentTypeWrap')?.classList.remove('hidden');
            setNewDocumentTypeMsg('Register the new document type before saving.', true);
            document.getElementById('newDocumentTypeName')?.focus();
            return false;
        }

        const typeSelect = document.getElementById('pa_procurement_type');

        if (typeSelect && typeSelect.value === '__new__') {
            document.getElementById('newProcurementTypeWrap')?.classList.remove('hidden');
            setNewProcurementTypeMsg('Register the new procurement type before saving.', true);
            document.getElementById('newProcurementTypeName')?.focus();
            return false;
        }

        const groupSelect = document.getElementById('pa_material_group');

        if (groupSelect && groupSelect.value === '__new__') {
            document.getElementById('newMaterialGroupWrap')?.classList.remove('hidden');
            setNewMaterialGroupMsg('Register the new material group before saving.', true);
            document.getElementById('newMaterialGroupName')?.focus();
            return false;
        }

        const unitSelect = document.getElementById('pa_unit');

        if (unitSelect && unitSelect.value === '__new__') {
            document.getElementById('newUnitWrap')?.classList.remove('hidden');
            setNewUnitMsg('Register the new unit before saving.', true);
            document.getElementById('newUnitName')?.focus();
            return false;
        }

        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const documentSelect = document.getElementById('pa_document_type');

        if (documentSelect && documentSelect.value === '__new__') {
            toggleNewDocumentType();
        }

        const typeSelect = document.getElementById('pa_procurement_type');

        if (typeSelect && typeSelect.value === '__new__') {
            toggleNewProcurementType();
        }

        const groupSelect = document.getElementById('pa_material_group');

        if (groupSelect && groupSelect.value === '__new__') {
            toggleNewMaterialGroup();
        }

        const unitSelect = document.getElementById('pa_unit');

        if (unitSelect && unitSelect.value === '__new__') {
            toggleNewUnit();
        }
    });

    @if($errors->any())
    document.addEventListener('DOMContentLoaded', openActivityModal);
    @endif
</script>
@endsection
