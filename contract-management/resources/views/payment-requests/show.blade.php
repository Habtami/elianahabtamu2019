@extends('layouts.app')

@section('title', 'Payment Request Details')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-money-bill-wave text-[#7c3aed] mr-3"></i>
                {{ $paymentRequest->getReference() }}
            </h1>
            <p class="text-gray-600 mt-2">Payment request registered on {{ $paymentRequest->payment_request_date->format('M d, Y') }}.</p>
        </div>
        <a href="{{ route('payment-requests.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Payment Requests
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Request details -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Request Details</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Request No.</span>
                    <span class="font-medium text-gray-800">{{ $paymentRequest->getReference() }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Payment Request Date</span>
                    <span class="font-medium text-gray-800">{{ $paymentRequest->payment_request_date->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Document No</span>
                    <span class="font-medium text-gray-800">{{ $paymentRequest->document_no ?: '—' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Payment Type</span>
                    <span class="font-medium text-gray-800">{{ $paymentRequest->payment_type ?: '—' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Amount Price</span>
                    <span class="font-medium text-gray-800">
                        {{ $paymentRequest->amount !== null ? 'ETB ' . number_format((float) $paymentRequest->amount, 2) : '—' }}
                    </span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Quantity</span>
                    <span class="font-medium text-gray-800">
                        {{ $paymentRequest->quantity !== null ? rtrim(rtrim(number_format((float) $paymentRequest->quantity, 2), '0'), '.') : '—' }}
                    </span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Material Group</span>
                    <span class="font-medium text-gray-800">{{ $paymentRequest->material_group ?: '—' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Procurement Type</span>
                    <span class="font-medium text-gray-800">{{ $paymentRequest->getProcurementTypeLabel() ?: '—' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Registered By</span>
                    <span class="font-medium text-gray-800">{{ $paymentRequest->creator->name ?? 'Unknown' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Registered At</span>
                    <span class="font-medium text-gray-800">{{ $paymentRequest->created_at?->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Payment Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($paymentRequest->isFinanceCompleted()) bg-green-100 text-green-800
                        @else bg-amber-100 text-amber-800 @endif">
                        {{ $paymentRequest->getStatusLabel() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Approval flow -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Approval Flow</h2>
            </div>
            <div class="divide-y divide-gray-100">
                <!-- GS -->
                <div class="px-6 py-5">
                    <div class="flex items-center justify-between">
                        <div class="font-medium text-gray-800">GS (1st Approval)</div>
                        @if($paymentRequest->isGsApproved())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>Approved
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Pending</span>
                        @endif
                    </div>
                    @if($paymentRequest->isGsApproved())
                        <p class="text-sm text-gray-700 mt-2">{{ $paymentRequest->gsApprover->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $paymentRequest->gsApprover?->getRoleDisplayName() }}
                            &middot; {{ $paymentRequest->gs_approved_at->format('M d, Y H:i') }}
                        </p>
                    @else
                        <p class="text-xs text-gray-500 mt-1">Not yet approved.</p>
                    @endif
                </div>

                <!-- Finance -->
                <div class="px-6 py-5">
                    <div class="flex items-center justify-between">
                        <div class="font-medium text-gray-800">Finance (2nd Approval)</div>
                        @if($paymentRequest->isFinanceCompleted())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>Completed
                            </span>
                        @elseif(!$paymentRequest->isGsApproved())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Locked</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Pending</span>
                        @endif
                    </div>
                    @if($paymentRequest->isFinanceCompleted())
                        <p class="text-sm text-gray-700 mt-2">{{ $paymentRequest->financeApprover->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $paymentRequest->financeApprover?->getRoleDisplayName() }}
                            &middot; {{ $paymentRequest->finance_approved_at->format('M d, Y H:i') }}
                        </p>
                    @elseif(!$paymentRequest->isGsApproved())
                        <p class="text-xs text-gray-500 mt-1">Awaiting GS approval before Finance can complete.</p>
                    @else
                        <p class="text-xs text-gray-500 mt-1">Awaiting Finance completion.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
