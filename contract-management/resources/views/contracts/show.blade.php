@extends('layouts.app')

@section('title', 'View Contract - ' . $contract->name)

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2>{{ $contract->name }}</h2>
        <div style="display: flex; gap: 0.5rem;">
            @if($contract->canUserEdit(auth()->user()))
            <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-secondary">Edit</a>
            @endif
            @if($contract->canUserDelete(auth()->user()))
            <form action="{{ route('contracts.destroy', $contract) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this contract?');" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
            @endif
        </div>
    </div>

    @if($contract->contract_alert || $contract->pg_alert)
        <div class="mb-4">
            @if($contract->contract_alert)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle"></i> Contract Alert:</strong> This contract is expiring in {{ $contract->days_until_end }} days ({{ $contract->end_date->format('Y-m-d') }})!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($contract->pg_alert)
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-circle"></i> Performance Guarantee Alert:</strong> Performance Guarantee is expiring in {{ $contract->days_until_pg_end }} days ({{ $contract->performance_guarantee_end_date->format('Y-m-d') }})!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0"><i class="fas fa-file-contract"></i> Contract Details</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Contract ID:</strong></div>
                        <div class="col-sm-8">{{ $contract->id }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Name:</strong></div>
                        <div class="col-sm-8">{{ $contract->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Created By:</strong></div>
                        <div class="col-sm-8">
                            @if($contract->creator)
                                {{ $contract->creator->name }}
                                <small class="text-muted d-block">{{ $contract->creator->getRoleDisplayName() }}@if($contract->creator->department) ({{ ucfirst(str_replace('_', ' ', $contract->creator->department)) }})@endif</small>
                                <small class="text-muted">Created on {{ $contract->created_at->format('M d, Y \a\t H:i') }}</small>
                            @else
                                <span class="text-muted">System / Unknown</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Start Date:</strong></div>
                        <div class="col-sm-8">{{ $contract->start_date->format('M d, Y') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>End Date:</strong></div>
                        <div class="col-sm-8">
                            {{ $contract->end_date->format('M d, Y') }}
                            @if($contract->days_until_end !== null)
                                @if($contract->days_until_end > 0)
                                    <span class="badge bg-info ms-2">{{ $contract->days_until_end }} days remaining</span>
                                @else
                                    <span class="badge bg-danger ms-2">Expired</span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Alert Days:</strong></div>
                        <div class="col-sm-8">{{ $contract->alert_days }} days</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Extensions:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-secondary">{{ $contract->extended_count ?? 0 }} times</span>
                            @if(($contract->extended_count ?? 0) == 0)
                                <small class="text-muted d-block">(No extensions yet)</small>
                            @endif
                        </div>
                    </div>

                    @if($contract->canUserEdit(auth()->user()))
                    <div class="text-center">
                        <a href="{{ route('contracts.extend-contract-end', $contract) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-calendar-plus"></i> Extend Contract End Date
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h4 class="card-title mb-0"><i class="fas fa-shield-alt"></i> Performance Guarantee</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Amount:</strong></div>
                        <div class="col-sm-8">
                            @if($contract->performance_guarantee_amount)
                                ${{ number_format($contract->performance_guarantee_amount, 2) }}
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>End Date:</strong></div>
                        <div class="col-sm-8">
                            @if($contract->performance_guarantee_end_date)
                                {{ $contract->performance_guarantee_end_date->format('M d, Y') }}
                                @if($contract->days_until_pg_end !== null)
                                    @if($contract->days_until_pg_end > 0)
                                        <span class="badge bg-info ms-2">{{ $contract->days_until_pg_end }} days remaining</span>
                                    @else
                                        <span class="badge bg-danger ms-2">Expired</span>
                                    @endif
                                @endif
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Alert Days:</strong></div>
                        <div class="col-sm-8">{{ $contract->performance_guarantee_alert_days }} days</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Extensions:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-secondary">0 times</span>
                            <small class="text-muted d-block">(No extensions yet)</small>
                        </div>
                    </div>

                    @if($contract->canUserEdit(auth()->user()) && $contract->performance_guarantee_end_date)
                    <div class="text-center">
                        <a href="{{ route('contracts.extend-performance-guarantee', $contract) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-calendar-plus"></i> Extend Performance Guarantee
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($contract->histories->count() > 0)
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <h4 class="card-title mb-0"><i class="fas fa-history"></i> Edit History</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><i class="fas fa-calendar"></i> Date & Time</th>
                        <th><i class="fas fa-tag"></i> Field</th>
                        <th><i class="fas fa-minus-circle"></i> Old Value</th>
                        <th><i class="fas fa-plus-circle"></i> New Value</th>
                        <th><i class="fas fa-user"></i> Edited By</th>
                        <th><i class="fas fa-comment"></i> Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contract->histories as $history)
                    <tr>
                        <td>{{ $history->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            @if($history->field_name == 'contract_end_date')
                                <span class="badge bg-primary">Contract End Date</span>
                            @elseif($history->field_name == 'performance_guarantee')
                                <span class="badge bg-warning text-dark">Performance Guarantee</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $history->field_name)) }}</span>
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
    </div>
</div>
@endif

<div class="mt-4 text-center">
    <a href="{{ route('contracts.index') }}" class="btn btn-secondary btn-lg">
        <i class="fas fa-arrow-left"></i> Back to Contracts List
    </a>
</div>
@endsection
