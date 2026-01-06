@extends('layouts.app')

@section('title', 'Extend Contract End Date - ' . $contract->name)

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Extend Contract End Date</h2>
    </div>

    <div class="alert alert-info">
        <strong>Contract:</strong> {{ $contract->name }}<br>
        <strong>Current End Date:</strong> {{ $contract->end_date->format('Y-m-d') }}<br>
        <strong>Previous Extensions:</strong> {{ $contract->contract_end_date_edit_count }} times
    </div>

    <form action="{{ route('contracts.update-contract-end', $contract) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="new_end_date">New End Date <span class="text-danger">*</span></label>
            <input type="date" name="new_end_date" id="new_end_date" class="form-control" value="{{ old('new_end_date') }}" min="{{ $contract->end_date->format('Y-m-d') }}" required>
            <small class="text-muted">New end date must be after the current end date ({{ $contract->end_date->format('Y-m-d') }})</small>
            @error('new_end_date')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="reason">Reason for Extension <span class="text-danger">*</span></label>
            <textarea name="reason" id="reason" class="form-control" rows="4" required>{{ old('reason') }}</textarea>
            <small class="text-muted">Please provide a detailed reason for this extension</small>
            @error('reason')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Extended By</label>
            <div class="form-control bg-light">
                {{ auth()->user()->name }} ({{ auth()->user()->getDepartmentDisplayName() }})
            </div>
            <small class="text-muted">This extension will be recorded under your account</small>
        </div>

        <div class="d-flex" style="gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-warning">Extend Contract End Date</button>
            <a href="{{ route('contracts.show', $contract) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@if($contract->contractEndDateHistories->count() > 0)
<div class="card">
    <div class="card-header">
        <h2>Previous Extensions History</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Old End Date</th>
                <th>New End Date</th>
                <th>Extended By</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contract->contractEndDateHistories as $history)
            <tr>
                <td>{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ $history->old_value }}</td>
                <td>{{ $history->new_value }}</td>
                <td>{{ $history->edited_by ?? 'N/A' }}</td>
                <td>{{ $history->reason ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
