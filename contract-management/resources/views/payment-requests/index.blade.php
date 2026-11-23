@extends('layouts.app')

@section('title', 'Payment Request')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-money-bill-wave text-[#7c3aed] mr-3"></i>
                Payment Request
            </h1>
            <p class="text-gray-600 mt-2">Register payment requests and track the two-stage flow: GS approval first, Finance completion second.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
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
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <button type="button" onclick="openPrStatModal('total', 'Total Requests')"
                class="relative overflow-hidden text-left w-full bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 hover:shadow-md hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#7c3aed] transition">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-[#7c3aed]"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">Total Requests</div>
            <div class="text-2xl font-bold text-gray-800 mt-1">{{ $total }}</div>
        </button>
        <button type="button" onclick="openPrStatModal('pending', 'Pending')"
                class="relative overflow-hidden text-left w-full bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 hover:shadow-md hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-amber-500"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">Pending</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">{{ $pending }}</div>
        </button>
        <button type="button" onclick="openPrStatModal('complete', 'Complete')"
                class="relative overflow-hidden text-left w-full bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 hover:shadow-md hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-green-500"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">Complete</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ $complete }}</div>
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
                return $currentDir === 'asc' ? 'fa-sort-up text-[#7c3aed]' : 'fa-sort-down text-[#7c3aed]';
            };
        @endphp

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 px-6 py-4 border-b border-gray-200">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Payment Requests</h2>
                <p class="text-sm text-gray-500 mt-0.5" id="prResultSummary">
                    @if($search !== '')
                        {{ $paymentRequests->total() }} result(s) for &ldquo;{{ $search }}&rdquo;
                    @else
                        {{ $paymentRequests->total() }} request(s)
                    @endif
                    &middot; page {{ $paymentRequests->currentPage() }} of {{ $paymentRequests->lastPage() }}
                </p>
            </div>
            <form method="GET" action="{{ route('payment-requests.index') }}" id="prSearchForm" class="flex flex-wrap items-center gap-2">
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('dir'))
                    <input type="hidden" name="dir" value="{{ request('dir') }}">
                @endif
                <div class="relative">
                    <i id="prSearchIcon" class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" id="prSearchInput" value="{{ $search }}" autocomplete="off"
                           placeholder="Search PR no., date, remark or approver..."
                           class="w-full sm:w-80 rounded-lg border border-gray-300 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#7c3aed]">
                </div>
                <button type="submit"
                        class="px-3 py-2 bg-[#7c3aed] text-white text-sm font-medium rounded-lg hover:bg-[#6d28d9] transition">
                    Search
                </button>
                <a href="{{ route('payment-requests.index') }}" id="prClearLink"
                   class="px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition {{ $search !== '' ? '' : 'hidden' }}">
                    Clear
                </a>
            </form>
        </div>

        <div id="prResults">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
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
                        <th class="px-6 py-3 font-medium">View</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($paymentRequests as $paymentRequest)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $paymentRequest->getReference() }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $paymentRequest->payment_request_date->format('M d, Y') }}</td>

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
                                <form method="POST" action="{{ route('payment-requests.approve-gs', $paymentRequest) }}" class="mt-2"
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
                                <form method="POST" action="{{ route('payment-requests.complete', $paymentRequest) }}" class="mt-2"
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

                        <td class="px-6 py-4">
                            <a href="{{ route('payment-requests.show', $paymentRequest) }}"
                               class="inline-flex items-center px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition"
                               title="View payment request">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
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

        @if($paymentRequests->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $paymentRequests->links() }}
        </div>
        @endif
        </div>
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
            <form method="POST" action="{{ route('payment-requests.store') }}">
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

<!-- Stat card detail modal -->
<div id="prStatModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closePrStatModal()"></div>
    <div class="relative mx-auto mt-12 w-full max-w-5xl px-4">
        <div class="bg-white rounded-xl shadow-xl border border-gray-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 id="prStatModalTitle" class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-money-bill-wave text-[#7c3aed] mr-2"></i>Payment Requests
                </h3>
                <button type="button" onclick="closePrStatModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="px-6 py-4">
                <p id="prStatModalMeta" class="text-sm text-gray-600 mb-3"></p>

                <div id="prStatModalLoading" class="py-10 text-center text-gray-500 hidden">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Loading payment requests...
                </div>

                <div id="prStatModalEmpty" class="py-10 text-center text-gray-500 hidden">
                    <i class="fas fa-inbox text-3xl text-gray-300 block mb-2"></i>
                    No payment requests in this group.
                </div>

                <div id="prStatModalTableWrap" class="overflow-x-auto max-h-[60vh] overflow-y-auto rounded-lg border border-gray-200 hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600 sticky top-0">
                            <tr>
                                <th class="px-4 py-2.5 font-medium">Request No.</th>
                                <th class="px-4 py-2.5 font-medium">Payment Request Date</th>
                                <th class="px-4 py-2.5 font-medium">GS (1st Approval)</th>
                                <th class="px-4 py-2.5 font-medium">Finance (2nd Approval)</th>
                                <th class="px-4 py-2.5 font-medium">Payment Status</th>
                                <th class="px-4 py-2.5 font-medium">Remark</th>
                            </tr>
                        </thead>
                        <tbody id="prStatModalBody" class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-end px-6 py-3 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <button type="button" onclick="closePrStatModal()"
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
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('prSearchForm');
        const input = document.getElementById('prSearchInput');
        const results = document.getElementById('prResults');
        const summary = document.getElementById('prResultSummary');
        const icon = document.getElementById('prSearchIcon');
        const clearLink = document.getElementById('prClearLink');
        if (!form || !input || !results) return;

        let timer = null;
        let controller = null;

        function setBusy(busy) {
            results.setAttribute('aria-busy', busy ? 'true' : 'false');
            results.classList.toggle('opacity-60', busy);
            if (icon) {
                icon.classList.toggle('fa-search', !busy);
                icon.classList.toggle('fa-spinner', busy);
                icon.classList.toggle('fa-spin', busy);
            }
        }

        function buildUrl() {
            const params = new URLSearchParams();
            new FormData(form).forEach(function (value, key) {
                if (String(value).trim() !== '') params.set(key, value);
            });
            const query = params.toString();
            return form.action + (query ? '?' + query : '');
        }

        async function runSearch() {
            if (controller) controller.abort();
            const current = new AbortController();
            controller = current;
            setBusy(true);

            try {
                const url = buildUrl();
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: current.signal,
                });
                if (!response.ok) throw new Error('Search failed with status ' + response.status);

                const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
                const freshResults = doc.getElementById('prResults');
                const freshSummary = doc.getElementById('prResultSummary');
                if (freshResults) results.innerHTML = freshResults.innerHTML;
                if (freshSummary && summary) summary.innerHTML = freshSummary.innerHTML;

                window.history.replaceState(null, '', url);
            } catch (error) {
                if (error.name !== 'AbortError') console.error(error);
            } finally {
                if (controller === current) {
                    controller = null;
                    setBusy(false);
                }
            }

            if (clearLink) clearLink.classList.toggle('hidden', input.value.trim() === '');
        }

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(runSearch, 350);
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearTimeout(timer);
            runSearch();
        });
    });

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

    function toggleNewProcurementType() {
        const select = document.getElementById('procurement_type');
        const wrap = document.getElementById('newProcurementTypeWrap');
        if (!select || !wrap) return;

        const isNew = select.value === '__new__';
        wrap.classList.toggle('hidden', !isNew);

        if (isNew) {
            const input = document.getElementById('newProcurementTypeName');
            if (input) input.focus();
        }
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

        const isNew = select.value === '__new__';
        wrap.classList.toggle('hidden', !isNew);

        if (isNew) {
            const input = document.getElementById('newPaymentTypeName');
            if (input) input.focus();
        }
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

    let prStatRequestSeq = 0;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        }[char]));
    }

    function prBadgeClass(tone) {
        return {
            green: 'bg-green-100 text-green-800',
            amber: 'bg-amber-100 text-amber-800',
            orange: 'bg-orange-100 text-orange-700',
            gray: 'bg-gray-100 text-gray-700',
        }[tone] || 'bg-gray-100 text-gray-700';
    }

    function openPrStatModal(scope, title) {
        const modal = document.getElementById('prStatModal');
        if (!modal) return;

        document.getElementById('prStatModalTitle').innerHTML =
            '<i class="fas fa-money-bill-wave text-[#7c3aed] mr-2"></i>' + escapeHtml(title);
        document.getElementById('prStatModalMeta').textContent = '';
        document.getElementById('prStatModalBody').innerHTML = '';
        document.getElementById('prStatModalEmpty').classList.add('hidden');
        document.getElementById('prStatModalTableWrap').classList.add('hidden');
        document.getElementById('prStatModalLoading').classList.remove('hidden');

        modal.classList.remove('hidden');
        loadPrStatSummary(scope);
    }

    function closePrStatModal() {
        const modal = document.getElementById('prStatModal');
        if (modal) modal.classList.add('hidden');
    }

    async function loadPrStatSummary(scope) {
        const seq = ++prStatRequestSeq;

        try {
            const response = await fetch('{{ url('/payment-requests/summary') }}/' + encodeURIComponent(scope), {
                headers: { 'Accept': 'application/json' },
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(data.message || 'Unable to load payment requests.');
            }

            if (seq !== prStatRequestSeq) return;

            renderPrStatSummary(data);
        } catch (error) {
            if (seq !== prStatRequestSeq) return;

            document.getElementById('prStatModalLoading').classList.add('hidden');
            document.getElementById('prStatModalMeta').textContent = error.message;
        }
    }

    function renderPrStatSummary(data) {
        const loading = document.getElementById('prStatModalLoading');
        const meta = document.getElementById('prStatModalMeta');
        const body = document.getElementById('prStatModalBody');
        const tableWrap = document.getElementById('prStatModalTableWrap');
        const empty = document.getElementById('prStatModalEmpty');

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
                prBadgeClass(item.gs_status === 'Approved' ? 'green' : 'amber') + '">' + escapeHtml(item.gs_status) + '</span>';
            const gsMeta = item.gs_approver
                ? '<div class="text-gray-700 mt-1">' + escapeHtml(item.gs_approver) + '</div>' +
                  (item.gs_approved_at ? '<div class="text-xs text-gray-500">' + escapeHtml(item.gs_approved_at) + '</div>' : '')
                : '';

            const isLocked = item.finance_status === 'Locked';
            const financeTone = item.finance_status === 'Completed'
                ? 'green'
                : (item.finance_status === 'Pending' ? 'amber' : 'orange');
            const financeBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs ' +
                (isLocked ? 'font-bold' : 'font-medium') + ' ' +
                prBadgeClass(financeTone) + '">' + escapeHtml(item.finance_status) + '</span>';
            let financeMeta = '';
            if (item.finance_approver) {
                financeMeta = '<div class="text-gray-700 mt-1">' + escapeHtml(item.finance_approver) + '</div>' +
                    (item.finance_approved_at ? '<div class="text-xs text-gray-500">' + escapeHtml(item.finance_approved_at) + '</div>' : '');
            } else if (isLocked) {
                financeMeta = '<div class="text-xs font-semibold text-orange-600 mt-1">Awaiting GS approval</div>';
            }

            const statusTone = item.status_key === 'complete' ? 'green' : 'amber';
            const statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' +
                prBadgeClass(statusTone) + '">' + escapeHtml(item.status) + '</span>';

            return '<tr class="hover:bg-gray-50 align-top">' +
                '<td class="px-4 py-3 font-semibold text-gray-800 whitespace-nowrap">' + escapeHtml(item.reference) + '</td>' +
                '<td class="px-4 py-3 text-gray-700 whitespace-nowrap">' + escapeHtml(item.date || '-') + '</td>' +
                '<td class="px-4 py-3">' + gsBadge + gsMeta + '</td>' +
                '<td class="px-4 py-3">' + financeBadge + financeMeta + '</td>' +
                '<td class="px-4 py-3">' + statusBadge + '</td>' +
                '<td class="px-4 py-3 text-gray-700">' + escapeHtml(item.remarks || '—') + '</td>' +
                '</tr>';
        }).join('');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closePrModal();
            closePrStatModal();
        }
    });

    @if($errors->any())
    document.addEventListener('DOMContentLoaded', openPrModal);
    @endif
</script>
@endsection
