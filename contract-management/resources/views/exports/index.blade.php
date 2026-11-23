@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Export Logs</h1>
        <a href="{{ route('tires.dashboard') }}" class="btn btn-secondary btn-sm">Back to Dashboard</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>Format</th>
                            <th>Records</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>#{{ $log->id }}</td>
                            <td>{{ $log->user->name ?? 'System' }}</td>
                            <td>{{ $log->export_type_display_name }}</td>
                            <td>{{ $log->format_display_name }}</td>
                            <td>{{ $log->records_count }}</td>
                            <td>
                                <span class="badge bg-{{ $log->status == 'completed' ? 'success' : ($log->status == 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No export logs found</td>
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
