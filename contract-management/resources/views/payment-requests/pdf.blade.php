<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Requests</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        h2 { margin: 0 0 4px; font-size: 18px; }
        .muted { color: #6b7280; margin-bottom: 14px; }
        .summary { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
        .summary td { padding: 8px; border: 1px solid #d1d5db; width: 33%; }
        .summary .label { font-size: 9px; text-transform: uppercase; color: #6b7280; }
        .summary .value { font-size: 14px; font-weight: bold; }
        table.records { width: 100%; border-collapse: collapse; }
        table.records th { background: #f3f4f6; text-align: left; padding: 6px; border: 1px solid #d1d5db; }
        table.records td { padding: 6px; border: 1px solid #e5e7eb; vertical-align: top; }
        .status-approved { color: #047857; font-weight: bold; }
        .status-pending { color: #92400e; font-weight: bold; }
        .status-locked { color: #c2410c; font-weight: bold; }
        .locked-note { color: #ea580c; font-weight: bold; font-size: 9px; }
        .meta { color: #6b7280; font-size: 9px; }
        .footer { margin-top: 12px; font-size: 9px; color: #6b7280; }
    </style>
</head>
<body>
    <h2>Payment Requests</h2>
    <div class="muted">
        @if($search !== '')
            Filtered by: &ldquo;{{ $search }}&rdquo;
        @else
            All payment requests
        @endif
        &middot; {{ $requests->count() }} request(s) listed
        &middot; Generated: {{ now()->format('M d, Y H:i') }}
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Total Requests</div>
                <div class="value">{{ $total }}</div>
            </td>
            <td>
                <div class="label">Pending</div>
                <div class="value">{{ $pending }}</div>
            </td>
            <td>
                <div class="label">Complete</div>
                <div class="value">{{ $complete }}</div>
            </td>
        </tr>
    </table>

    <table class="records">
        <thead>
            <tr>
                <th>Request No.</th>
                <th>Payment Request Date</th>
                <th>GS (1st Approval)</th>
                <th>Finance (2nd Approval)</th>
                <th>Payment Status</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $paymentRequest)
            <tr>
                <td>{{ $paymentRequest->getReference() }}</td>
                <td>{{ $paymentRequest->payment_request_date ? $paymentRequest->payment_request_date->format('M d, Y') : '-' }}</td>
                <td>
                    @if($paymentRequest->isGsApproved())
                        <span class="status-approved">Approved</span>
                        <div>{{ $paymentRequest->gsApprover->name ?? 'Unknown' }}</div>
                        <div class="meta">{{ $paymentRequest->gs_approved_at?->format('M d, Y H:i') }}</div>
                    @else
                        <span class="status-pending">Pending</span>
                    @endif
                </td>
                <td>
                    @if($paymentRequest->isFinanceCompleted())
                        <span class="status-approved">Completed</span>
                        <div>{{ $paymentRequest->financeApprover->name ?? 'Unknown' }}</div>
                        <div class="meta">{{ $paymentRequest->finance_approved_at?->format('M d, Y H:i') }}</div>
                    @elseif(!$paymentRequest->isGsApproved())
                        <span class="status-locked">Locked</span>
                        <div class="locked-note">Awaiting GS approval</div>
                    @else
                        <span class="status-pending">Pending</span>
                    @endif
                </td>
                <td>
                    <span class="{{ $paymentRequest->isFinanceCompleted() ? 'status-approved' : 'status-pending' }}">
                        {{ $paymentRequest->getStatusLabel() }}
                    </span>
                </td>
                <td>{{ $paymentRequest->remarks ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6">No payment requests found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Finance can only complete a payment after the GS approval has been recorded.
    </div>
</body>
</html>
