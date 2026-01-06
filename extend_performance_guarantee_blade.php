@extends('layouts.app')

@section('title', 'Extend Performance Guarantee - ' . $contract->name)

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Extend Performance Guarantee</h2>
    </div>

    <div class="alert alert-info">
        <strong>Contract:</strong> {{ $contract->name }}<br>
        <strong>Current PG Amount:</strong> {{ $contract->performance_guarantee_amount ? number_format($contract->performance_guarantee_amount, 2) : 'Not set' }}<br>
        <strong>Current PG End Date:</strong> {{ $contract->performance_guarantee_end_date ? $contract->performance_guarantee_end_date->format('Y-m-d') : 'Not set' }}<br>
        <strong>Previous Extensions:</strong> {{ $contract->performance_guarantee_edit_count }} times
    </div>

    <form action="{{ route('contracts.update-performance-guarantee', $contract) }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="new_amount">New Amount <span class="text-danger">*</span></label>
                <input type="number" name="new_amount" id="new_amount" class="form-control" value="{{ old('new_amount', $contract->performance_guarantee_amount) }}" step="0.01" min="0" required>
                @error('new_amount')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="new_end_date">New End Date <span class="text-danger">*</span></label>
                <input type="date" name="new_end_date" id="new_end_date" class="form-control" value="{{ old('new_end_date', $contract->performance_guarantee_end_date?->format('Y-m-d')) }}" required>
                @error('new_end_date')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="reason">Reason for Extension/Update <span class="text-danger">*</span></label>
            <textarea name="reason" id="reason" class="form-control" rows="4" required>{{ old('reason') }}</textarea>
            <small class="text-muted">Please provide a detailed reason for this extension or update</small>
            @error('reason')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="edited_by">Extended By</label>
            <input type="text" name="edited_by" id="edited_by" class="form-control" value="{{ old('edited_by', auth()->user()->name ?? '') }}" placeholder="Your name">
            <small class="text-muted">Optional: Name of the person performing this extension</small>
            @error('edited_by')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex" style="gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-warning">Update Performance Guarantee</button>
            <a href="{{ route('contracts.show', $contract) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@if($contract->performanceGuaranteeHistories->count() > 0)
<div class="card">
    <div class="card-header">
        <h2>Previous Extensions/Updates History</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Old Values</th>
                <th>New Values</th>
                <th>Extended By</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contract->performanceGuaranteeHistories as $history)
            <tr>
                <td>{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
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
@endsection
