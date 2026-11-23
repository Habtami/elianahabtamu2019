@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tire Tracking Dashboard</h1>
        <span class="text-muted">{{ now()->format('l, F j, Y') }}</span>
    </div>

    <div class="row row-cols-1 row-cols-md-4 g-3 mb-4">
        <div class="col">
            <div class="card border-primary h-100">
                <div class="card-body text-center">
                    <i class="fas fa-truck fa-2x text-primary mb-2"></i>
                    <h5 class="card-title">{{ $stats['total_tires'] }}</h5>
                    <p class="card-text text-muted small">Total Tires</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-info h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-friends fa-2x text-info mb-2"></i>
                    <h5 class="card-title">{{ $stats['total_drivers'] }}</h5>
                    <p class="card-text text-muted small">Active Drivers</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-warning h-100">
                <div class="card-body text-center">
                    <i class="fas fa-sign-out-alt fa-2x text-warning mb-2"></i>
                    <h5 class="card-title">{{ $stats['tires_in_use'] }}</h5>
                    <p class="card-text text-muted small">Tires In Use</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-success h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                    <h5 class="card-title">{{ $stats['km_difference'] }} km</h5>
                    <p class="card-text text-muted small">Total KM Difference</p>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->canPerformTireOperations())
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tires.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Add New Tire
                        </a>
                        <a href="{{ route('drivers.create') }}" class="btn btn-outline-info">
                            <i class="fas fa-user-plus me-2"></i>Add New Driver
                        </a>
                        <a href="{{ route('tires.give') }}" class="btn btn-outline-warning">
                            <i class="fas fa-sign-out-alt me-2"></i>Give Tire to Driver
                        </a>
                        <a href="{{ route('tires.index', ['status' => 'given_out']) }}" class="btn btn-outline-success">
                            <i class="fas fa-sign-in-alt me-2"></i>Return Tire from Driver
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($stats['recent_gives'] as $give)
                        <div class="list-group-item py-2">
                            <i class="fas fa-truck text-warning me-2"></i>
                            <small>Tire {{ $give->tire->serial_number }} given to {{ $give->driver->name }} ({{ $give->give_date->format('Y-m-d') }})</small>
                        </div>
                        @empty
                        <div class="list-group-item py-2">
                            <small class="text-muted">No recent activities</small>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="gave-tab" data-bs-toggle="tab" data-bs-target="#gave" type="button" role="tab">
                                Gave This Week
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned" type="button" role="tab">
                                Returned This Week
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="weekly-tab-content">
                        <div class="tab-pane fade show active" id="gave" role="tabpanel">
                            @if($stats['gave_this_week']->isNotEmpty())
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tire</th>
                                        <th>Driver</th>
                                        <th>KM Given</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['gave_this_week'] as $give)
                                    <tr>
                                        <td>{{ $give->tire->serial_number }}</td>
                                        <td>{{ $give->driver->name }}</td>
                                        <td>{{ $give->given_kilometer }}</td>
                                        <td>{{ $give->give_date->format('Y-m-d') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <p class="text-muted">No tire gives this week.</p>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="returned" role="tabpanel">
                            @if($stats['returned_this_week']->isNotEmpty())
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tire</th>
                                        <th>Driver</th>
                                        <th>KM Returned</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['returned_this_week'] as $return)
                                    <tr>
                                        <td>{{ $return->serial_number }}</td>
                                        <td>{{ $return->driver->name }}</td>
                                        <td>{{ $return->current_kilometer }}</td>
                                        <td>{{ $return->return_date->format('Y-m-d') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <p class="text-muted">No tire returns this week.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Charts</h5>
                </div>
                <div class="card-body">
                    @include('tires._charts', ['stats' => $stats])
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Upcoming License Expirations ({{ now()->addDays(30)->format('Y-m-d') }})</h5>
        </div>
        <div class="card-body">
            @if($stats['expiring_licenses']->isNotEmpty())
            <table class="table table-sm table-warning">
                <thead>
                    <tr>
                        <th>Driver</th>
                        <th>License</th>
                        <th>Expiry Date</th>
                        <th>Days Left</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['expiring_licenses'] as $driver)
                    <tr>
                        <td>{{ $driver->name }}</td>
                        <td>{{ $driver->license_number ?? 'N/A' }}</td>
                        <td>{{ $driver->license_expiry->format('Y-m-d') }}</td>
                        <td>{{ now()->diffInDays($driver->license_expiry) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted">No upcoming expirations in the next 30 days.</p>
            @endif
        </div>
    </div>
</div>
@endsection
