@extends('layouts.app')

@section('title', 'Extend Performance Guarantee - ' . $contract->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-calendar-plus me-2"></i>Extend Performance Guarantee
                    </h4>
                    <a href="{{ route('contracts.show', $contract) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Contract
                    </a>
                </div>

                <!-- Contract Summary -->
                <div class="card border-left-info border-3">
                    <div class="card-body">
                        <h5 class="card-title text-info mb-3">
                            <i class="fas fa-file-contract me-2"></i>Contract Summary
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong class="text-muted">Contract Name:</strong>
                                    <span class="ms-2">{{ $contract->name }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-muted">Current PG Amount:</strong>
                                    <span class="ms-2 fw-bold">
                                        {{ $contract->performance_guarantee_amount ? number_format($contract->performance_guarantee_amount, 2) : 'Not set' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong class="text-muted">Current PG End Date:</strong>
                                    <span class="ms-2 fw-bold">
                                        {{ $contract->performance_guarantee_end_date ? $contract->performance_guarantee_end_date->format('Y-m-d') : 'Not set' }}
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-muted">Previous Extensions:</strong>
                                    <span class="ms-2 badge bg-warning text-dark">
                                        {{ $contract->performance_guarantee_edit_count }} times
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Extension Form -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Update Performance Guarantee Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('contracts.update-performance-guarantee', $contract) }}" method="POST">
                            @csrf

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="new_amount" class="form-label fw-semibold">
                                            New Amount <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">$</span>
                                            <input type="number" 
                                                   name="new_amount" 
                                                   id="new_amount" 
                                                   class="form-control @error('new_amount') is-invalid @enderror" 
                                                   value="{{ old('new_amount', $contract->performance_guarantee_amount) }}" 
                                                   step="0.01" 
                                                   min="0" 
                                                   required>
                                        </div>
                                        @error('new_amount')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="new_end_date" class="form-label fw-semibold">
                                            New End Date <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-calendar-alt"></i>
                                            </span>
                                            <input type="date" 
                                                   name="new_end_date" 
                                                   id="new_end_date" 
                                                   class="form-control @error('new_end_date') is-invalid @enderror" 
                                                   value="{{ old('new_end_date', $contract->performance_guarantee_end_date?->format('Y-m-d')) }}" 
                                                   required>
                                        </div>
                                        @error('new_end_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label for="reason" class="form-label fw-semibold">
                                    Reason for Extension/Update <span class="text-danger">*</span>
                                </label>
                                <textarea name="reason" 
                                          id="reason" 
                                          class="form-control @error('reason') is-invalid @enderror" 
                                          rows="4" 
                                          required
                                          placeholder="Provide detailed justification for this extension...">{{ old('reason') }}</textarea>
                                <div class="form-text">Please provide a detailed reason for this extension or update</div>
                                @error('reason')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label fw-semibold">
                                    Extended By
                                </label>
                                <div class="form-control bg-light">
                                    {{ auth()->user()->name }} ({{ auth()->user()->getDepartmentDisplayName() }})
                                </div>
                                <div class="form-text">This extension will be recorded under your account</div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-top pt-4">
                                <div>
                                    <button type="submit" class="btn btn-warning px-4 py-2">
                                        <i class="fas fa-save me-2"></i>Update Performance Guarantee
                                    </button>
                                    <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-secondary px-4 py-2 ms-2">
                                        Cancel
                                    </a>
                                </div>
                                <div class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i> All fields marked with * are required
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- History Section -->
            @if($contract->performanceGuaranteeHistories->count() > 0)
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Previous Extensions/Updates History
                    </h5>
                    <span class="badge bg-secondary">
                        {{ $contract->performanceGuaranteeHistories->count() }} records
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-top-0">Date & Time</th>
                                    <th class="border-top-0">Old Values</th>
                                    <th class="border-top-0">New Values</th>
                                    <th class="border-top-0">Extended By</th>
                                    <th class="border-top-0">Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contract->performanceGuaranteeHistories as $history)
                                <tr>
                                    <td class="text-nowrap">
                                        <i class="far fa-clock me-1 text-muted"></i>
                                        {{ $history->created_at ? $history->created_at->format('Y-m-d H:i:s') : 'N/A' }}
                                    </td>
                                    <td>
                                        <div class="text-muted small">Amount/Date</div>
                                        {{ $history->getFormattedOldValue() }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold small">Amount/Date</div>
                                        {{ $history->getFormattedNewValue() }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $history->edited_by ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="small">
                                        {{ $history->reason ?? 'N/A' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection