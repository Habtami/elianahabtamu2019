@extends('layouts.app')

@section('title', 'Driver Details')

@section('page-title', 'Driver Details')

@section('styles')
<style>
    :root {
        --epss-blue: #1C75BC;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
    }
    .info-box {
        background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%);
        border: 1px solid #BAE6FD;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.875rem;
        border-radius: 9999px;
        font-weight: 700;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        text-align: center;
    }
    .stats-item {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        border: 1px solid #E5E7EB;
    }
    .stats-item .value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--epss-blue);
    }
    .table-cell { padding: 1rem; text-align: left; vertical-align: middle; }
    .table-row { border-bottom: 1px solid #F1F5F9; }
    .table-header { background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%); border-bottom: 2px solid #E5E7EB; }
</style>
@endsection

@section('content')
<div class="page-container py-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Driver Details</h1>
                    <p class="text-gray-600 mt-2">{{ $driver->name }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('drivers.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Drivers
                    </a>
                    @if(auth()->user()->canEditDrivers())
                    <a href="{{ route('drivers.edit', $driver) }}" class="bg-[#1C75BC] hover:bg-[#1664a0] text-white font-semibold py-2 px-4 rounded-lg flex items-center transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Driver
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Driver Info Card -->
            <div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center mb-6">
                    @if($driver->photo)
                        <img src="{{ asset('storage/' . $driver->photo) }}" alt="{{ $driver->name }}" class="w-24 h-24 rounded-full object-cover mx-auto mb-4">
                    @else
                        <div class="w-24 h-24 bg-gradient-to-br from-gray-300 to-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user text-3xl text-gray-500"></i>
                        </div>
                    @endif

                    <h2 class="text-xl font-bold text-gray-800 mb-2">{{ $driver->name }}</h2>

                    @if($driver->isLicenseExpired())
                        <span class="status-badge bg-red-100 text-red-800 mb-2 block">License Expired</span>
                    @elseif($driver->isLicenseExpiringSoon())
                        <span class="status-badge bg-yellow-100 text-yellow-800 mb-2 block">License Expiring Soon</span>
                    @endif

                    <span class="status-badge {{ $driver->status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} mb-4 block">
                        {{ $driver->status ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Contact Information</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-id-card fa-fw text-gray-400 mr-3 w-5"></i>
                            <div>
                                <span class="text-gray-500">License #:</span>
                                <span class="font-medium text-gray-800">{{ $driver->license_number ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone fa-fw text-gray-400 mr-3 w-5"></i>
                            <div>
                                <span class="text-gray-500">Phone:</span>
                                <span class="font-medium text-gray-800">{{ $driver->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-envelope fa-fw text-gray-400 mr-3 w-5"></i>
                            <div>
                                <span class="text-gray-500">Email:</span>
                                <span class="font-medium text-gray-800">{{ $driver->email ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">License Information</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-500">Expiry Date:</span>
                            <span class="font-medium text-gray-800">
                                {{ $driver->license_expiry ? $driver->license_expiry->format('Y-m-d') : 'N/A' }}
                            </span>
                        </div>
                        @if($driver->license_expiry)
                            @if($driver->isLicenseExpired())
                                <div class="flex items-center text-red-600">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    Expired {{ now()->diffInDays($driver->license_expiry) }} days ago
                                </div>
                            @elseif($driver->isLicenseExpiringSoon())
                                <div class="flex items-center text-yellow-600">
                                    <i class="fas fa-clock mr-2"></i>
                                    Expiring in {{ now()->diffInDays($driver->license_expiry) }} days
                                </div>
                            @else
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Valid for {{ now()->diffInDays($driver->license_expiry) }} days
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Driver Statistics and History -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Driver Statistics</h3>
                    <div class="stats-grid">
                        <div class="stats-item">
                            <div class="text-gray-500 text-sm font-medium mb-1">Total Tires</div>
                            <div class="value">{{ $totalTires }}</div>
                        </div>
                        <div class="stats-item">
                            <div class="text-gray-500 text-sm font-medium mb-1">Active Tires</div>
                            <div class="value text-green-600">{{ $activeTires }}</div>
                        </div>
                        <div class="stats-item">
                            <div class="text-gray-500 text-sm font-medium mb-1">Total Returns</div>
                            <div class="value text-blue-600">{{ $tireReturns->count() }}</div>
                        </div>
                    </div>
                </div>

                <!-- Give History -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Tire Give History</h3>
                    @if($tireGives->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="table-header">
                                <tr>
                                    <th class="table-cell font-semibold text-gray-700">Date</th>
                                    <th class="table-cell font-semibold text-gray-700">Tire Type</th>
                                    <th class="table-cell font-semibold text-gray-700">Serial</th>
                                    <th class="table-cell font-semibold text-gray-700">KM at Give</th>
                                    <th class="table-cell font-semibold text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tireGives as $give)
                                <tr class="table-row">
                                    <td class="table-cell text-gray-600">{{ $give->give_date->format('Y-m-d') }}</td>
                                    <td class="table-cell font-medium text-gray-800">{{ $give->tire->tire_type }}</td>
                                    <td class="table-cell text-gray-600 font-mono">{{ $give->tire->serial_number }}</td>
                                    <td class="table-cell text-gray-600">{{ $give->given_kilometer }}</td>
                                    <td class="table-cell">
                                        @if($give->is_returned)
                                            <span class="status-badge bg-green-100 text-green-800">Returned</span>
                                        @else
                                            <span class="status-badge bg-yellow-100 text-yellow-800">In Use</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-3">
                            <i class="fas fa-hand-holding text-4xl"></i>
                        </div>
                        <p class="text-gray-500">No tires have been given to this driver</p>
                    </div>
                    @endif
                </div>

                <!-- Return History -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Return History</h3>
                    @if($tireReturns->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="table-header">
                                <tr>
                                    <th class="table-cell font-semibold text-gray-700">Date</th>
                                    <th class="table-cell font-semibold text-gray-700">Tire</th>
                                    <th class="table-cell font-semibold text-gray-700">Serial</th>
                                    <th class="table-cell font-semibold text-gray-700">Current KM</th>
                                    <th class="table-cell font-semibold text-gray-700">KM Difference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tireReturns as $ret)
                                <tr class="table-row">
                                    <td class="table-cell text-gray-600">{{ $ret->return_date->format('Y-m-d') }}</td>
                                    <td class="table-cell font-medium text-gray-800">{{ $ret->tire->tire_type ?? $ret->tire_type }}</td>
                                    <td class="table-cell text-gray-600 font-mono">{{ $ret->serial_number }}</td>
                                    <td class="table-cell text-gray-600">{{ $ret->current_kilometer }}</td>
                                    <td class="table-cell">
                                        <span class="{{ $ret->km_difference >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                            {{ $ret->km_difference }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-3">
                            <i class="fas fa-undo-alt text-4xl"></i>
                        </div>
                        <p class="text-gray-500">No return records for this driver</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection