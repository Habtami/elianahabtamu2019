@extends('layouts.app')

@section('title', 'View Contract - ' . $contract->name)

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2>{{ $contract->name }}</h2>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-secondary">Edit</a>
            <form action="{{ route('contracts.destroy', $contract) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this contract?');" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>

    @if($contract->contract_alert || $contract->pg_alert)
        <div style="margin-bottom: 1rem;">
            @if($contract->contract_alert)
                <div class="alert alert-danger">
                    <strong>Alert:</strong> This contract is expiring in {{ $contract->days_until_end }} days ({{ $contract->end_date->format('Y-m-d') }})!
                </div>
            @endif

            @if($contract->pg_alert)
                <div class="alert alert-warning">
                    <strong>Alert:</strong> Performance Guarantee is expiring in {{ $contract->days_until_pg_end }} days ({{ $contract->performance_guarantee_end_date->format('Y-m-d') }})!
                </div>
            @endif
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div>
            <h3 style="margin-bottom: 1rem; color: #2c3e50;">Contract Details</h3>

            <div class="form-group">
                <label>Contract ID</label>
                <p><strong>{{ $contract->id }}</strong></p>
            </div>

            <div class="form-group">
                <label>Contract Name</label>
                <p><strong>{{ $contract->name }}</strong></p>
            </div>

            <div class="form-group">
                <label>Start Date</label>
                <p>{{ $contract->start_date->format('Y-m-d') }}</p>
            </div>

            <div class="form-group">
                <label>End Date</label>
                <p>
                    {{ $contract->end_date->format('Y-m-d') }}
                    @if($contract->days_until_end !== null)
                        @if($contract->days_until_end > 0)
                            <span class="badge badge-info">{{ $contract->days_until_end }} days remaining</span>
                        @else
                            <span class="badge badge-danger">Expired</span>
                        @endif
                    @endif
                </p>
            </div>

            <div class="form-group">
                <label>Alert Days Before End</label>
                <p>{{ $contract->alert_days_before }} days</p>
            </div>

            <div class="form-group">
                <label>Contract Extensions</label>
                <p>
                    <span class="badge badge-info">Extended {{ $contract->contract_end_date_edit_count }} times</span>
                    @if($contract->contract_end_date_edit_count == 0)
                        <span class="text-muted">(No extensions yet)</span>
                    @endif
                </p>
                <a href="{{ route('contracts.extend-contract-end', $contract) }}" class="btn btn-warning btn-sm">Extend Contract End Date</a>
            </div>
        </div>

        <div>
            <h3 style="margin-bottom: 1rem; color: #2c3e50;">Performance Guarantee</h3>

            <div class="form-group">
                <label>Amount</label>
                <p>
                    @if($contract->performance_guarantee_amount)
                        <strong>{{ number_format($contract->performance_guarantee_amount, 2) }}</strong>
                    @else
                        <span class="text-muted">Not set</span>
                    @endif
                </p>
            </div>

            <div class="form-group">
                <label>End Date</label>
                <p>
                    @if($contract->performance_guarantee_end_date)
                        {{ $contract->performance_guarantee_end_date->format('Y-m-d') }}
                        @if($contract->days_until_pg_end !== null)
                            @if($contract->days_until_pg_end > 0)
                                <span class="badge badge-info">{{ $contract->days_until_pg_end }} days remaining</span>
                            @else
                                <span class="badge badge-danger">Expired</span>
                            @endif
                        @endif
                    @else
                        <span class="text-muted">Not set</span>
                    @endif
                </p>
            </div>

            <div class="form-group">
                <label>Alert Days Before End</label>
                <p>{{ $contract->performance_guarantee_alert_days }} days</p>
            </div>

            <div class="form-group">
                <label>Performance Guarantee Extensions</label>
                <p>
                    <span class="badge badge-info">Extended {{ $contract->performance_guarantee_edit_count }} times</span>
                    @if($contract->performance_guarantee_edit_count == 0)
                        <span class="text-muted">(No extensions yet)</span>
                    @endif
                </p>
                <a href="{{ route('contracts.extend-performance-guarantee', $contract) }}" class="btn btn-warning btn-sm">Extend Performance Guarantee</a>
            </div>
        </div>
    </div>
</div>

@if($contract->histories->count() > 0)
<div class="card">
    <div class="card-header">
        <h2>Edit History</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Field</th>
                <th>Old Value</th>
                <th>New Value</th>
                <th>Edited By</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contract->histories as $history)
            <tr>
                <td>{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
                <td>
                    @if($history->field_name == 'contract_end_date')
                        <span class="badge badge-info">Contract End Date</span>
                    @else
                        <span class="badge badge-warning">Performance Guarantee</span>
                    @endif
                </td>
                <td>{{ $history->getFormattedOldValue() }}</td>
                <td>{{ $history->getFormattedNewValue() }}</td>
                <td>{{ $history->edited_by ?? 'N/A' }}</td>
                <td>{{ $history->reason ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div style="margin-top: 1rem;">
    <a href="{{ route('contracts.index') }}" class="btn btn-secondary">Back to List</a>
</div>
@endsection
