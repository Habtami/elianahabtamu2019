@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Export Logs</h1>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="table-tip-btn" data-table-tip aria-expanded="false" aria-controls="tipExportLogs"><i class="fas fa-info-circle"></i> TIP: info</button>
            <a href="{{ route('tires.dashboard') }}" class="btn btn-secondary btn-sm">Back to Dashboard</a>
        </div>
    </div>

    <div id="tipExportLogs" class="table-tip-panel no-print">
        <h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4>
        <ul>
            <li>Each row is one export run with its ID, user, export type, file format and record count.</li>
            <li>User shows the account that ran the export, or "System" when no user is attached.</li>
            <li>Status badges read Completed in green, Failed in red, and any other state in amber.</li>
            <li>Date shows the run time as Y-m-d H:i; with no records the table says "No export logs found".</li>
            <li>Page links appear in the card footer only when the log list spans more than one page.</li>
        </ul>
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
