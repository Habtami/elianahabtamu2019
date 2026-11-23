@extends('layouts.app')

@section('title', 'Tire Tracking Reports')

@section('page-title', 'Tire Tracking Reports')

@section('styles')
<style>
    :root {
        --epss-blue: #1C75BC;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
    }
    .chart-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #E5E7EB;
        padding: 1.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    .stats-card {
        background: white;
        border-radius: 1rem;
        border: 1px solid #E2E8F0;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }
    .stats-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--epss-blue) 0%, var(--epss-blue-light) 100%);
        border-radius: 0.75rem 0.75rem 0 0;
    }
    .stats-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@section('content')
<div class="page-container py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Tire Tracking Reports</h1>
                    <p class="text-gray-600 mt-2">Overview of tire operations and statistics</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="stats-card relative" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#exportModal">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Export</p>
                        <p class="text-3xl font-bold text-[#1C75BC] mt-2">
                            <i class="fas fa-file-export"></i>
                        </p>
                    </div>
                    <div class="stats-icon bg-gradient-to-br from-blue-400 to-blue-600">
                        <i class="fas fa-file-excel text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card relative" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#moreModal">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">More Actions</p>
                        <p class="text-3xl font-bold text-gray-700 mt-2">
                            <i class="fas fa-ellipsis-v"></i>
                        </p>
                    </div>
                    <div class="stats-icon bg-gradient-to-br from-gray-400 to-gray-600">
                        <i class="fas fa-cog text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card relative" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#totalTiresModal">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Tires</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalTires }}</p>
                    </div>
                    <div class="stats-icon bg-gradient-to-br from-blue-400 to-blue-600">
                        <i class="fas fa-tire text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card relative" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#activeTiresModal">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Active Tires</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $activeTires }}</p>
                    </div>
                    <div class="stats-icon bg-gradient-to-br from-green-400 to-green-600">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card relative" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#givenOutModal">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Given Out</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $givenOutTires }}</p>
                    </div>
                    <div class="stats-icon bg-gradient-to-br from-yellow-400 to-orange-500">
                        <i class="fas fa-hand-holding text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card relative" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#givenTodayModal">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Given Today</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">{{ $givenToday }}</p>
                    </div>
                    <div class="stats-icon bg-gradient-to-br from-indigo-400 to-indigo-600">
                        <i class="fas fa-plus text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card relative" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#returnedTodayModal">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Returned Today</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2">{{ $returnedToday }}</p>
                    </div>
                    <div class="stats-icon bg-gradient-to-br from-purple-400 to-purple-600">
                        <i class="fas fa-undo-alt text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8 mt-8">
            <!-- Monthly Activity Chart -->
            <div class="chart-card">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Monthly Activity (Last 6 Months)</h3>
                <div class="h-64">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <!-- Tire Type Distribution -->
            <div class="chart-card">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Tire Type Distribution</h3>
                <div class="h-64">
                    <canvas id="typeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Report Table: Recent Gives -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Recent Gives</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-cell font-semibold text-gray-700">Date</th>
                            <th class="table-cell font-semibold text-gray-700">Tire</th>
                            <th class="table-cell font-semibold text-gray-700">Serial</th>
                            <th class="table-cell font-semibold text-gray-700">Driver</th>
                            <th class="table-cell font-semibold text-gray-700">KM</th>
                            <th class="table-cell font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentGives as $give)
                        <tr class="table-row">
                            <td class="table-cell text-gray-600">{{ $give->give_date->format('Y-m-d') }}</td>
                            <td class="table-cell font-medium text-gray-800">{{ $give->tire->tire_type }}</td>
                            <td class="table-cell text-gray-600 font-mono">{{ $give->tire->serial_number }}</td>
                            <td class="table-cell text-gray-600">{{ $give->driver->name }}</td>
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
        </div>

        <!-- Report Table: Recent Returns -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Recent Returns</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-cell font-semibold text-gray-700">Date</th>
                            <th class="table-cell font-semibold text-gray-700">Tire</th>
                            <th class="table-cell font-semibold text-gray-700">Serial</th>
                            <th class="table-cell font-semibold text-gray-700">Driver</th>
                            <th class="table-cell font-semibold text-gray-700">Current KM</th>
                            <th class="table-cell font-semibold text-gray-700">Registered KM</th>
                            <th class="table-cell font-semibold text-gray-700">KM Diff</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentReturns as $ret)
                        <tr class="table-row">
                            <td class="table-cell text-gray-600">{{ $ret->return_date->format('Y-m-d') }}</td>
                            <td class="table-cell font-medium text-gray-800">{{ $ret->tire->tire_type }}</td>
                            <td class="table-cell text-gray-600 font-mono">{{ $ret->serial_number }}</td>
                            <td class="table-cell text-gray-600">{{ $ret->driver->name ?? 'N/A' }}</td>
                            <td class="table-cell text-gray-600">{{ $ret->current_kilometer }}</td>
                            <td class="table-cell text-gray-600">{{ $ret->registered_kilometer }}</td>
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
        </div>
    </div>
</div>

<!-- Total Tires Modal -->
<div class="modal fade" id="totalTiresModal" tabindex="-1" aria-labelledby="totalTiresModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-[#1C75BC] text-white">
                <h5 class="modal-title" id="totalTiresModalLabel">
                    <i class="fas fa-tire mr-2"></i> All Tires ({{ $totalTires }})
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="table-header">
                            <tr>
                                <th class="table-cell font-semibold text-gray-700">ID</th>
                                <th class="table-cell font-semibold text-gray-700">Tire Type</th>
                                <th class="table-cell font-semibold text-gray-700">Serial Number</th>
                                <th class="table-cell font-semibold text-gray-700">Plate Number</th>
                                <th class="table-cell font-semibold text-gray-700">Initial KM</th>
                                <th class="table-cell font-semibold text-gray-700">Status</th>
                                <th class="table-cell font-semibold text-gray-700">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allTires as $tire)
                            <tr class="table-row">
                                <td class="table-cell text-gray-600">{{ $tire->id }}</td>
                                <td class="table-cell font-medium text-gray-800">{{ $tire->tire_type }}</td>
                                <td class="table-cell text-gray-600 font-mono">{{ $tire->serial_number }}</td>
                                <td class="table-cell text-gray-600">{{ $tire->plate_number ?? '-' }}</td>
                                <td class="table-cell text-gray-600">{{ $tire->initial_kilometer }}</td>
                                <td class="table-cell">
                                    @if($tire->is_active)
                                        <span class="status-badge bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="status-badge bg-gray-100 text-gray-800">Inactive</span>
                                    @endif
                                </td>
                                <td class="table-cell text-gray-600">{{ $tire->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Active Tires Modal -->
<div class="modal fade" id="activeTiresModal" tabindex="-1" aria-labelledby="activeTiresModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-green-600 text-white">
                <h5 class="modal-title" id="activeTiresModalLabel">
                    <i class="fas fa-check-circle mr-2"></i> Active Tires ({{ $activeTires }})
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="table-header">
                            <tr>
                                <th class="table-cell font-semibold text-gray-700">ID</th>
                                <th class="table-cell font-semibold text-gray-700">Tire Type</th>
                                <th class="table-cell font-semibold text-gray-700">Serial Number</th>
                                <th class="table-cell font-semibold text-gray-700">Plate Number</th>
                                <th class="table-cell font-semibold text-gray-700">Initial KM</th>
                                <th class="table-cell font-semibold text-gray-700">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeTireList as $tire)
                            <tr class="table-row">
                                <td class="table-cell text-gray-600">{{ $tire->id }}</td>
                                <td class="table-cell font-medium text-gray-800">{{ $tire->tire_type }}</td>
                                <td class="table-cell text-gray-600 font-mono">{{ $tire->serial_number }}</td>
                                <td class="table-cell text-gray-600">{{ $tire->plate_number ?? '-' }}</td>
                                <td class="table-cell text-gray-600">{{ $tire->initial_kilometer }}</td>
                                <td class="table-cell text-gray-600">{{ $tire->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Given Out Modal -->
<div class="modal fade" id="givenOutModal" tabindex="-1" aria-labelledby="givenOutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-orange-600 text-white">
                <h5 class="modal-title" id="givenOutModalLabel">
                    <i class="fas fa-hand-holding mr-2"></i> Given Out Tires ({{ $givenOutTires }})
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="table-header">
                            <tr>
                                <th class="table-cell font-semibold text-gray-700">Date</th>
                                <th class="table-cell font-semibold text-gray-700">Tire Type</th>
                                <th class="table-cell font-semibold text-gray-700">Serial</th>
                                <th class="table-cell font-semibold text-gray-700">Driver</th>
                                <th class="table-cell font-semibold text-gray-700">KM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($givenOutTiresList as $give)
                            <tr class="table-row">
                                <td class="table-cell text-gray-600">{{ $give->give_date->format('Y-m-d') }}</td>
                                <td class="table-cell font-medium text-gray-800">{{ $give->tire->tire_type }}</td>
                                <td class="table-cell text-gray-600 font-mono">{{ $give->tire->serial_number }}</td>
                                <td class="table-cell text-gray-600">{{ $give->driver->name }}</td>
                                <td class="table-cell text-gray-600">{{ $give->given_kilometer }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Given Today Modal -->
<div class="modal fade" id="givenTodayModal" tabindex="-1" aria-labelledby="givenTodayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-indigo-600 text-white">
                <h5 class="modal-title" id="givenTodayModalLabel">
                    <i class="fas fa-plus mr-2"></i> Given Today ({{ $givenToday }})
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="table-header">
                            <tr>
                                <th class="table-cell font-semibold text-gray-700">Time</th>
                                <th class="table-cell font-semibold text-gray-700">Tire Type</th>
                                <th class="table-cell font-semibold text-gray-700">Serial</th>
                                <th class="table-cell font-semibold text-gray-700">Driver</th>
                                <th class="table-cell font-semibold text-gray-700">KM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($givenTodayList as $give)
                            <tr class="table-row">
                                <td class="table-cell text-gray-600">{{ $give->give_date->format('h:i A') }}</td>
                                <td class="table-cell font-medium text-gray-800">{{ $give->tire->tire_type }}</td>
                                <td class="table-cell text-gray-600 font-mono">{{ $give->tire->serial_number }}</td>
                                <td class="table-cell text-gray-600">{{ $give->driver->name }}</td>
                                <td class="table-cell text-gray-600">{{ $give->given_kilometer }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Returned Today Modal -->
<div class="modal fade" id="returnedTodayModal" tabindex="-1" aria-labelledby="returnedTodayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-purple-600 text-white">
                <h5 class="modal-title" id="returnedTodayModalLabel">
                    <i class="fas fa-undo-alt mr-2"></i> Returned Today ({{ $returnedToday }})
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="table-header">
                            <tr>
                                <th class="table-cell font-semibold text-gray-700">Time</th>
                                <th class="table-cell font-semibold text-gray-700">Tire Type</th>
                                <th class="table-cell font-semibold text-gray-700">Serial</th>
                                <th class="table-cell font-semibold text-gray-700">Driver</th>
                                <th class="table-cell font-semibold text-gray-700">Current KM</th>
                                <th class="table-cell font-semibold text-gray-700">KM Diff</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($returnedTodayList as $ret)
                            <tr class="table-row">
                                <td class="table-cell text-gray-600">{{ $ret->return_date->format('h:i A') }}</td>
                                <td class="table-cell font-medium text-gray-800">{{ $ret->tire->tire_type }}</td>
                                <td class="table-cell text-gray-600 font-mono">{{ $ret->serial_number }}</td>
                                <td class="table-cell text-gray-600">{{ $ret->driver->name ?? 'N/A' }}</td>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-[#1C75BC] text-white">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="fas fa-file-export mr-2"></i> Export Options
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-gray-600 mb-4">Select an export format:</p>
                <div class="space-y-3">
                    <a href="{{ route('tires.reports.export.excel') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors border border-gray-200">
                        <i class="fas fa-file-excel text-green-600 mr-3 text-xl"></i>
                        <div>
                            <h4 class="font-medium">Export Tires Excel</h4>
                            <p class="text-sm text-gray-500">Download tire inventory data as Excel</p>
                        </div>
                    </a>
                    <a href="{{ route('tires.reports.export.drivers') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors border border-gray-200">
                        <i class="fas fa-file-excel text-blue-600 mr-3 text-xl"></i>
                        <div>
                            <h4 class="font-medium">Export Drivers Excel</h4>
                            <p class="text-sm text-gray-500">Download driver data as Excel</p>
                        </div>
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- More Modal -->
<div class="modal fade" id="moreModal" tabindex="-1" aria-labelledby="moreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gray-700 text-white">
                <h5 class="modal-title" id="moreModalLabel">
                    <i class="fas fa-ellipsis-v mr-2"></i> Additional Actions
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="space-y-3">
                    <a href="{{ route('tires.audit') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors border border-gray-200">
                        <i class="fas fa-history text-purple-600 mr-3 text-xl"></i>
                        <div>
                            <h4 class="font-medium">Audit Trail</h4>
                            <p class="text-sm text-gray-500">View tire operation history</p>
                        </div>
                    </a>
                    <a href="{{ route('tires.dashboard') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors border border-gray-200">
                        <i class="fas fa-tachometer-alt text-blue-600 mr-3 text-xl"></i>
                        <div>
                            <h4 class="font-medium">Dashboard</h4>
                            <p class="text-sm text-gray-500">View tire tracking dashboard</p>
                        </div>
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Activity Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: @json($monthlyData->pluck('month')),
            datasets: [
                {
                    label: 'Tires Given',
                    data: @json($monthlyData->pluck('gives')),
                    backgroundColor: 'rgba(28, 117, 188, 0.8)',
                    borderColor: '#1C75BC',
                    borderWidth: 1
                },
                {
                    label: 'Tires Returned',
                    data: @json($monthlyData->pluck('returns')),
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: '#10B981',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    // Tire Type Distribution Chart
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: @json($tireTypeDistribution->pluck('tire_type')),
            datasets: [{
                data: @json($tireTypeDistribution->pluck('count')),
                backgroundColor: ['#1C75BC', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444', '#3B82F6', '#6366F1'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugin: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
@endsection
