@extends('layouts.app')

@section('title', 'Contract Alerts - Contract Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Contract Alerts Dashboard</h2>
    </div>

    @if(count($alertContracts) > 0)
        <div class="alert alert-warning">
            <strong>{{ count($alertContracts) }} contract(s)</strong> require your attention!
        </div>

        @foreach($alertContracts as $item)
            <div class="card" style="border-left: 4px solid #ffc107;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 style="margin: 0;">{{ $item['contract']->name }}</h3>
                    <a href="{{ route('contracts.show', $item['contract']) }}" class="btn btn-sm btn-primary">View Contract</a>
                </div>

                <div>
                    <p><strong>Contract ID:</strong> {{ $item['contract']->id }}</p>
                    <p><strong>Start Date:</strong> {{ $item['contract']->start_date->format('Y-m-d') }}</p>

                    <div style="margin-top: 1rem;">
                        <h4>Active Alerts:</h4>
                        @foreach($item['alerts'] as $alert)
                            @if($alert['type'] == 'contract_end')
                                <div class="alert alert-danger">
                                    <strong>Contract Expiration Alert:</strong><br>
                                    {{ $alert['message'] }}<br>
                                    <strong>End Date:</strong> {{ $item['contract']->end_date->format('Y-m-d') }}<br>
                                    <div style="margin-top: 0.5rem;">
                                        <a href="{{ route('contracts.extend-contract-end', $item['contract']) }}" class="btn btn-warning btn-sm">Extend Contract</a>
                                    </div>
                                </div>
                            @endif

                            @if($alert['type'] == 'performance_guarantee')
                                <div class="alert alert-warning">
                                    <strong>Performance Guarantee Expiration Alert:</strong><br>
                                    {{ $alert['message'] }}<br>
                                    <strong>PG End Date:</strong> {{ $item['contract']->performance_guarantee_end_date->format('Y-m-d') }}<br>
                                    <strong>PG Amount:</strong> {{ number_format($item['contract']->performance_guarantee_amount, 2) }}<br>
                                    <div style="margin-top: 0.5rem;">
                                        <a href="{{ route('contracts.extend-performance-guarantee', $item['contract']) }}" class="btn btn-warning btn-sm">Extend Performance Guarantee</a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-success">
            <strong>No alerts!</strong> All contracts are in good standing.
        </div>

        <p class="text-muted">
            When contracts or performance guarantees are approaching their end dates (based on the alert days configured for each contract), they will appear here.
        </p>
    @endif

    <div style="margin-top: 1rem;">
        <a href="{{ route('contracts.index') }}" class="btn btn-secondary">Back to All Contracts</a>
    </div>
</div>

@section('scripts')
<script>
    // Auto-refresh alerts every 60 seconds
    setTimeout(function() {
        location.reload();
    }, 60000);
</script>
@endsection
@endsection
