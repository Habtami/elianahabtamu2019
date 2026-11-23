@extends('layouts.app')

@section('title', 'Contract Closing Details')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-file-signature text-[#0d9488] mr-3"></i>
                {{ $contractClosure->contract->name ?? 'Contract Closing' }}
            </h1>
            <p class="text-gray-600 mt-2">Closing record for {{ $contractClosure->closing_date->format('M d, Y') }}.</p>
        </div>
        <a href="{{ route('contract-closures.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Contract Closing
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Contract details -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Contract Details</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Contract</span>
                    <span class="font-medium text-gray-800 text-right">{{ $contractClosure->contract->name ?? 'Deleted contract' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Supplier</span>
                    <span class="font-medium text-gray-800 text-right">{{ $contractClosure->contract->supplier_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Department</span>
                    <span class="font-medium text-gray-800">{{ $contractClosure->contract ? $contractClosure->contract->getDepartmentDisplayName() : '-' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Contract Period</span>
                    <span class="font-medium text-gray-800 text-right">
                        {{ $contractClosure->contract?->start_date?->format('M d, Y') ?? '-' }}
                        &ndash;
                        {{ $contractClosure->contract?->end_date?->format('M d, Y') ?? '-' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Closing details -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Closing Details</h2>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Closing Date</span>
                    <span class="font-medium text-gray-800">{{ $contractClosure->closing_date->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($contractClosure->isComplete()) bg-green-100 text-green-800
                        @else bg-amber-100 text-amber-800 @endif">
                        {{ $contractClosure->getStatusLabel() }}
                    </span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Requested By</span>
                    <span class="font-medium text-gray-800">{{ $contractClosure->creator->name ?? 'Unknown' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Requested At</span>
                    <span class="font-medium text-gray-800">{{ $contractClosure->created_at?->format('M d, Y H:i') }}</span>
                </div>
                @if($contractClosure->isComplete())
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Completed By</span>
                    <span class="font-medium text-gray-800">{{ $contractClosure->completer->name ?? 'Unknown' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Completed At</span>
                    <span class="font-medium text-gray-800">{{ $contractClosure->completed_at?->format('M d, Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($contractClosure->remarks)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Remarks</h2>
        </div>
        <div class="px-6 py-5 text-sm text-gray-700 whitespace-pre-line">{{ $contractClosure->remarks }}</div>
    </div>
    @endif
</div>
@endsection
