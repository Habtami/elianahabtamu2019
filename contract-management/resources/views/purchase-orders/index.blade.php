@extends('layouts.app')

@section('title', 'Purchase Order Processing')

@section('styles')
<style>
    .po-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .po-stat {
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
    .po-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
        border-left-color: var(--card-color, #1c75bc);
    }
    .po-stat:focus-visible { outline: 2px solid var(--card-color, #1c75bc); outline-offset: 2px; }
    .po-stat-main { min-width: 0; }
    .po-stat-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
    }
    .po-stat-value {
        display: block;
        font-size: 1.7rem;
        font-weight: 700;
        color: #0f172a;
        margin-top: 0.3rem;
        line-height: 1;
    }
    .po-stat-hint {
        display: block;
        margin-top: 0.55rem;
        font-size: 0.68rem;
        font-weight: 600;
        color: #8aa2b8;
        opacity: 0;
        transition: opacity 0.15s ease, color 0.15s ease;
    }
    .po-stat:hover .po-stat-hint,
    .po-stat:focus-visible .po-stat-hint { opacity: 1; color: #1a6fc4; }
    .po-stat-icon {
        flex: none;
        padding-top: 0.1rem;
        font-size: 1.9rem;
        line-height: 1;
        color: var(--card-color, #1c75bc);
        opacity: 0.22;
    }

    .po-action-btn {
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
    .po-action-btn:hover { background: #f1f5f9; }
    .po-actions { display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap; }

    .po-perpage { display: flex; align-items: center; gap: 0.35rem; }
    .po-perpage label { font-size: 0.72rem; font-weight: 600; color: #6b8299; margin: 0; }
    .po-perpage select {
        padding: 0.35rem 0.5rem;
        border: 1px solid #cfdcea;
        border-radius: 7px;
        font-size: 0.75rem;
        background: #fff;
        color: #1e293b;
    }

    .po-pagination {
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
    .po-pagination strong { color: #1e293b; font-weight: 600; }
    .po-pages { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; margin-left: auto; }
    .po-page-btn {
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
    .po-page-btn:hover:not(:disabled) { background: #f1f5f9; }
    .po-page-btn.po-active { background: #1d4ed8; border-color: #1d4ed8; color: #fff; }
    .po-page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .po-page-dots { padding: 0.35rem 0.3rem; color: #6b8299; }

    .po-modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(10, 25, 45, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1700;
        padding: 1rem;
    }
    .po-modal-bg.open { display: flex; }
    .po-modal {
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
    .po-modal.po-modal-sm { max-width: 480px; }
    .po-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.3rem;
        border-bottom: 1px solid #edf2f8;
    }
    .po-modal-head h3 { margin: 0; font-size: 1.05rem; color: #0f2a44; }
    .po-modal-head span { font-size: 0.75rem; color: #6b8299; }
    .po-modal-close {
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
    .po-modal-close:hover { background: #dbe6f1; }
    .po-modal-body { padding: 1rem 1.3rem 1.4rem; overflow-y: auto; }
    .po-modal-foot {
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
    .po-modal-foot strong { color: #1e293b; font-weight: 600; }
    .po-modal-pages { display: flex; gap: 0.25rem; align-items: center; flex-wrap: wrap; }

    .po-table-wrap { overflow-x: auto; }
    .po-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    .po-table th {
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
    .po-table td {
        padding: 0.7rem 0.6rem;
        border-bottom: 1px solid #edf2f8;
        color: #1e293b;
        vertical-align: top;
    }
    .po-table tbody tr:hover td { background: #f8fbff; }
    .po-table th.po-roll,
    .po-table td.po-roll {
        width: 3.2rem;
        color: #7a90a6;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .po-empty { text-align: center; color: #8ca3b8; padding: 2rem 1rem; font-style: italic; }

    .po-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.68rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .po-badge.green { background: #dcfce7; color: #166534; }
    .po-badge.amber { background: #fef3c7; color: #92400e; }
    .po-badge.blue { background: #dbeafe; color: #1d4ed8; }
    .po-badge.orange { background: #ffedd5; color: #c2410c; font-weight: 700; }
    .po-badge.gray { background: #f1f5f9; color: #475569; }
    .po-modal-person { color: #33506e; margin-top: 0.25rem; }
    .po-modal-sub { font-size: 0.72rem; color: #7a90a6; }

    .po-print-options { display: flex; flex-direction: column; gap: 0.55rem; }
    .po-print-options label {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        font-size: 0.88rem;
        color: #1e293b;
        cursor: pointer;
    }
    .po-print-options input { width: 16px; height: 16px; accent-color: #1a6fc4; }
    .po-print-hint { margin: 0.7rem 0 0; font-size: 0.78rem; font-weight: 600; color: #b91c1c; }

    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        .sidebar, .sidebar-toggle, .page-header, .page-footer, .no-print,
        .po-modal-bg { display: none !important; }
        .print-hide-section { display: none !important; }
        body { background: #fff; }
        .main-content, .main-content.expanded { margin-left: 0 !important; }
        .po-page { max-width: none !important; margin: 0 !important; padding: 0 !important; }
        .po-stat { box-shadow: none; cursor: default; }
        .po-table-card { box-shadow: none; border: 1px solid #c9d7e6; }
        .po-table-scroll { overflow: visible !important; }
        .po-main-table thead { display: table-header-group; }
        .po-main-table th, .po-main-table td { border: 1px solid #9aa9b8; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endsection

@section('content')
@php
    $statCards = [
        ['key' => 'total', 'label' => 'Total POs', 'color' => '#1C75BC', 'icon' => 'fa-file-invoice', 'id' => 'poModalTotal', 'hint' => 'Click to view all purchase orders →', 'view' => 'View all purchase orders'],
        ['key' => 'pending_first', 'label' => 'Awaiting 1st Approval', 'color' => '#f59e0b', 'icon' => 'fa-hourglass-half', 'id' => 'poModalPendingFirst', 'hint' => 'Click to view the queue →', 'view' => 'View purchase orders awaiting first approval'],
        ['key' => 'pending_second', 'label' => 'Awaiting 2nd Approval', 'color' => '#3b82f6', 'icon' => 'fa-user-shield', 'id' => 'poModalPendingSecond', 'hint' => 'Click to view the queue →', 'view' => 'View purchase orders awaiting second approval'],
        ['key' => 'approved', 'label' => 'Fully Approved', 'color' => '#10b981', 'icon' => 'fa-circle-check', 'id' => 'poModalApproved', 'hint' => 'Click to view the list →', 'view' => 'View fully approved purchase orders'],
    ];
@endphp

<div class="po-page container mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-shopping-cart text-[#1C75BC] mr-3"></i>
                Purchase Order Processing
            </h1>
            <p class="text-gray-600 mt-2">Register purchase orders and track approvals: above ETB 400,000 needs GS Manager and DDG; ETB 400,000 or less needs GS Manager only.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 no-print">
            <a href="{{ route('purchase-orders.export.excel', request()->query()) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-excel mr-2"></i>Excel
            </a>
            <a href="{{ route('purchase-orders.export.pdf', request()->query()) }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-file-pdf mr-2"></i>PDF
            </a>
            <button type="button" data-po-print-options data-print-default="table"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            @if(auth()->user()->canCreatePurchaseOrders())
            <button type="button" onclick="openPoModal()"
                    class="inline-flex items-center px-4 py-2 bg-[#1C75BC] text-white font-medium rounded-lg hover:bg-[#1664a0] transition">
                <i class="fas fa-plus mr-2"></i>
                New Purchase Order
            </button>
            @endif
        </div>
    </div>

    <!-- Summary cards -->
    <div class="po-stats" data-print-section="cards">
        @foreach($statCards as $card)
            @php $statOrders = $statGroups[$card['key']]; @endphp
            <button type="button" class="po-stat" style="--card-color: {{ $card['color'] }};"
                    data-po-modal="{{ $card['id'] }}" aria-haspopup="dialog"
                    aria-label="{{ $card['view'] }}" title="{{ $card['view'] }}">
                <span class="po-stat-main">
                    <span class="po-stat-label">{{ $card['label'] }}</span>
                    <span class="po-stat-value">{{ $statOrders->count() }}</span>
                    <span class="po-stat-hint">{{ $card['hint'] }}</span>
                </span>
                <span class="po-stat-icon" aria-hidden="true"><i class="fas {{ $card['icon'] }}"></i></span>
            </button>
        @endforeach
    </div>

    <!-- Table -->
    <div class="po-table-card bg-white rounded-lg shadow-sm border border-gray-200" data-print-section="table">
        @php
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
                return $currentDir === 'asc' ? 'fa-sort-up text-[#1C75BC]' : 'fa-sort-down text-[#1C75BC]';
            };
        @endphp

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 px-6 py-4 border-b border-gray-200">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Purchase Orders</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    @if($search !== '')
                        {{ $purchaseOrders->total() }} result(s) for &ldquo;{{ $search }}&rdquo;
                    @else
                        {{ $purchaseOrders->total() }} order(s)
                    @endif
                    &middot; page {{ $purchaseOrders->currentPage() }} of {{ $purchaseOrders->lastPage() }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 no-print">
                <div class="po-actions">
                    <a href="{{ route('purchase-orders.export.excel', request()->query()) }}" class="po-action-btn" title="Export this list to Excel">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                    <a href="{{ route('purchase-orders.export.pdf', request()->query()) }}" class="po-action-btn" title="Export this list to PDF">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <button type="button" class="po-action-btn" data-po-print-options data-print-default="table" title="Print this table only">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
                <form method="GET" action="{{ route('purchase-orders.index') }}" class="po-perpage">
                    @if($search !== '')
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    @if(request('dir'))
                        <input type="hidden" name="dir" value="{{ request('dir') }}">
                    @endif
                    <label for="poPerPage">Rows</label>
                    <select name="per_page" id="poPerPage" onchange="this.form.submit()">
                        @foreach([5, 10, 25, 50] as $size)
                            <option value="{{ $size }}" @selected($purchaseOrders->perPage() === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </form>
                <form method="GET" action="{{ route('purchase-orders.index') }}" class="flex flex-wrap items-center gap-2">
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    @if(request('dir'))
                        <input type="hidden" name="dir" value="{{ request('dir') }}">
                    @endif
                    @if(request('per_page'))
                        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                    @endif
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search PO number or approver..."
                               class="w-full sm:w-64 rounded-lg border border-gray-300 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1C75BC]">
                    </div>
                    <button type="submit"
                            class="px-3 py-2 bg-[#1C75BC] text-white text-sm font-medium rounded-lg hover:bg-[#1664a0] transition">
                        Search
                    </button>
                    @if($search !== '')
                    <a href="{{ route('purchase-orders.index') }}"
                       class="px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
                        Clear
                    </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="po-table-scroll overflow-x-auto">
            <table class="po-main-table min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-4 py-3 font-medium po-roll">No</th>
                        <th class="px-6 py-3 font-medium">
                            <a href="{{ $sortUrl('po_number') }}" class="inline-flex items-center gap-1 hover:text-[#1C75BC]">
                                PO Number <i class="fas {{ $sortIcon('po_number') }} text-xs"></i>
                            </a>
                        </th>
                        <th class="px-6 py-3 font-medium">
                            <a href="{{ $sortUrl('amount') }}" class="inline-flex items-center gap-1 hover:text-[#1C75BC]">
                                Amount (ETB) <i class="fas {{ $sortIcon('amount') }} text-xs"></i>
                            </a>
                        </th>
                        <th class="px-6 py-3 font-medium">
                            <a href="{{ $sortUrl('approval_date') }}" class="inline-flex items-center gap-1 hover:text-[#1C75BC]">
                                Approval Date <i class="fas {{ $sortIcon('approval_date') }} text-xs"></i>
                            </a>
                        </th>
                        <th class="px-6 py-3 font-medium">1st Approval (GS Manager)</th>
                        <th class="px-6 py-3 font-medium">2nd Approval (DDG)</th>
                        <th class="px-6 py-3 font-medium">
                            <a href="{{ $sortUrl('status') }}" class="inline-flex items-center gap-1 hover:text-[#1C75BC]">
                                Status <i class="fas {{ $sortIcon('status') }} text-xs"></i>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($purchaseOrders as $order)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-4 py-4 text-gray-400" style="font-variant-numeric: tabular-nums;">
                            {{ ($purchaseOrders->firstItem() ?? 1) + $loop->index }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $order->po_number }}</td>
                        <td class="px-6 py-4 text-gray-700">
                            {{ $order->amount !== null ? number_format((float) $order->amount, 2) : '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $order->approval_date->format('M d, Y') }}</td>

                        <!-- 1st approval -->
                        <td class="px-6 py-4">
                            @if($order->isGsApproved())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Approved
                                </span>
                                <div class="text-gray-700 mt-1">{{ $order->gsManagerApprover->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $order->gsManagerApprover?->getRoleDisplayName() }}
                                    &middot; {{ $order->gs_manager_approved_at->format('M d, Y H:i') }}
                                </div>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    Pending
                                </span>
                                @if(auth()->user()->canApprovePurchaseOrdersGs())
                                <form method="POST" action="{{ route('purchase-orders.approve-gs', $order) }}" class="mt-2"
                                      onsubmit="return confirm('Record the GS Manager first approval for this purchase order?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 text-xs font-medium bg-[#1C75BC] text-white rounded hover:bg-[#1664a0]">
                                        Approve
                                    </button>
                                </form>
                                @endif
                            @endif
                        </td>

                        <!-- 2nd approval -->
                        <td class="px-6 py-4">
                            @if($order->isDdgApproved())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Approved
                                </span>
                                <div class="text-gray-700 mt-1">{{ $order->ddgApprover->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $order->ddgApprover?->getRoleDisplayName() }}
                                    &middot; {{ $order->ddg_approved_at->format('M d, Y H:i') }}
                                </div>
                            @elseif(!$order->requiresDdgApproval())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Not Required
                                </span>
                                <div class="text-xs text-gray-500 mt-1">Amount is at or below ETB 400,000</div>
                            @elseif(!$order->isGsApproved())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700">
                                    Locked
                                </span>
                                <div class="text-xs font-semibold text-orange-600 mt-1">Awaiting 2nd approval</div>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    Pending
                                </span>
                                @if(auth()->user()->canApprovePurchaseOrdersDdg())
                                <form method="POST" action="{{ route('purchase-orders.approve-ddg', $order) }}" class="mt-2"
                                      onsubmit="return confirm('Record the DDG final approval for this purchase order?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 text-xs font-medium bg-[#1C75BC] text-white rounded hover:bg-[#1664a0]">
                                        Approve
                                    </button>
                                </form>
                                @endif
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            @php
                                $status = $order->getStatus();
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($status === \App\Models\PurchaseOrder::STATUS_APPROVED) bg-green-100 text-green-800
                                @elseif($status === \App\Models\PurchaseOrder::STATUS_FIRST_APPROVED) bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ $order->getStatusLabel() }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <i class="fas fa-file-invoice text-4xl text-gray-300"></i>
                            @if($search !== '')
                                <p class="mt-4 text-gray-700 font-medium">No purchase orders match &ldquo;{{ $search }}&rdquo;</p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Try a different PO number or approver name, or
                                    <a href="{{ route('purchase-orders.index') }}" class="text-[#1C75BC] hover:underline">clear the search</a>.
                                </p>
                            @else
                                <p class="mt-4 text-gray-700 font-medium">No purchase orders yet</p>
                                <p class="mt-1 text-sm text-gray-500">Create a purchase order to start the approval flow.</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchaseOrders->total() > 0)
            @php
                $poCurrent = $purchaseOrders->currentPage();
                $poLast = $purchaseOrders->lastPage();
                $poPages = [];
                for ($poPage = 1; $poPage <= $poLast; $poPage++) {
                    if ($poPage === 1 || $poPage === $poLast || abs($poPage - $poCurrent) <= 1) {
                        $poPages[] = $poPage;
                    } elseif (end($poPages) !== '...') {
                        $poPages[] = '...';
                    }
                }
            @endphp
            <div class="po-pagination no-print">
                <span>
                    Showing <strong>{{ $purchaseOrders->firstItem() }}–{{ $purchaseOrders->lastItem() }}</strong>
                    of <strong>{{ $purchaseOrders->total() }}</strong>
                </span>
                <div class="po-pages">
                    @if($poCurrent > 1)
                        <a class="po-page-btn" href="{{ $purchaseOrders->url($poCurrent - 1) }}" rel="prev">« Prev</a>
                    @else
                        <button type="button" class="po-page-btn" disabled>« Prev</button>
                    @endif
                    @foreach($poPages as $poPage)
                        @if($poPage === '...')
                            <span class="po-page-dots">...</span>
                        @else
                            <a class="po-page-btn {{ $poPage === $poCurrent ? 'po-active' : '' }}" href="{{ $purchaseOrders->url($poPage) }}" @if($poPage === $poCurrent) aria-current="page" @endif>{{ $poPage }}</a>
                        @endif
                    @endforeach
                    @if($poCurrent < $poLast)
                        <a class="po-page-btn" href="{{ $purchaseOrders->url($poCurrent + 1) }}" rel="next">Next »</a>
                    @else
                        <button type="button" class="po-page-btn" disabled>Next »</button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <p class="text-xs text-gray-500 mt-4">
        <i class="fas fa-info-circle mr-1"></i>
        Purchase orders above ETB 400,000 need both approvals (GS Manager first, then DDG). At ETB 400,000 or less, the GS Manager approval completes the process.
    </p>
</div>

@if(auth()->user()->canCreatePurchaseOrders())
<!-- New Purchase Order modal -->
<div id="poModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closePoModal()"></div>
    <div class="relative mx-auto mt-24 w-full max-w-lg px-4">
        <div class="bg-white rounded-xl shadow-xl border border-gray-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-file-invoice text-[#1C75BC] mr-2"></i>New Purchase Order
                </h3>
                <button type="button" onclick="closePoModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('purchase-orders.store') }}">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label for="po_number" class="block text-sm font-medium text-gray-700 mb-1">PO No <span class="text-red-500">*</span></label>
                        <input type="text" id="po_number" name="po_number" value="{{ old('po_number') }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1C75BC]"
                               placeholder="e.g. PO-2026-001" required>
                        @error('po_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (ETB)</label>
                        <input type="number" id="amount" name="amount" min="0" step="0.01"
                               value="{{ old('amount') }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1C75BC]"
                               placeholder="e.g. 150000.00">
                        <p class="text-xs text-gray-500 mt-1">
                            Above ETB 400,000 requires GS Manager + DDG approval; ETB 400,000 or less requires GS Manager only.
                        </p>
                        @error('amount')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="approval_date" class="block text-sm font-medium text-gray-700 mb-1">Approval Date <span class="text-red-500">*</span></label>
                        <input type="date" id="approval_date" name="approval_date"
                               value="{{ old('approval_date', now()->format('Y-m-d')) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1C75BC]"
                               required>
                        @error('approval_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="po_remarks" class="block text-sm font-medium text-gray-700 mb-1">
                            Remark <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <textarea id="po_remarks" name="remarks" rows="3" maxlength="1000"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1C75BC]"
                                  placeholder="e.g. Additional notes about this purchase order">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="text-xs text-gray-500">
                        After saving, the GS Manager records the 1st approval. Orders above ETB 400,000 then need the DDG 2nd approval.
                    </p>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                    <button type="button" onclick="closePoModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-white transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-[#1C75BC] text-white font-medium rounded-lg hover:bg-[#1664a0] transition">
                        Save Purchase Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@foreach($statCards as $card)
    @php $modalOrders = $statGroups[$card['key']]; @endphp
    <div class="po-modal-bg" id="{{ $card['id'] }}" role="dialog" aria-modal="true" aria-labelledby="{{ $card['id'] }}Title">
        <div class="po-modal">
            <div class="po-modal-head">
                <div>
                    <h3 id="{{ $card['id'] }}Title">{{ $card['label'] }}</h3>
                    <span>{{ $modalOrders->count() }} record(s)</span>
                </div>
                <button type="button" class="po-modal-close" aria-label="Close" data-po-close>×</button>
            </div>
            <div class="po-modal-body">
                @if($modalOrders->isEmpty())
                    <div class="po-empty">No purchase orders in this group.</div>
                @else
                    <div class="po-table-wrap">
                        <table class="po-table">
                            <thead>
                                <tr>
                                    <th class="po-roll">No</th>
                                    <th>PO Number</th>
                                    <th>Amount (ETB)</th>
                                    <th>Approval Date</th>
                                    <th>1st Approval (GS Manager)</th>
                                    <th>2nd Approval (DDG)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modalOrders as $order)
                                    <tr>
                                        <td class="po-roll">{{ $loop->iteration }}</td>
                                        <td style="font-weight:600;">{{ $order->po_number }}</td>
                                        <td>{{ $order->amount !== null ? number_format((float) $order->amount, 2) : '-' }}</td>
                                        <td>{{ $order->approval_date?->format('M d, Y') ?? '-' }}</td>
                                        <td>
                                            @if($order->isGsApproved())
                                                <span class="po-badge green"><i class="fas fa-check"></i>Approved</span>
                                                <div class="po-modal-person">{{ $order->gsManagerApprover->name ?? 'Unknown' }}</div>
                                                <div class="po-modal-sub">
                                                    {{ $order->gsManagerApprover?->getRoleDisplayName() }}
                                                    &middot; {{ $order->gs_manager_approved_at->format('M d, Y H:i') }}
                                                </div>
                                            @else
                                                <span class="po-badge amber">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->isDdgApproved())
                                                <span class="po-badge green"><i class="fas fa-check"></i>Approved</span>
                                                <div class="po-modal-person">{{ $order->ddgApprover->name ?? 'Unknown' }}</div>
                                                <div class="po-modal-sub">
                                                    {{ $order->ddgApprover?->getRoleDisplayName() }}
                                                    &middot; {{ $order->ddg_approved_at->format('M d, Y H:i') }}
                                                </div>
                                            @elseif(!$order->requiresDdgApproval())
                                                <span class="po-badge gray">Not Required</span>
                                                <div class="po-modal-sub mt-1">Amount is at or below ETB 400,000</div>
                                            @elseif(!$order->isGsApproved())
                                                <span class="po-badge orange">Locked</span>
                                                <div class="po-modal-sub mt-1" style="font-weight:600; color:#c2410c;">Awaiting 2nd approval</div>
                                            @else
                                                <span class="po-badge amber">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $modalStatus = $order->getStatus();
                                                $modalStatusTone = $modalStatus === \App\Models\PurchaseOrder::STATUS_APPROVED
                                                    ? 'green'
                                                    : ($modalStatus === \App\Models\PurchaseOrder::STATUS_FIRST_APPROVED ? 'blue' : 'gray');
                                            @endphp
                                            <span class="po-badge {{ $modalStatusTone }}">{{ $order->getStatusLabel() }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="po-modal-foot">
                <span class="po-modal-range"></span>
                <div class="po-modal-pages"></div>
            </div>
        </div>
    </div>
@endforeach

<div class="po-modal-bg" id="poModalPrint" role="dialog" aria-modal="true" aria-labelledby="poPrintTitle">
    <div class="po-modal po-modal-sm">
        <div class="po-modal-head">
            <div>
                <h3 id="poPrintTitle">Print Purchase Orders</h3>
                <span>Choose which sections appear on the printed page</span>
            </div>
            <button type="button" class="po-modal-close" aria-label="Close" data-po-close>×</button>
        </div>
        <div class="po-modal-body">
            <div class="po-print-options">
                <label><input type="checkbox" class="po-print-check" value="cards"> Summary cards</label>
                <label><input type="checkbox" class="po-print-check" value="table" checked> Purchase orders table</label>
            </div>
            <p id="poPrintHint" class="po-print-hint" style="display:none;">Select at least one section to print.</p>
        </div>
        <div class="po-modal-foot" style="display:flex;">
            <span></span>
            <div class="po-modal-pages">
                <button type="button" class="po-action-btn" data-po-close>Cancel</button>
                <button type="button" class="inline-flex items-center px-3 py-1.5 bg-[#1C75BC] text-white text-xs font-semibold rounded-lg hover:bg-[#1664a0]" id="poPrintGo">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openPoModal() {
        const modal = document.getElementById('poModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        const input = document.getElementById('po_number');
        if (input) input.focus();
    }

    function closePoModal() {
        const modal = document.getElementById('poModal');
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
            if (!document.querySelector('.po-modal-bg.open')) {
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
            var table = modal.querySelector('.po-modal-body .po-table');
            if (!table) return;

            var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
            if (!rows.length) return;

            var foot = modal.querySelector('.po-modal-foot');
            var rangeNode = foot && foot.querySelector('.po-modal-range');
            var pagesNode = foot && foot.querySelector('.po-modal-pages');
            if (!foot || !rangeNode || !pagesNode) return;

            var state = modal.poPager;
            if (!state) {
                state = modal.poPager = { rows: rows, page: 1, foot: foot, range: rangeNode, pages: pagesNode };
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
                    dots.className = 'po-page-dots';
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
                var active = state.pages.querySelector('.po-active');
                if (active) active.focus();
            }
        }

        function pagerButton(label, disabled, active, onClick) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'po-page-btn' + (active ? ' po-active' : '');
            button.textContent = label;
            button.disabled = disabled;
            button.addEventListener('click', onClick);
            return button;
        }

        function goToModalPage(state, page) {
            state.page = page;
            paintModalPage(state, true);
            var body = state.foot.parentNode.querySelector('.po-modal-body');
            if (body) body.scrollTop = 0;
        }

        var printModal = document.getElementById('poModalPrint');
        var printChecks = Array.prototype.slice.call(document.querySelectorAll('.po-print-check'));
        var printHint = document.getElementById('poPrintHint');

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

        Array.prototype.forEach.call(document.querySelectorAll('[data-po-modal]'), function (button) {
            button.addEventListener('click', function () {
                var modal = document.getElementById(button.getAttribute('data-po-modal'));
                initModalPager(modal);
                openModalEl(modal);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('.po-modal-bg'), function (backdrop) {
            backdrop.addEventListener('click', function (event) {
                if (event.target === backdrop) closeModal(backdrop);
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-po-close]'), function (button) {
            button.addEventListener('click', function () {
                closeModal(button.closest('.po-modal-bg'));
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-po-print-options]'), function (button) {
            button.addEventListener('click', function () {
                openPrintOptions(button.getAttribute('data-print-default'));
            });
        });

        var printGo = document.getElementById('poPrintGo');
        if (printGo) {
            printGo.addEventListener('click', printSelectedSections);
        }

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            closePoModal();
            var open = document.querySelectorAll('.po-modal-bg.open');
            if (open.length) {
                closeModal(open[open.length - 1]);
            }
        });
    })();

    @if($errors->any())
    document.addEventListener('DOMContentLoaded', openPoModal);
    @endif
</script>
@endsection
