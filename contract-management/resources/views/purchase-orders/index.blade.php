@extends('layouts.app')

@section('title', 'Purchase Order Processing')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-shopping-cart text-[#1C75BC] mr-3"></i>
                Purchase Order Processing
            </h1>
            <p class="text-gray-600 mt-2">Register purchase orders and track approvals: above ETB 400,000 needs GS Manager and DDG; ETB 400,000 or less needs GS Manager only.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('purchase-orders.export.excel', request()->query()) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-excel mr-2"></i>Excel
            </a>
            <a href="{{ route('purchase-orders.export.pdf', request()->query()) }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-file-pdf mr-2"></i>PDF
            </a>
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
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <button type="button" onclick="openPoStatModal('total', 'Total POs')"
                class="relative overflow-hidden text-left w-full bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 hover:shadow-md hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#1C75BC] transition">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-[#1C75BC]"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">Total POs</div>
            <div class="text-2xl font-bold text-gray-800 mt-1">{{ $total }}</div>
        </button>
        <button type="button" onclick="openPoStatModal('pending_first', 'Awaiting 1st Approval')"
                class="relative overflow-hidden text-left w-full bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 hover:shadow-md hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-amber-500"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">Awaiting 1st Approval</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingFirst }}</div>
        </button>
        <button type="button" onclick="openPoStatModal('pending_second', 'Awaiting 2nd Approval')"
                class="relative overflow-hidden text-left w-full bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 hover:shadow-md hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-blue-500"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">Awaiting 2nd Approval</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ $pendingSecond }}</div>
        </button>
        <button type="button" onclick="openPoStatModal('approved', 'Fully Approved')"
                class="relative overflow-hidden text-left w-full bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 hover:shadow-md hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-green-500"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">Fully Approved</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ $approved }}</div>
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
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
            <form method="GET" action="{{ route('purchase-orders.index') }}" class="flex flex-wrap items-center gap-2">
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('dir'))
                    <input type="hidden" name="dir" value="{{ request('dir') }}">
                @endif
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search PO number or approver..."
                           class="w-full sm:w-72 rounded-lg border border-gray-300 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1C75BC]">
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

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
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
                        <td colspan="6" class="px-6 py-16 text-center">
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

        @if($purchaseOrders->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $purchaseOrders->links() }}
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

<!-- Stat card detail modal -->
<div id="poStatModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closePoStatModal()"></div>
    <div class="relative mx-auto mt-12 w-full max-w-5xl px-4">
        <div class="bg-white rounded-xl shadow-xl border border-gray-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 id="poStatModalTitle" class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-shopping-cart text-[#1C75BC] mr-2"></i>Purchase Orders
                </h3>
                <button type="button" onclick="closePoStatModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="px-6 py-4">
                <p id="poStatModalMeta" class="text-sm text-gray-600 mb-3"></p>

                <div id="poStatModalLoading" class="py-10 text-center text-gray-500 hidden">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Loading purchase orders...
                </div>

                <div id="poStatModalEmpty" class="py-10 text-center text-gray-500 hidden">
                    <i class="fas fa-inbox text-3xl text-gray-300 block mb-2"></i>
                    No purchase orders in this group.
                </div>

                <div id="poStatModalTableWrap" class="overflow-x-auto max-h-[60vh] overflow-y-auto rounded-lg border border-gray-200 hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600 sticky top-0">
                            <tr>
                                <th class="px-4 py-2.5 font-medium">PO Number</th>
                                <th class="px-4 py-2.5 font-medium">Amount (ETB)</th>
                                <th class="px-4 py-2.5 font-medium">Approval Date</th>
                                <th class="px-4 py-2.5 font-medium">1st Approval (GS Manager)</th>
                                <th class="px-4 py-2.5 font-medium">2nd Approval (DDG)</th>
                                <th class="px-4 py-2.5 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody id="poStatModalBody" class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-end px-6 py-3 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <button type="button" onclick="closePoStatModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-white transition">
                    Close
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

    let poStatRequestSeq = 0;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        }[char]));
    }

    function poBadgeClass(tone) {
        return {
            green: 'bg-green-100 text-green-800',
            amber: 'bg-amber-100 text-amber-800',
            orange: 'bg-orange-100 text-orange-700',
            blue: 'bg-blue-100 text-blue-800',
            gray: 'bg-gray-100 text-gray-700',
        }[tone] || 'bg-gray-100 text-gray-700';
    }

    function openPoStatModal(scope, title) {
        const modal = document.getElementById('poStatModal');
        if (!modal) return;

        document.getElementById('poStatModalTitle').innerHTML =
            '<i class="fas fa-shopping-cart text-[#1C75BC] mr-2"></i>' + escapeHtml(title);
        document.getElementById('poStatModalMeta').textContent = '';
        document.getElementById('poStatModalBody').innerHTML = '';
        document.getElementById('poStatModalEmpty').classList.add('hidden');
        document.getElementById('poStatModalTableWrap').classList.add('hidden');
        document.getElementById('poStatModalLoading').classList.remove('hidden');

        modal.classList.remove('hidden');
        loadPoStatSummary(scope);
    }

    function closePoStatModal() {
        const modal = document.getElementById('poStatModal');
        if (modal) modal.classList.add('hidden');
    }

    async function loadPoStatSummary(scope) {
        const seq = ++poStatRequestSeq;

        try {
            const response = await fetch('{{ url('/purchase-orders/summary') }}/' + encodeURIComponent(scope), {
                headers: { 'Accept': 'application/json' },
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(data.message || 'Unable to load purchase orders.');
            }

            if (seq !== poStatRequestSeq) return;

            renderPoStatSummary(data);
        } catch (error) {
            if (seq !== poStatRequestSeq) return;

            document.getElementById('poStatModalLoading').classList.add('hidden');
            document.getElementById('poStatModalMeta').textContent = error.message;
        }
    }

    function renderPoStatSummary(data) {
        const loading = document.getElementById('poStatModalLoading');
        const meta = document.getElementById('poStatModalMeta');
        const body = document.getElementById('poStatModalBody');
        const tableWrap = document.getElementById('poStatModalTableWrap');
        const empty = document.getElementById('poStatModalEmpty');

        loading.classList.add('hidden');
        meta.textContent = data.total === 0
            ? 'No records'
            : `Showing ${data.items.length} of ${data.total} record(s)` + (data.limited ? ' — latest 50 shown' : '');

        if (!data.items.length) {
            body.innerHTML = '';
            tableWrap.classList.add('hidden');
            empty.classList.remove('hidden');
            return;
        }

        empty.classList.add('hidden');
        tableWrap.classList.remove('hidden');
        tableWrap.scrollTop = 0;

        body.innerHTML = data.items.map((item) => {
            const gsBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' +
                poBadgeClass(item.gs_status === 'Approved' ? 'green' : 'amber') + '">' + escapeHtml(item.gs_status) + '</span>';
            const gsMeta = item.gs_approver
                ? '<div class="text-gray-700 mt-1">' + escapeHtml(item.gs_approver) + '</div>' +
                  (item.gs_approved_at ? '<div class="text-xs text-gray-500">' + escapeHtml(item.gs_approved_at) + '</div>' : '')
                : '';

            const isLocked = item.ddg_status === 'Locked';
            const ddgTone = item.ddg_status === 'Approved'
                ? 'green'
                : (item.ddg_status === 'Pending' ? 'amber' : (isLocked ? 'orange' : 'gray'));
            const ddgBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs ' +
                (isLocked ? 'font-bold' : 'font-medium') + ' ' +
                poBadgeClass(ddgTone) + '">' + escapeHtml(item.ddg_status) + '</span>';
            let ddgMeta = '';
            if (item.ddg_approver) {
                ddgMeta = '<div class="text-gray-700 mt-1">' + escapeHtml(item.ddg_approver) + '</div>' +
                    (item.ddg_approved_at ? '<div class="text-xs text-gray-500">' + escapeHtml(item.ddg_approved_at) + '</div>' : '');
            } else if (item.ddg_status === 'Not Required') {
                ddgMeta = '<div class="text-xs text-gray-500 mt-1">Amount is at or below ETB 400,000</div>';
            } else if (isLocked) {
                ddgMeta = '<div class="text-xs font-semibold text-orange-600 mt-1">Awaiting 2nd approval</div>';
            }

            const statusTone = item.status_key === 'approved' ? 'green' : (item.status_key === 'first_approved' ? 'blue' : 'gray');
            const statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' +
                poBadgeClass(statusTone) + '">' + escapeHtml(item.status) + '</span>';

            return '<tr class="hover:bg-gray-50 align-top">' +
                '<td class="px-4 py-3 font-semibold text-gray-800 whitespace-nowrap">' + escapeHtml(item.po_number) + '</td>' +
                '<td class="px-4 py-3 text-gray-700 whitespace-nowrap">' + escapeHtml(item.amount) + '</td>' +
                '<td class="px-4 py-3 text-gray-700 whitespace-nowrap">' + escapeHtml(item.approval_date || '-') + '</td>' +
                '<td class="px-4 py-3">' + gsBadge + gsMeta + '</td>' +
                '<td class="px-4 py-3">' + ddgBadge + ddgMeta + '</td>' +
                '<td class="px-4 py-3">' + statusBadge + '</td>' +
                '</tr>';
        }).join('');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closePoModal();
            closePoStatModal();
        }
    });

    @if($errors->any())
    document.addEventListener('DOMContentLoaded', openPoModal);
    @endif
</script>
@endsection
