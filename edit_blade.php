@extends('layouts.app')

@section('title', 'Edit Contract - ' . $contract->name)

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Edit Contract: {{ $contract->name }}</h2>
    </div>

    <form action="{{ route('contracts.update', $contract) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Contract Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $contract->name) }}" required>
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="start_date">Start Date <span class="text-danger">*</span></label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}" required>
                @error('start_date')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="end_date">End Date <span class="text-danger">*</span></label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $contract->end_date->format('Y-m-d')) }}" required>
                @error('end_date')
                    <div class="error">{{ $message }}</div>
                @enderror
                <small class="text-muted">Note: Use the "Extend Contract End Date" feature to track extensions properly</small>
            </div>
        </div>

        <div class="form-group">
            <label for="alert_days_before">Alert Days Before Contract End <span class="text-danger">*</span></label>
            <input type="number" name="alert_days_before" id="alert_days_before" class="form-control" value="{{ old('alert_days_before', $contract->alert_days_before) }}" min="1" max="365" required>
            <small class="text-muted">Number of days before contract end date to trigger alert</small>
            @error('alert_days_before')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <hr style="margin: 2rem 0;">

        <h3 style="margin-bottom: 1rem;">Performance Guarantee (Optional)</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="performance_guarantee_amount">Performance Guarantee Amount</label>
                <input type="number" name="performance_guarantee_amount" id="performance_guarantee_amount" class="form-control" value="{{ old('performance_guarantee_amount', $contract->performance_guarantee_amount) }}" step="0.01" min="0">
                @error('performance_guarantee_amount')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="performance_guarantee_end_date">Performance Guarantee End Date</label>
                <input type="date" name="performance_guarantee_end_date" id="performance_guarantee_end_date" class="form-control" value="{{ old('performance_guarantee_end_date', $contract->performance_guarantee_end_date?->format('Y-m-d')) }}">
                @error('performance_guarantee_end_date')
                    <div class="error">{{ $message }}</div>
                @enderror
                <small class="text-muted">Note: Use the "Extend Performance Guarantee" feature to track extensions properly</small>
            </div>
        </div>

        <div class="form-group">
            <label for="performance_guarantee_alert_days">Alert Days Before Performance Guarantee End</label>
            <input type="number" name="performance_guarantee_alert_days" id="performance_guarantee_alert_days" class="form-control" value="{{ old('performance_guarantee_alert_days', $contract->performance_guarantee_alert_days) }}" min="1" max="365">
            <small class="text-muted">Number of days before performance guarantee end date to trigger alert</small>
            @error('performance_guarantee_alert_days')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex" style="gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-success">Update Contract</button>
            <a href="{{ route('contracts.show', $contract) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
