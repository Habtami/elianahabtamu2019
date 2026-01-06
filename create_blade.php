@extends('layouts.app')

@section('title', 'Create Contract - Contract Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Create New Contract</h2>
    </div>

    <form action="{{ route('contracts.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Contract Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="start_date">Start Date <span class="text-danger">*</span></label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}" required>
                @error('start_date')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="end_date">End Date <span class="text-danger">*</span></label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}" required>
                @error('end_date')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="alert_days_before">Alert Days Before Contract End <span class="text-danger">*</span></label>
            <input type="number" name="alert_days_before" id="alert_days_before" class="form-control" value="{{ old('alert_days_before', 30) }}" min="1" max="365" required>
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
                <input type="number" name="performance_guarantee_amount" id="performance_guarantee_amount" class="form-control" value="{{ old('performance_guarantee_amount') }}" step="0.01" min="0">
                @error('performance_guarantee_amount')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="performance_guarantee_end_date">Performance Guarantee End Date</label>
                <input type="date" name="performance_guarantee_end_date" id="performance_guarantee_end_date" class="form-control" value="{{ old('performance_guarantee_end_date') }}">
                @error('performance_guarantee_end_date')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="performance_guarantee_alert_days">Alert Days Before Performance Guarantee End</label>
            <input type="number" name="performance_guarantee_alert_days" id="performance_guarantee_alert_days" class="form-control" value="{{ old('performance_guarantee_alert_days', 30) }}" min="1" max="365">
            <small class="text-muted">Number of days before performance guarantee end date to trigger alert</small>
            @error('performance_guarantee_alert_days')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex" style="gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-success">Create Contract</button>
            <a href="{{ route('contracts.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@section('scripts')
<script>
    // Auto-calculate alert dates when dates are selected
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = new Date(this.value);
        if (!document.getElementById('end_date').value) {
            const endDate = new Date(startDate);
            endDate.setFullYear(endDate.getFullYear() + 1);
            document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
        }
    });

    // Validate end date is after start date
    document.getElementById('end_date').addEventListener('change', function() {
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(this.value);
        if (endDate <= startDate) {
            alert('End date must be after start date');
            this.value = '';
        }
    });
</script>
@endsection
@endsection
