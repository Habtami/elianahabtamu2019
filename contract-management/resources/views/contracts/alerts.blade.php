@extends('layouts.app')

@section('title', 'Contract Alerts')

@section('styles')
<style>
    .alert-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #E5E7EB;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.2s;
    }

    .alert-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .alert-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 600;
        display: inline-block;
    }

    .alert-high {
        background: #FEF2F2;
        color: #DC2626;
        border: 1px solid #FECACA;
    }

    .alert-medium {
        background: #FFFBEB;
        color: #D97706;
        border: 1px solid #FDE68A;
    }

    .contract-info {
        background: #F9FAFB;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Contract Alerts</h1>
                    <p class="text-gray-600 mt-2">Monitor contracts requiring immediate attention</p>
                </div>
                <div class="mt-4 lg:mt-0">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ count($alertContracts) > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                        <i class="fas {{ count($alertContracts) > 0 ? 'fa-exclamation-triangle' : 'fa-check-circle' }} mr-2"></i>
                        {{ count($alertContracts) }} {{ Str::plural('Alert', count($alertContracts)) }}
                    </span>
                </div>
            </div>
        </div>

        @if(count($alertContracts) > 0)
            <div class="space-y-6">
                @foreach($alertContracts as $item)
                <div class="alert-card">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $item['contract']->name }}</h3>
                            <div class="contract-info">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-500">Contract ID:</span>
                                        <span class="ml-2 text-gray-800">#{{ $item['contract']->id }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-500">Department:</span>
                                        <span class="ml-2 text-gray-800">{{ $item['contract']->getDepartmentDisplayName() }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-500">Start Date:</span>
                                        <span class="ml-2 text-gray-800">{{ $item['contract']->start_date->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 lg:mt-0 lg:ml-4">
                            <a href="{{ route('contracts.show', $item['contract']) }}"
                               class="bg-[#1C75BC] hover:bg-[#1664a0] text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center">
                                <i class="fas fa-eye mr-2"></i>
                                View Details
                            </a>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach($item['alerts'] as $alert)
                            @if($alert['type'] == 'contract_end')
                            <div class="border-l-4 border-red-500 pl-4 py-3 bg-red-50 rounded-r-lg">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <i class="fas fa-calendar-times text-red-600 mr-2"></i>
                                            <span class="font-semibold text-red-800">Contract Expiration Alert</span>
                                            <span class="alert-badge alert-high ml-2">{{ $alert['severity'] === 'high' ? 'High Priority' : 'Medium Priority' }}</span>
                                        </div>
                                        <p class="text-red-700 mb-2">{{ $alert['message'] }}</p>
                                        <p class="text-sm text-red-600">
                                            <strong>End Date:</strong> {{ $item['contract']->end_date->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <div class="action-buttons">
                                        <a href="{{ route('contracts.extend-contract-end', $item['contract']) }}"
                                           class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center text-sm">
                                            <i class="fas fa-calendar-plus mr-2"></i>
                                            Extend Contract
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($alert['type'] == 'performance_guarantee')
                            <div class="border-l-4 border-yellow-500 pl-4 py-3 bg-yellow-50 rounded-r-lg">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <i class="fas fa-shield-alt text-yellow-600 mr-2"></i>
                                            <span class="font-semibold text-yellow-800">Performance Guarantee Alert</span>
                                            <span class="alert-badge alert-medium ml-2">{{ $alert['severity'] === 'high' ? 'High Priority' : 'Medium Priority' }}</span>
                                        </div>
                                        <p class="text-yellow-700 mb-2">{{ $alert['message'] }}</p>
                                        <div class="text-sm text-yellow-600 space-y-1">
                                            <p><strong>PG End Date:</strong> {{ $item['contract']->performance_guarantee_end_date->format('M d, Y') }}</p>
                                            <p><strong>PG Amount:</strong> ${{ number_format($item['contract']->performance_guarantee_amount, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="action-buttons">
                                        <a href="{{ route('contracts.extend-performance-guarantee', $item['contract']) }}"
                                           class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center text-sm">
                                            <i class="fas fa-shield-plus mr-2"></i>
                                            Extend PG
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="alert-card text-center py-12">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-green-500 text-6xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">All Clear!</h3>
                <p class="text-gray-600 mb-4">No contracts require immediate attention at this time.</p>
                <p class="text-sm text-gray-500">
                    Alerts will appear here when contracts or performance guarantees are approaching their end dates based on the configured alert periods.
                </p>
            </div>
        @endif

        <!-- Back Button -->
        <div class="mt-8 text-center">
            <a href="{{ route('contracts.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Contracts
            </a>
        </div>
    </div>
</div>

<script>
    // Auto-refresh alerts every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000); // 5 minutes
</script>
@endsection
