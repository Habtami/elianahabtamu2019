@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Telegram Logs</h1>
        <button type="button" class="table-tip-btn" data-table-tip aria-expanded="false" aria-controls="tipTelegramLogs"><i class="fas fa-info-circle"></i> TIP: info</button>
    </div>

    <div id="tipTelegramLogs" class="table-tip-panel no-print">
        <h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4>
        <ul>
            <li>Each row records one Telegram message with its send date, chat ID, message text, type and status.</li>
            <li>Message text is shortened to 80 characters so long notifications keep the row compact.</li>
            <li>Type shows as a grey badge; Status is green when sent, red when failed, amber for anything else.</li>
            <li>There is no search or paging here: the log arrives as one list, and empty results say "No logs found".</li>
        </ul>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Chat ID</th>
                            <th>Message</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $log->chat_id }}</td>
                            <td>{{ Str::limit($log->message, 80) }}</td>
                            <td><span class="badge bg-secondary">{{ $log->type }}</span></td>
                            <td>
                                <span class="badge bg-{{ $log->status == 'sent' ? 'success' : ($log->status == 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No logs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
