@extends('layouts.app')

@section('title', 'All Contracts - Contract Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2>All Contracts</h2>
        <a href="{{ route('contracts.create') }}" class="btn btn-primary">Create New Contract</a>
    </div>

    <div class="mb-3">
        <form action="{{ route('contracts.index') }}" method="GET" class="d-flex" style="gap: 1rem;">
            <input type="text" name="search" class="form-control" placeholder="Search by contract name..." value="{{ request('search') }}" style="max-width: 300px;">
            <select name="alert_type" class="form-control" style="max-width: 200px;">
                <option value="">All Contracts</option>
                <option value="contract_end" {{ request('alert_type') == 'contract_end' ? 'selected' : '' }}>Expiring Contracts</option>
                <option value="performance_guarantee" {{ request('alert_type') == 'performance_guarantee' ? 'selected' : '' }}>Expiring PG</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request('search') || request('alert_type'))
                <a href="{{ route('contracts.index') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>

    @if($contracts->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Contract Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>PG Amount</th>
                    <th>PG End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contracts as $contract)
                <tr>
                    <td>{{ $contract->id }}</td>
                    <td><strong>{{ $contract->name }}</strong></td>
                    <td>{{ $contract->start_date->format('Y-m-d') }}</td>
                    <td>
                        {{ $contract->end_date->format('Y-m-d') }}
                        @if($contract->contract_end_date_edit_count > 0)
                            <span class="badge badge-info">Extended {{ $contract->contract_end_date_edit_count }}x</span>
                        @endif
                    </td>
                    <td>{{ $contract->performance_guarantee_amount ? number_format($contract->performance_guarantee_amount, 2) : 'N/A' }}</td>
                    <td>
                        {{ $contract->performance_guarantee_end_date ? $contract->performance_guarantee_end_date->format('Y-m-d') : 'N/A' }}
                        @if($contract->performance_guarantee_edit_count > 0)
                            <span class="badge badge-info">Extended {{ $contract->performance_guarantee_edit_count }}x</span>
                        @endif
                    </td>
                    <td>
                        @if($contract->contract_alert)
                            <span class="badge badge-danger">Contract Expiring</span>
                        @endif
                        @if($contract->pg_alert)
                            <span class="badge badge-warning">PG Expiring</span>
                        @endif
                        @if(!$contract->contract_alert && !$contract->pg_alert)
                            <span class="badge badge-success">Active</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-primary">View</a>
                        <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-secondary">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $contracts->links() }}
        </div>
    @else
        <p class="text-muted">No contracts found. <a href="{{ route('contracts.create') }}">Create your first contract</a>.</p>
    @endif
</div>
@endsection
