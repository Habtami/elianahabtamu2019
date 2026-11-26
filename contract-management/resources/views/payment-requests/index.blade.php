@extends('layouts.app')

@section('title', 'Payment Request')

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
        border-left: 4px solid var(--card-color, #7c3aed);
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
        border-left-color: var(--card-color, #7c3aed);
    }
    .pr-stat:focus-visible { outline: 2px solid var(--card-color, #7c3aed); outline-offset: 2px; }
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
        font-size: 1.7rem;
        font-weight: 700;
        color: #0f172a;
        margin-top: 0.3rem;
        line-height: 1;
    }
    .pr-stat-hint {
        display: block;
        margin-top: 0.55rem;
        font-size: 0.68rem;
        font-weight: 600;
        color: #8aa2b8;
        opacity: 0;
        transition: opacity 0.15s ease, color 0.15s ease;
    }
    .pr-stat:hover .pr-stat-hint,
    .pr-stat:focus-visible .pr-stat-hint { opacity: 1; color: #6d28d9; }
    .pr-stat-icon {
        flex: none;
        padding-top: 0.1rem;
        font-size: 1.9rem;
        line-height: 1;
        color: var(--card-color, #7c3aed);
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
    .pr-page-btn.pr-active { background: #7c3aed; border-color: #7c3aed; color: #fff; }
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
        vertical-align: top;
    }
    .pr-table tbody tr:hover td { background: #f8fbff; }
    .pr-table th.pr-roll,
    .pr-table td.pr-roll {
        width: 3.2rem;
        color: #7a90a6;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .pr-empty { text-align: center; color: #8ca3b8; padding: 2rem 1rem; font-style: italic; }

    .pr-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.68rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .pr-badge.green { background: #dcfce7; color: #166534; }
    .pr-badge.amber { background: #fef3c7; color: #92400e; }
    .pr-badge.orange { background: #ffedd5; color: #9a3412; }
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
    .pr-print-options input { width: 16px; height: 16px; accent-color: #7c3aed; }
    .pr-print-hint { margin: 0.7rem 0 0; font-size: 0.78rem; font-weight: 600; color: #b91c1c; }
    .pr-action-btn-active { background: #eef2ff; border-color: #c7d2fe; color: #4338ca; }

    .pr-tip {
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
    .pr-tip.open { display: block; }
    .pr-tip h4 {
        margin: 0 0 0.5rem;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #1d4ed8;
    }
    .pr-tip ul { margin: 0; padding-left: 1.1rem; }
    .pr-tip li { margin-bottom: 0.3rem; line-height: 1.45; }
    .pr-tip li:last-child { margin-bottom: 0; }
    .pr-tip strong { color: #0f2a44; }

    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        .sidebar, .sidebar-toggle, .page-header, .page-footer, .no-print,
        .pr-modal-bg { display: none !important; }
        .print-hide-section { display: none !important; }
        body { background: #fff; }
        .main-content, .main-content.expanded { margin-left: 0 !important; }
        .pr-page { max-width: none !important; margin: 0 !important; padding: 0 !important; }
        .pr-stat { box-shadow: none; cursor: default; }
        .pr-table-card { box-shadow: none; border: 1px solid #c9d7e6; }
        .pr-table-scroll { overflow: visible !important; }
        .pr-main-table thead { display: table-header-group; }
        .pr-main-table th, .pr-main-table td { border: 1px solid #9aa9b8; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endsection

@section('content')
@php
    $statCards = [
        ['key' => 'total', 'label' => 'Total Requests', 'color' => '#7c3aed', 'icon' => 'fa-money-bill-wave', 'id' => 'prModalTotal', 'hint' => 'Click to view all requests →', 'view' => 'View all payment requests'],
        ['key' => 'pending', 'label' => 'Pending', 'color' => '#f59e0b', 'icon' => 'fa-hourglass-half', 'id' => 'prModalPending', 'hint' => 'Click to view the pending queue →', 'view' => 'View pending payment requests'],
        ['key' => 'complete', 'label' => 'Complete', 'color' => '#10b981', 'icon' => 'fa-circle-check', 'id' => 'prModalComplete', 'hint' => 'Click to view completed requests →', 'view' => 'View completed payment requests'],
    ];
    $currentSort = request('sort', 'created_at');
    $currentDir = strtolower(request('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
    $sortUrl = function (string $column) use ($currentSort, $currentDir) {
        $nextDir = $currentSort === $column && $currentDir === 'asc' ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $column, 'dir' => $nextDir, 'page' => 1]);
    };
    $sortIcon = function (string $column) use ($currentSort, $currentDir) {
        if ($currentSort !== $column) {
            return 'fa-sort text-gray-400';
        }
        return $currentDir === 'asc' ? 'fa-sort-up text-[#7c3aed]' : 'fa-sort-down text-[#7c3aed]';
    };
@endphp

<div class="pr-page container mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-money-bill-wave text-[#7c3aed] mr-3"></i>
                Payment Request
            </h1>
            <p class="text-gray-600 mt-2">Register payment requests and track the two-stage flow: GS approval first, Finance completion second.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 no-print">
            <button type="button" data-pr-print-options data-print-default="table"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <a href="{{ route('payment-requests.export.excel', request()->query()) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-excel mr-2"></i>Excel
            </a>
            <a href="{{ route('payment-requests.export.pdf', request()->query()) }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-file-pdf mr-2"></i>PDF
            </a>
            @if(auth()->user()->canCreatePaymentRequests())
            <button type="button" onclick="openPrModal()"
                    class="inline-flex items-center px-4 py-2 bg-[#7c3aed] text-white font-medium rounded-lg hover:bg-[#6d28d9] transition">
                <i class="fas fa-plus mr-2"></i>
                New Payment Request
            </button>
            @endif
        </div>
    </div>

    <!-- Summary cards -->
    <div class="pr-stats" data-print-section="cards">
        @foreach($statCards as $card)
            @php $statRequests = $statGroups[$card['key']]; @endphp
            <button type="button" class="pr-stat" style="--card-color: {{ $card['color'] }};"
                    data-pr-modal="{{ $card['id'] }}" aria-haspopup="dialog"
                    aria-label="{{ $card['view'] }}" title="{{ $card['view'] }}">
                <span class="pr-stat-main">
                    <span class="pr-stat-label">{{ $card['label'] }}</span>
                    <span class="pr-stat-value">{{ $statRequests->count() }}</span>
                    <span class="pr-stat-hint">{{ $card['hint'] }}</span>
                </span>
                <span class="pr-stat-icon" aria-hidden="true"><i class="fas {{ $card['icon'] }}"></i></span>
            </button>
        @endforeach
    </div>

    <!-- Table -->
    <div class="pr-table-card bg-white rounded-lg shadow-sm border border-gray-200" data-print-section="table">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 px-6 py-4 border-b border-gray-200">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Payment Requests</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    @if($search !== '')
                        {{ $paymentRequests->total() }} result(s) for &ldquo;{{ $search }}&rdquo;
                    @else
                        {{ $paymentRequests->total() }} request(s)
                    @endif
                    &middot; page {{ $paymentRequests->currentPage() }} of {{ $paymentRequests->lastPage() }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 no-print">
                <form method="GET" action="{{ route('payment-requests.index') }}" class="flex flex-wrap items-center gap-2">
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    @if(request('dir'))
                        <input type="hidden" name="dir" value="{{ request('dir') }}">
                    @endif
                    <input type="hidden" name="per_page" value="{{ $paymentRequests->perPage() }}">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="search" value="{{ $search }}" autocomplete="off"
                               placeholder="Search PR no., date, remark or approver..."
                               class="w-full sm:w-72 rounded-lg border border-gray-300 pl-9 pr-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#7c3aed]">
                    </div>
                    <button type="submit"
                            class="px-3 py-1.5 bg-[#7c3aed] text-white text-sm font-medium rounded-lg hover:bg-[#6d28d9] transition">
                        Search
                    </button>
                    <a href="{{ route('payment-requests.index') }}"
                       class="px-3 py-1.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition {{ $search !== '' ? '' : 'hidden' }}">
                        Clear
                    </a>
                </form>
                <form method="GET" action="{{ route('payment-requests.index') }}" class="pr-perpage">
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                        @if(is_scalar($value))
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <label for="prPerPage">Rows</label>
                    <select name="per_page" id="prPerPage" onchange="this.form.submit()">
                        @foreach([5, 10, 25, 50] as $size)
                            <option value="{{ $size }}" @selected($paymentRequests->perPage() === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </form>
                <div class="pr-actions">
                    <button type="button" class="pr-action-btn" data-pr-tip aria-expanded="false" aria-controls="prTipBox">
                        <i class="fas fa-info-circle"></i> TIP: info
                    </button>
                    <button type="button" class="pr-action-btn" data-pr-print-options data-print-default="table" title="Print this table only">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <div id="prTipBox" class="pr-tip no-print">
            <h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4>
            <ul>
                <li><strong>Request No.</strong> is generated as PR-#### from the record number — searching &ldquo;PR-14&rdquo; finds it directly.</li>
                <li><strong>GS (1st Approval)</strong> must be recorded first. Until then the Finance column stays <strong>Locked</strong>.</li>
                <li><strong>Finance (2nd Approval)</strong> completes the payment, and only after the GS approval exists.</li>
                <li><strong>Payment Status</strong> remains Pending until Finance completes the request.</li>
                <li>Search matches PR number, date, remark and approver names. <strong>Rows</strong> changes the page size, and the KPI cards open the full list regardless of the search.</li>
                <li>Excel, PDF and Print follow the current search, sort and page size.</li>
            </ul>
        </div>

        <div class="pr-table-scroll overflow-x-auto">
            <table class="pr-main-table min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-4 py-3 font-medium pr-roll">No</th>
                        <th class="px-6 py-3 font-medium">
                            <a href="{{ $sortUrl('reference') }}" class="inline-flex items-center gap-1 hover:text-[#7c3aed]">
                                Request No. <i class="fas {{ $sortIcon('reference') }} text-xs"></i>
                            </a>
                        </th>
                        <th class="px-6 py-3 font-medium">
                            <a href="{{ $sortUrl('payment_request_date') }}" class="inline-flex items-center gap-1 hover:text-[#7c3aed]">
                                Payment Request Date <i class="fas {{ $sortIcon('payment_request_date') }} text-xs"></i>
                            </a>
                        </th>
                        <th class="px-6 py-3 font-medium">GS (1st Approval)</th>
                        <th class="px-6 py-3 font-medium">Finance (2nd Approval)</th>
                        <th class="px-6 py-3 font-medium">
                            <a href="{{ $sortUrl('status') }}" class="inline-flex items-center gap-1 hover:text-[#7c3aed]">
                                Payment Status <i class="fas {{ $sortIcon('status') }} text-xs"></i>
                            </a>
                        </th>
                        <th class="px-6 py-3 font-medium">Remark</th>
                        <th class="px-6 py-3 font-medium no-print">View</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($paymentRequests as $paymentRequest)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-4 py-4 text-gray-400 pr-roll">{{ ($paymentRequests->firstItem() ?? 1) + $loop->index }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $paymentRequest->getReference() }}</td>
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap">{{ $paymentRequest->payment_request_date->format('M d, Y') }}</td>

                        <!-- GS approval -->
                        <td class="px-6 py-4">
                            @if($paymentRequest->isGsApproved())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Approved
                                </span>
                                <div class="text-gray-700 mt-1">{{ $paymentRequest->gsApprover->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $paymentRequest->gsApprover?->getRoleDisplayName() }}
                                    &middot; {{ $paymentRequest->gs_approved_at->format('M d, Y H:i') }}
                                </div>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    Pending
                                </span>
                                @if(auth()->user()->canApprovePaymentRequestsGs())
                                <form method="POST" action="{{ route('payment-requests.approve-gs', $paymentRequest) }}" class="mt-2 no-print"
                                      onsubmit="return confirm('Record the GS approval for this payment request?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 text-xs font-medium bg-[#7c3aed] text-white rounded hover:bg-[#6d28d9]">
                                        Approve
                                    </button>
                                </form>
                                @endif
                            @endif
                        </td>

                        <!-- Finance completion -->
                        <td class="px-6 py-4">
                            @if($paymentRequest->isFinanceCompleted())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Completed
                                </span>
                                <div class="text-gray-700 mt-1">{{ $paymentRequest->financeApprover->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $paymentRequest->financeApprover?->getRoleDisplayName() }}
                                    &middot; {{ $paymentRequest->finance_approved_at->format('M d, Y H:i') }}
                                </div>
                            @elseif(!$paymentRequest->isGsApproved())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700">
                                    Locked
                                </span>
                                <div class="text-xs font-semibold text-orange-600 mt-1">Awaiting GS approval</div>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    Pending
                                </span>
                                @if(auth()->user()->canCompletePaymentRequestsFinance())
                                <form method="POST" action="{{ route('payment-requests.complete', $paymentRequest) }}" class="mt-2 no-print"
                                      onsubmit="return confirm('Complete this payment as Finance?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 text-xs font-medium bg-green-600 text-white rounded hover:bg-green-700">
                                        Complete
                                    </button>
                                </form>
                                @endif
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($paymentRequest->isFinanceCompleted()) bg-green-100 text-green-800
                                @else bg-amber-100 text-amber-800 @endif">
                                {{ $paymentRequest->getStatusLabel() }}
                            </span>
                        </td>

                        <td class="px-6 py-4 max-w-[220px]">
                            @if($paymentRequest->remarks)
                                <span class="text-gray-700 break-words" title="{{ $paymentRequest->remarks }}">{{ $paymentRequest->remarks }}</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 no-print">
                            <a href="{{ route('payment-requests.show', $paymentRequest) }}"
                               class="inline-flex items-center px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition"
                               title="View payment request">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <i class="fas fa-money-check text-4xl text-gray-300"></i>
                            @if($search !== '')
                                <p class="mt-4 text-gray-700 font-medium">No payment requests match &ldquo;{{ $search }}&rdquo;</p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Try a different PR number, date, remark or approver name, or
                                    <a href="{{ route('payment-requests.index') }}" class="text-[#7c3aed] hover:underline">clear the search</a>.
                                </p>
                            @else
                                <p class="mt-4 text-gray-700 font-medium">No payment requests yet</p>
                                <p class="mt-1 text-sm text-gray-500">Create a payment request to start the GS and Finance flow.</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paymentRequests->total() > 0)
            @php
                $prCurrent = $paymentRequests->currentPage();
                $prLast = $paymentRequests->lastPage();
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
                    Showing <strong>{{ $paymentRequests->firstItem() }}–{{ $paymentRequests->lastItem() }}</strong>
                    of <strong>{{ $paymentRequests->total() }}</strong>
                </span>
                <div class="pr-pages">
                    @if($prCurrent > 1)
                        <a class="pr-page-btn" href="{{ $paymentRequests->url($prCurrent - 1) }}" rel="prev">« Prev</a>
                    @else
                        <button type="button" class="pr-page-btn" disabled>« Prev</button>
                    @endif
                    @foreach($prPages as $prPage)
                        @if($prPage === '...')
                            <span class="pr-page-dots">...</span>
                        @else
                            <a class="pr-page-btn {{ $prPage === $prCurrent ? 'pr-active' : '' }}" href="{{ $paymentRequests->url($prPage) }}" @if($prPage === $prCurrent) aria-current="page" @endif>{{ $prPage }}</a>
                        @endif
                    @endforeach
                    @if($prCurrent < $prLast)
                        <a class="pr-page-btn" href="{{ $paymentRequests->url($prCurrent + 1) }}" rel="next">Next »</a>
                    @else
                        <button type="button" class="pr-page-btn" disabled>Next »</button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <p class="text-xs text-gray-500 mt-4">
        <i class="fas fa-info-circle mr-1"></i>
        Finance can only complete a payment after the GS approval has been recorded. The payment status stays Pending until then.
    </p>
</div>

@if(auth()->user()->canCreatePaymentRequests())
<!-- New Payment Request modal -->
<div id="prModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closePrModal()"></div>
    <div class="relative mx-auto mt-10 mb-10 w-full max-w-2xl px-4 max-h-[calc(100vh-5rem)] overflow-y-auto">
        <div class="bg-white rounded-xl shadow-xl border border-gray-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-money-bill-wave text-[#7c3aed] mr-2"></i>New Payment Request
                </h3>
                <button type="button" onclick="closePrModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('payment-requests.store') }}" onsubmit="return validatePrForm();">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label for="payment_request_date" class="block text-sm font-medium text-gray-700 mb-1">Payment Request Date <span class="text-red-500">*</span></label>
                        <input type="date" id="payment_request_date" name="payment_request_date"
                               value="{{ old('payment_request_date', now()->format('Y-m-d')) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#7c3aed]"
                               required>
                        @error('payment_request_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="document_no" class="block text-sm font-medium text-gray-700 mb-1">
                                Document No <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="document_no" name="document_no" maxlength="255" required
                                   value="{{ old('document_no') }}"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#7c3aed]"
                                   placeholder="e.g. GRN-00123">
                            @error('document_no')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="payment_type" class="block text-sm font-medium text-gray-700 mb-1">
                                Payment Type <span class="text-red-500">*</span>
                            </label>
                            <select id="payment_type" name="payment_type" required
                                    onchange="toggleNewPaymentType()"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#7c3aed]">
                                <option value="">Select Payment Type</option>
                                @foreach($paymentTypes as $type)
                                    <option value="{{ $type }}" @selected(old('payment_type') === $type)>{{ $type }}</option>
                                @endforeach
                                <option value="__new__" @selected(old('payment_type') === '__new__')>+ Register new payment type...</option>
                            </select>
                            <div id="newPaymentTypeWrap" class="mt-2 {{ old('payment_type') === '__new__' ? '' : 'hidden' }}">
                                <div class="flex gap-2">
                                    <input type="text" id="newPaymentTypeName" maxlength="191"
                                           value="{{ old('new_payment_type') }}"
                                           placeholder="Enter new payment type name"
                                           class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#7c3aed]">
                                    <button type="button" id="newPaymentTypeBtn" onclick="registerPaymentType()"
                                            class="inline-flex items-center px-3 py-2 bg-[#7c3aed] text-white text-sm font-medium rounded-lg hover:bg-[#6d28d9] transition disabled:opacity-60">
                                        <i class="fas fa-plus mr-1"></i>Register
                                    </button>
                                </div>
                            </div>
                            <p id="newPaymentTypeMsg" class="text-sm mt-1 hidden"></p>
                            @error('payment_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                                Amount Price <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="amount" name="amount" min="0" step="0.01" required
                                   value="{{ old('amount') }}"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#7c3aed]"
                                   placeholder="0.00">
                            @error('amount')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                                Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="quantity" name="quantity" min="0" step="0.01" required
                                   value="{{ old('quantity') }}"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#7c3aed]"
                                   placeholder="0">
                            @error('quantity')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="material_group" class="block text-sm font-medium text-gray-700 mb-1">
                                Material Group <span class="text-red-500">*</span>
                            </label>
                            <select id="material_group" name="material_group" required
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#7c3aed]">
                                <option value="">Select Material Group</option>
                                @foreach($materialGroups as $group)
                                    <option value="{{ $group }}" @selected(old('material_group') === $group)>{{ $group }}</option>
                                @endforeach
                            </select>
                            @error('material_group')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="procurement_type" class="block text-sm font-medium text-gray-700 mb-1">
                                Procurement Type <span class="text-red-500">*</span>
                            </label>
                            <select id="procurement_type" name="procurement_type" required
                                    onchange="toggleNewProcurementType()"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#7c3aed]">
                                <option value="">Select Procurement Type</option>
                                @foreach($procurementTypes as $type)
                                    <option value="{{ $type }}" @selected(old('procurement_type') === $type)>{{ $type }}</option>
                                @endforeach
                                <option value="__new__" @selected(old('procurement_type') === '__new__')>+ Register new procurement type...</option>
                            </select>
                            <div id="newProcurementTypeWrap" class="mt-2 {{ old('procurement_type') === '__new__' ? '' : 'hidden' }}">
                                <div class="flex gap-2">
                                    <input type="text" id="newProcurementTypeName" maxlength="191"
                                           value="{{ old('new_procurement_type') }}"
                                           placeholder="Enter new procurement type name"
                                           class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#7c3aed]">
                                    <button type="button" id="newProcurementTypeBtn" onclick="registerProcurementType()"
                                            class="inline-flex items-center px-3 py-2 bg-[#7c3aed] text-white text-sm font-medium rounded-lg hover:bg-[#6d28d9] transition disabled:opacity-60">
                                        <i class="fas fa-plus mr-1"></i>Register
                                    </button>
                                </div>
                            </div>
                            <p id="newProcurementTypeMsg" class="text-sm mt-1 hidden"></p>
                            @error('procurement_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="pr_remarks" class="block text-sm font-medium text-gray-700 mb-1">
                            Remark <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <textarea id="pr_remarks" name="remarks" rows="3" maxlength="1000"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#7c3aed]"
                                  placeholder="e.g. Additional notes about this payment request">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="text-xs text-gray-500">
                        After saving, the GS records the 1st approval, then Finance completes the payment.
                    </p>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                    <button type="button" onclick="closePrModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-white transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-[#7c3aed] text-white font-medium rounded-lg hover:bg-[#6d28d9] transition">
                        Save Payment Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@foreach($statCards as $card)
    @php $modalRequests = $statGroups[$card['key']]; @endphp
    <div class="pr-modal-bg" id="{{ $card['id'] }}" role="dialog" aria-modal="true" aria-labelledby="{{ $card['id'] }}Title">
        <div class="pr-modal">
            <div class="pr-modal-head">
                <div>
                    <h3 id="{{ $card['id'] }}Title">{{ $card['label'] }}</h3>
                    <span>{{ $modalRequests->count() }} record(s)</span>
                </div>
                <button type="button" class="pr-modal-close" aria-label="Close" data-pr-close>×</button>
            </div>
            <div class="pr-modal-body">
                @if($modalRequests->isEmpty())
                    <div class="pr-empty">No payment requests in this group.</div>
                @else
                    <div class="pr-table-wrap">
                        <table class="pr-table">
                            <thead>
                                <tr>
                                    <th class="pr-roll">No</th>
                                    <th>Request No.</th>
                                    <th>Payment Request Date</th>
                                    <th>GS (1st Approval)</th>
                                    <th>Finance (2nd Approval)</th>
                                    <th>Payment Status</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modalRequests as $paymentRequest)
                                    <tr>
                                        <td class="pr-roll">{{ $loop->iteration }}</td>
                                        <td class="whitespace-nowrap font-semibold">{{ $paymentRequest->getReference() }}</td>
                                        <td class="whitespace-nowrap">{{ $paymentRequest->payment_request_date?->format('M d, Y') ?? '-' }}</td>
                                        <td>
                                            @if($paymentRequest->isGsApproved())
                                                <span class="pr-badge green"><i class="fas fa-check"></i>Approved</span>
                                                <div class="pr-modal-sub">
                                                    {{ $paymentRequest->gsApprover->name ?? 'Unknown' }}
                                                    &middot; {{ $paymentRequest->gs_approved_at?->format('M d, Y H:i') }}
                                                </div>
                                            @else
                                                <span class="pr-badge amber">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($paymentRequest->isFinanceCompleted())
                                                <span class="pr-badge green"><i class="fas fa-check"></i>Completed</span>
                                                <div class="pr-modal-sub">
                                                    {{ $paymentRequest->financeApprover->name ?? 'Unknown' }}
                                                    &middot; {{ $paymentRequest->finance_approved_at?->format('M d, Y H:i') }}
                                                </div>
                                            @elseif(!$paymentRequest->isGsApproved())
                                                <span class="pr-badge orange">Locked</span>
                                                <div class="pr-modal-sub">Awaiting GS approval</div>
                                            @else
                                                <span class="pr-badge amber">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="pr-badge {{ $paymentRequest->isFinanceCompleted() ? 'green' : 'amber' }}">
                                                {{ $paymentRequest->getStatusLabel() }}
                                            </span>
                                        </td>
                                        <td>{{ $paymentRequest->remarks ?: '—' }}</td>
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
                <h3 id="prPrintTitle">Print Payment Requests</h3>
                <span>Choose which sections appear on the printed page</span>
            </div>
            <button type="button" class="pr-modal-close" aria-label="Close" data-pr-close>×</button>
        </div>
        <div class="pr-modal-body">
            <div class="pr-print-options">
                <label><input type="checkbox" class="pr-print-check" value="cards"> Summary cards</label>
                <label><input type="checkbox" class="pr-print-check" value="table" checked> Payment requests table</label>
            </div>
            <p id="prPrintHint" class="pr-print-hint" style="display:none;">Select at least one section to print.</p>
        </div>
        <div class="pr-modal-foot" style="display:flex;">
            <span></span>
            <div class="pr-modal-pages">
                <button type="button" class="pr-action-btn" data-pr-close>Cancel</button>
                <button type="button" class="inline-flex items-center px-3 py-1.5 bg-[#7c3aed] text-white text-xs font-semibold rounded-lg hover:bg-[#6d28d9]" id="prPrintGo">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openPrModal() {
        const modal = document.getElementById('prModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        const input = document.getElementById('payment_request_date');
        if (input) input.focus();
    }

    function closePrModal() {
        const modal = document.getElementById('prModal');
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

        var tipButton = document.querySelector('[data-pr-tip]');
        var tipBox = document.getElementById('prTipBox');
        if (tipButton && tipBox) {
            tipButton.addEventListener('click', function () {
                var open = tipBox.classList.toggle('open');
                tipButton.setAttribute('aria-expanded', open ? 'true' : 'false');
                tipButton.classList.toggle('pr-action-btn-active', open);
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            closePrModal();
            var open = document.querySelectorAll('.pr-modal-bg.open');
            if (open.length) {
                closeModal(open[open.length - 1]);
            }
        });
    })();

    function toggleNewProcurementType() {
        const select = document.getElementById('procurement_type');
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
        const select = document.getElementById('procurement_type');
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
            const response = await fetch('{{ route('payment-requests.procurement-types.store') }}', {
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
            setNewProcurementTypeMsg(data.message + ' Selected for this request.', false);
        } catch (error) {
            setNewProcurementTypeMsg(error.message, true);
        } finally {
            button.disabled = false;
        }
    }

    function toggleNewPaymentType() {
        const select = document.getElementById('payment_type');
        const wrap = document.getElementById('newPaymentTypeWrap');
        if (!select || !wrap) return;

        if (select.value === '__new__') {
            wrap.classList.remove('hidden');
            document.getElementById('newPaymentTypeName')?.focus();
            return;
        }

        wrap.classList.add('hidden');
        setNewPaymentTypeMsg('', false);
    }

    function setNewPaymentTypeMsg(text, isError) {
        const msg = document.getElementById('newPaymentTypeMsg');
        if (!msg) return;

        msg.textContent = text;
        msg.classList.toggle('hidden', !text);
        msg.classList.remove('text-red-600', 'text-green-600');

        if (text) {
            msg.classList.add(isError ? 'text-red-600' : 'text-green-600');
        }
    }

    async function registerPaymentType() {
        const select = document.getElementById('payment_type');
        const input = document.getElementById('newPaymentTypeName');
        const button = document.getElementById('newPaymentTypeBtn');
        const wrap = document.getElementById('newPaymentTypeWrap');
        if (!select || !input || !button) return;

        const name = input.value.trim();
        if (!name) {
            setNewPaymentTypeMsg('Enter the payment type name.', true);
            input.focus();
            return;
        }

        button.disabled = true;
        setNewPaymentTypeMsg('', false);

        try {
            const response = await fetch('{{ route('payment-requests.payment-types.store') }}', {
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
                throw new Error(firstError || data.message || 'Unable to register the payment type.');
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
            setNewPaymentTypeMsg(data.message + ' Selected for this request.', false);
        } catch (error) {
            setNewPaymentTypeMsg(error.message, true);
        } finally {
            button.disabled = false;
        }
    }

    function validatePrForm() {
        const paymentSelect = document.getElementById('payment_type');

        if (paymentSelect && paymentSelect.value === '__new__') {
            document.getElementById('newPaymentTypeWrap')?.classList.remove('hidden');
            setNewPaymentTypeMsg('Register the new payment type before saving.', true);
            document.getElementById('newPaymentTypeName')?.focus();
            return false;
        }

        const typeSelect = document.getElementById('procurement_type');

        if (typeSelect && typeSelect.value === '__new__') {
            document.getElementById('newProcurementTypeWrap')?.classList.remove('hidden');
            setNewProcurementTypeMsg('Register the new procurement type before saving.', true);
            document.getElementById('newProcurementTypeName')?.focus();
            return false;
        }

        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const paymentSelect = document.getElementById('payment_type');

        if (paymentSelect && paymentSelect.value === '__new__') {
            toggleNewPaymentType();
        }

        const typeSelect = document.getElementById('procurement_type');

        if (typeSelect && typeSelect.value === '__new__') {
            toggleNewProcurementType();
        }
    });

    @if($errors->any())
    document.addEventListener('DOMContentLoaded', openPrModal);
    @endif
</script>
@endsection
