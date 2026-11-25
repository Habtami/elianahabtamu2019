@extends('layouts.app')

@section('title', 'Database Backups')
@section('page-title', 'Database Backups')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<style>
    .countdown-container {
        background: linear-gradient(135deg, #1C75BC 0%, #0f4c81 100%);
        color: #fff;
        padding: 1.4rem 1.6rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 24px rgba(15, 76, 129, 0.25);
    }
    .countdown-timer {
        font-size: 2.4rem;
        font-weight: 700;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        line-height: 1.15;
    }
    .countdown-label { font-size: 0.9rem; opacity: 0.9; }
    .next-backup-time { font-size: 1rem; margin-top: 0.35rem; }
    .countdown-container .btn { white-space: nowrap; }
    .backup-status {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 600;
    }
    .status-waiting { background-color: #facc15; color: #1f2937; }
    .status-running { background-color: #38bdf8; color: #082f49; }

    #backupsTable { font-size: 11px; margin-bottom: 0; }
    #backupsTable thead th {
        font-size: 9px;
        font-weight: 600;
        text-transform: uppercase;
        white-space: nowrap;
        padding: 6px;
        vertical-align: middle;
    }
    #backupsTable tbody td { padding: 6px; white-space: nowrap; vertical-align: middle; }
    #backupsTable .cell-ellipsis {
        display: block;
        max-width: 230px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    #backupsTable .cell-ellipsis--emails { max-width: 200px; }
    #backupsTable .btn-group .btn { padding: 2px 7px; font-size: 11px; }
    #backupsTable .badge { font-size: 9px; font-weight: 500; }
    #backupsTable .backup-skipped-row > * { background-color: #fff8e1; }
    body.dark-theme #backupsTable .backup-skipped-row > * { background-color: #3b3524 !important; }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate { font-size: 12px; }
    body.dark-theme .dataTables_wrapper .dataTables_filter input,
    body.dark-theme .dataTables_wrapper .dataTables_length select {
        background: #16233a;
        border-color: #1e293b;
        color: #cbd5e1;
    }
    body.dark-theme .page-link { background: #16233a; border-color: #1e293b; color: #cbd5e1; }

    .activity-log-timeline { position: relative; padding-left: 30px; }
    .activity-log-timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
    .activity-log-item { position: relative; padding-bottom: 25px; }
    .activity-log-item::before {
        content: '';
        position: absolute;
        left: -26px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid #dee2e6;
        z-index: 1;
    }
    .activity-log-item.action-created::before { border-color: #28a745; }
    .activity-log-item.action-deleted::before { border-color: #dc3545; }
    .activity-log-card {
        background: #f8f9fa;
        border-left: 4px solid #dee2e6;
        padding: 10px 14px;
        border-radius: 4px;
    }
    .activity-log-card.action-created { border-left-color: #28a745; }
    .activity-log-card.action-deleted { border-left-color: #dc3545; }
    body.dark-theme .activity-log-card { background: #16233a; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Schedule countdown --}}
    <div class="countdown-container">
        <div class="row g-3 align-items-center">
            <div class="col-lg-6">
                <div class="countdown-label"><i class="fas fa-clock me-1"></i> Scheduled database backup</div>
                <div class="countdown-timer" id="countdownTimer">--:--:--</div>
                <div class="next-backup-time">
                    <span>Next backup: </span>
                    <span id="nextBackupTime">{{ $settings['backup_time'] ?? '00:00' }}</span>
                    <span class="opacity-75">(EAT)</span>
                </div>
                @if($nextLikelySkip)
                    <div class="small mt-1" id="nextBackupHint">
                        <i class="fas fa-circle-check me-1"></i>No database changes detected — the next scheduled run may be skipped.
                    </div>
                @endif
                <div class="mt-2">
                    <span class="backup-status status-waiting" id="backupStatus">Waiting for schedule</span>
                </div>
                <div class="mt-3 d-flex flex-wrap gap-2 no-print">
                    <button type="button" class="btn btn-warning btn-sm" id="testNowBtn" onclick="triggerTestBackup()">
                        <i class="fas fa-bolt me-1"></i>Test Now
                    </button>
                    <form action="{{ route('backup.skip-next') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $skipNext ? 'btn-danger' : 'btn-outline-light' }}">
                            <i class="fas fa-forward-step me-1"></i>{{ $skipNext ? 'Cancel Skip' : 'Skip Next Backup' }}
                        </button>
                    </form>
                </div>
                @if($skipNext)
                    <div class="small mt-2">
                        <i class="fas fa-circle-info me-1"></i>The next scheduled run will be skipped on purpose.
                    </div>
                @endif
            </div>
            <div class="col-lg-6">
                <div class="countdown-label"><i class="fas fa-hourglass-half me-1"></i> Current time</div>
                <div class="countdown-timer" id="currentTime" style="font-size: 1.8rem;">--:--:--</div>
                <div class="next-backup-time">
                    <small><i class="fas fa-server me-1"></i>Backups run on the server. The page does not need to be open.</small>
                </div>
                <div class="mt-2">
                    <small id="countdownInfo" class="opacity-75"></small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0"><i class="fas fa-hard-drive me-2"></i>Backups List</h5>
                    <form action="{{ route('backup.create') }}" method="POST" id="manualBackupForm">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm" id="backupButton">
                            <i class="fas fa-database me-1"></i>Create Manual Backup
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle" id="backupsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>File Name</th>
                                    <th>Size</th>
                                    <th>Date Created</th>
                                    <th>Created By</th>
                                    <th>Type</th>
                                    <th>Storage</th>
                                    <th>Email Status</th>
                                    <th>Email Recipients</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($backups as $index => $backup)
                                <tr class="{{ $backup['was_skipped'] ? 'backup-skipped-row' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($backup['was_skipped'])
                                            <span class="text-muted">—</span>
                                        @else
                                            <span class="cell-ellipsis" title="{{ $backup['filename'] }}">{{ $backup['filename'] }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $backup['size'] }}</td>
                                    <td data-order="{{ $backup['date'] }}">
                                        {{ $backup['date'] ? \Carbon\Carbon::parse($backup['date'])->format('d/m/Y H:i') : '—' }}
                                    </td>
                                    <td><small>{{ $backup['created_by'] ?? '-' }}</small></td>
                                    <td>
                                        <span class="badge bg-{{ $backup['type'] === 'manual' ? 'primary' : ($backup['type'] === 'scheduled' ? 'success' : 'secondary') }}">
                                            {{ $backup['type'] === 'manual' ? 'Manual' : ($backup['type'] === 'scheduled' ? 'Scheduled' : 'Unknown') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($backup['was_skipped'])
                                            <span class="badge bg-warning text-dark" title="{{ $backup['skip_reason'] }}">
                                                <i class="fas fa-forward-step me-1"></i>No changes
                                            </span>
                                        @elseif(in_array($backup['copy_status'], ['primary', 'copied'], true))
                                            <span class="badge bg-success" title="{{ $primaryPath }}">{{ $backup['copy_status'] === 'primary' ? 'Destination' : 'Copied' }}</span>
                                        @elseif($backup['copy_status'] === 'fallback')
                                            <span class="badge bg-warning text-dark" title="The configured folder was unavailable; the dump was kept in storage/app/backups">Fallback (local)</span>
                                        @elseif($backup['copy_status'] === 'copy_failed')
                                            <span class="badge bg-danger">Copy failed</span>
                                        @elseif($backup['copy_status'] === 'not_configured')
                                            <span class="badge bg-secondary" title="Backups are kept in storage/app/backups only">Storage only</span>
                                        @else
                                            <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($backup['was_skipped'])
                                            <span class="badge bg-secondary">Not sent (skipped)</span>
                                        @elseif($backup['email_status'] === 'sent')
                                            <span class="badge bg-success">Sent</span>
                                        @elseif($backup['email_status'] === 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @elseif($backup['email_status'] === 'no_recipients')
                                            <span class="badge bg-secondary">No recipients</span>
                                        @elseif($backup['email_status'] === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($backup['was_skipped'])
                                            <span class="text-muted">—</span>
                                        @elseif($backup['email_recipients'])
                                            <small class="text-muted cell-ellipsis cell-ellipsis--emails" title="{{ $backup['email_recipients'] }}">{{ $backup['email_recipients'] }}</small>
                                        @elseif(! empty($adminEmails))
                                            <small class="text-muted cell-ellipsis cell-ellipsis--emails" title="{{ implode(', ', $adminEmails) }}">{{ implode(', ', $adminEmails) }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($backup['was_skipped'])
                                            <span class="text-muted small" title="{{ $backup['skip_reason'] }}">
                                                <i class="fas fa-circle-info me-1"></i>{{ $backup['skip_reason'] }}
                                            </span>
                                        @else
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-info"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#backupHistoryModal{{ $index }}"
                                                        title="Backup history">
                                                    <i class="fas fa-history"></i>
                                                </button>
                                                <a href="{{ route('backup.download', $backup['filename']) }}" class="btn btn-info" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <form action="{{ route('backup.delete', $backup['filename']) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="fas fa-sliders me-2"></i>Backup Settings</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('backup.settings') }}" method="POST" id="settingsForm">
                        @csrf
                        <div class="mb-3">
                            <label for="backup_time" class="form-label">Backup time (24h, EAT)</label>
                            <input type="time" class="form-control" id="backup_time" name="backup_time" value="{{ $settings['backup_time'] ?? '00:00' }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="destination_path" class="form-label">Backup folder</label>
                            <input type="text" class="form-control" id="destination_path" name="destination_path"
                                   value="{{ $settings['destination_path'] ?? '' }}" placeholder="D:\a_contract_backup">
                            <div class="form-text">Dumps are stored in this folder. Leave empty to use <code>storage/app/backups</code>. Relative paths are created under the project folder.</div>
                        </div>
                        <div class="mb-3">
                            <label for="notification_email" class="form-label">Notification email</label>
                            <input type="email" class="form-control" id="notification_email" name="notification_email"
                                   value="{{ $settings['notification_email'] ?? '' }}" placeholder="email@example.com">
                            <div class="form-text">Active admins are notified automatically; this address is added on top.</div>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="smart_mode" name="smart_mode" value="1" @checked($smartMode)>
                            <label class="form-check-label fw-semibold" for="smart_mode">Smart Mode</label>
                            <div class="form-text">Skip a run when the database has not changed since the newest backup. Scheduled runs follow this switch; manual backups from this page check it too and report &ldquo;no changes&rdquo; instead of stacking identical files.</div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-save me-1"></i>Save Settings
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-fingerprint me-2"></i>Fingerprint Comparison</h6>
                    <span class="badge bg-{{ $smartMode ? 'success' : 'secondary' }}">Smart Mode: {{ $smartMode ? 'On' : 'Off' }}</span>
                </div>
                <div class="card-body small">
                    @if(! $lastBackup)
                        <p class="text-muted mb-2">No fingerprint backup yet. The next scheduled run will create one.</p>
                    @else
                        <div class="mb-1">
                            <strong>Last backup:</strong>
                            {{ $lastBackupAt?->format('d/m/Y H:i') }}
                        </div>
                        <div class="mb-1">
                            <strong>Last hash:</strong>
                            <code>{{ \Illuminate\Support\Str::limit($lastBackup->content_hash, 16, '…') }}</code>
                        </div>
                        <div class="mb-2">
                            <strong>Current hash:</strong>
                            <code>{{ $currentHash ? \Illuminate\Support\Str::limit($currentHash, 16, '…') : '—' }}</code>
                        </div>
                        @if($hashMatch === true)
                            <span class="badge bg-warning text-dark"><i class="fas fa-equals me-1"></i>Unchanged</span>
                        @elseif($hashMatch === false)
                            <span class="badge bg-success"><i class="fas fa-arrows-rotate me-1"></i>Changed</span>
                        @else
                            <span class="badge bg-secondary">Unknown</span>
                        @endif
                    @endif
                    <hr class="my-2">
                    <div><strong>Skipped this week:</strong> {{ $skippedThisWeek }}</div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="fas fa-folder-tree me-2"></i>Storage</h6>
                </div>
                <div class="card-body small">
                    <p class="mb-1">
                        <strong>Backup folder:</strong><br>
                        <code>{{ $primaryPath }}</code>
                    </p>
                    <p class="text-muted mb-2">
                        Fallback: <code>storage/app/backups</code> is used only when the folder above cannot be written.
                    </p>
                    <hr>
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Scheduled runs require <code>php artisan schedule:work</code> (or the Windows scheduled task) to be running on the server.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Backup history modals (kept outside the table so paging never hides them) --}}
@foreach($backups as $index => $backup)
    @if(! $backup['was_skipped'])
    @php $events = $histories->get($backup['filename'], collect()); @endphp
    <div class="modal fade" id="backupHistoryModal{{ $index }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-history me-2"></i>Backup History — {{ $backup['filename'] }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($events->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-clock-rotate-left fa-2x mb-2"></i>
                            <p class="mb-0">No history recorded for this file.</p>
                        </div>
                    @else
                        <div class="activity-log-timeline">
                            @foreach($events as $event)
                                @php
                                    $failed = in_array($event['action'], ['backup_failed', 'backup_deleted'], true);
                                    $values = $event['values'];
                                    $actionLabel = [
                                        'backup_created' => 'Backup created',
                                        'backup_failed' => 'Backup failed',
                                        'backup_deleted' => 'Backup deleted',
                                        'backup_skipped' => 'Backup skipped',
                                    ][$event['action']] ?? $event['action'];
                                @endphp
                                <div class="activity-log-item {{ $failed ? 'action-deleted' : 'action-created' }}">
                                    <div class="activity-log-card {{ $failed ? 'action-deleted' : 'action-created' }}">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <strong>
                                                <i class="fas {{ $failed ? 'fa-triangle-exclamation' : 'fa-database' }} me-1"></i>
                                                {{ $actionLabel }}
                                            </strong>
                                            <small class="text-muted text-nowrap">{{ $event['at']?->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-user me-1"></i>{{ $event['user'] }}
                                        </div>
                                        <div class="d-flex flex-wrap gap-1 mb-1">
                                            @if(! empty($values['type']))
                                                <span class="badge bg-secondary">{{ $values['type'] === 'scheduled' ? 'Scheduled' : 'Manual' }}</span>
                                            @endif
                                            @if(! empty($values['copy_status']))
                                                @php
                                                    $storageLabels = ['primary' => 'Destination', 'copied' => 'Copied', 'fallback' => 'Fallback (local)', 'not_configured' => 'Storage only', 'copy_failed' => 'Copy failed'];
                                                    $storageColor = match ($values['copy_status']) {
                                                        'primary', 'copied' => 'success',
                                                        'fallback' => 'warning text-dark',
                                                        'not_configured' => 'secondary',
                                                        default => 'danger',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $storageColor }}">{{ $storageLabels[$values['copy_status']] ?? ucfirst(str_replace('_', ' ', $values['copy_status'])) }}</span>
                                            @endif
                                            @if(! empty($values['email_status']) || ! empty($values['email_sent']))
                                                @php $emailState = $values['email_status'] ?? $values['email_sent']; @endphp
                                                <span class="badge bg-{{ $emailState === 'sent' ? 'success' : ($emailState === 'pending' ? 'warning text-dark' : 'danger') }}">
                                                    {{ $emailState === 'sent' ? 'Email sent' : ($emailState === 'pending' ? 'Email pending' : ucfirst(str_replace('_', ' ', $emailState))) }}
                                                </span>
                                            @endif
                                        </div>
                                        @if(! empty($values['email_recipients']))
                                            <div class="small text-muted">
                                                <i class="fas fa-envelope me-1"></i>{{ $values['email_recipients'] }}
                                            </div>
                                        @endif
                                        @if(! empty($values['destination_path']))
                                            <div class="small text-muted">
                                                <i class="fas fa-folder me-1"></i><code>{{ $values['destination_path'] }}</code>
                                            </div>
                                        @endif
                                        @if(! empty($values['size']))
                                            <div class="small text-muted">
                                                <i class="fas fa-weight-hanging me-1"></i>{{ $values['size'] }}
                                            </div>
                                        @endif
                                        @if(! empty($values['error']))
                                            <pre class="small text-danger mb-0 mt-1" style="white-space: pre-wrap;">{{ $values['error'] }}</pre>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    $('#backupsTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        pagingType: 'simple_numbers',
        autoWidth: false,
        order: [[3, 'desc']],
        columnDefs: [
            { orderable: false, targets: -1 }
        ],
        language: {
            search: 'Search:',
            lengthMenu: '_MENU_',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'No entries',
            infoFiltered: '(filtered from _MAX_ total entries)',
            zeroRecords: 'No matching backups found.'
        }
    });

    $('.delete-form').on('submit', function (e) {
        if (!confirm('Delete this backup file? This cannot be undone.')) {
            e.preventDefault();
        }
    });

    const scheduled = ('{{ $settings['backup_time'] ?? '00:00' }}').split(':');
    let scheduledHour = parseInt(scheduled[0], 10) || 0;
    let scheduledMinute = parseInt(scheduled[1], 10) || 0;

    const countdownTimer = document.getElementById('countdownTimer');
    const nextBackupTime = document.getElementById('nextBackupTime');
    const backupStatus = document.getElementById('backupStatus');
    const backupButton = document.getElementById('backupButton');
    const manualBackupForm = document.getElementById('manualBackupForm');
    const currentTimeDisplay = document.getElementById('currentTime');
    const countdownInfo = document.getElementById('countdownInfo');

    function updateCountdown() {
        const now = new Date();
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();
        const currentSecond = now.getSeconds();

        if (currentTimeDisplay) {
            currentTimeDisplay.textContent = String(currentHour).padStart(2, '0') + ':' +
                String(currentMinute).padStart(2, '0') + ':' +
                String(currentSecond).padStart(2, '0');
        }

        const nextBackup = new Date();
        nextBackup.setHours(scheduledHour, scheduledMinute, 0, 0);

        if (currentHour > scheduledHour || (currentHour === scheduledHour && currentMinute >= scheduledMinute)) {
            nextBackup.setDate(nextBackup.getDate() + 1);
        }

        const diff = nextBackup - now;
        const hours = Math.floor(diff / 3600000);
        const minutes = Math.floor((diff % 3600000) / 60000);
        const seconds = Math.floor((diff % 60000) / 1000);

        countdownTimer.textContent = String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0');

        nextBackupTime.textContent = String(scheduledHour).padStart(2, '0') + ':' +
            String(scheduledMinute).padStart(2, '0');

        if (countdownInfo) {
            if (hours > 0) {
                countdownInfo.textContent = hours + ' hour(s) ' + minutes + ' minute(s) remaining';
            } else if (minutes > 0) {
                countdownInfo.textContent = minutes + ' minute(s) ' + seconds + ' second(s) remaining';
            } else {
                countdownInfo.textContent = seconds + ' second(s) remaining';
            }
        }
    }

    setInterval(updateCountdown, 1000);
    updateCountdown();

    const backupTimeInput = document.getElementById('backup_time');
    if (backupTimeInput) {
        backupTimeInput.addEventListener('change', function () {
            const parts = this.value.split(':');
            scheduledHour = parseInt(parts[0], 10) || 0;
            scheduledMinute = parseInt(parts[1], 10) || 0;
            updateCountdown();
        });
    }

    let isRunningBackup = false;

    window.triggerTestBackup = function () {
        if (isRunningBackup) {
            return;
        }

        if (!confirm('Create a manual backup now?')) {
            return;
        }

        isRunningBackup = true;
        backupStatus.textContent = 'Running backup…';
        backupStatus.className = 'backup-status status-running';
        backupButton.disabled = true;
        const testBtn = document.getElementById('testNowBtn');
        if (testBtn) {
            testBtn.disabled = true;
        }
        backupButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Running backup…';
        manualBackupForm.submit();
    };
});
</script>
@endsection
