@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Audit Trail</h1>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="table-tip-btn" data-table-tip aria-expanded="false" aria-controls="tipTireAudit"><i class="fas fa-info-circle"></i> TIP: info</button>
            <a href="{{ route('tires.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div id="tipTireAudit" class="table-tip-panel no-print">
        <h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4>
        <ul>
            <li>Each row is one logged change: timestamp, user (or System), action badge, subject type, description and properties.</li>
            <li>Action badges are colour-coded: Created green, Updated amber, Deleted red; Type names the affected model, such as Tire.</li>
            <li>Type filters Tires, Drivers, Tire Gives and Tire Returns; Action filters Created, Updated and Deleted.</li>
            <li>Date from and date to limit the trail; press Filter to apply, filters do not submit on their own.</li>
            <li>Properties shows a shortened JSON excerpt (100 characters); pagination links appear when more records exist.</li>
        </ul>
    </div>

    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="type" class="form-select">
                <option value="">All Types</option>
                <option value="tire" {{ request('type') == 'tire' ? 'selected' : '' }}>Tires</option>
                <option value="driver" {{ request('type') == 'driver' ? 'selected' : '' }}>Drivers</option>
                <option value="give" {{ request('type') == 'give' ? 'selected' : '' }}>Tire Gives</option>
                <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Tire Returns</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="action" class="form-select">
                <option value="">All Actions</option>
                <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Properties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $log->causer->name ?? 'System' }}</td>
                            <td>
                                @php
                                    $badgeClass = [
                                        'created' => 'success',
                                        'updated' => 'warning',
                                        'deleted' => 'danger',
                                    ][$log->action_name ?? 'created'] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($log->action_name ?? 'created') }}</span>
                            </td>
                            <td>{{ class_basename($log->subject_type ?? '') }}</td>
                            <td>{{ $log->description ?? '' }}</td>
                            <td>
                                @if($log->properties)
                                <small class="text-muted">{{ Str::limit(json_encode($log->properties), 100) }}</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No audit logs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
