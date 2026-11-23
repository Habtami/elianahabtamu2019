<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Orders</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        h2 { margin: 0 0 4px; font-size: 18px; }
        .muted { color: #6b7280; margin-bottom: 14px; }
        .summary { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
        .summary td { padding: 8px; border: 1px solid #d1d5db; width: 25%; }
        .summary .label { font-size: 9px; text-transform: uppercase; color: #6b7280; }
        .summary .value { font-size: 14px; font-weight: bold; }
        table.records { width: 100%; border-collapse: collapse; }
        table.records th { background: #f3f4f6; text-align: left; padding: 6px; border: 1px solid #d1d5db; }
        table.records td { padding: 6px; border: 1px solid #e5e7eb; vertical-align: top; }
        table.records td.amount, table.records th.amount { text-align: right; }
        .status-approved { color: #047857; font-weight: bold; }
        .status-first { color: #1d4ed8; font-weight: bold; }
        .status-pending { color: #92400e; font-weight: bold; }
        .status-muted { color: #6b7280; font-weight: bold; }
        .status-locked { color: #c2410c; font-weight: bold; }
        .locked-note { color: #ea580c; font-weight: bold; font-size: 9px; }
        .meta { color: #6b7280; font-size: 9px; }
        .footer { margin-top: 12px; font-size: 9px; color: #6b7280; }
    </style>
</head>
<body>
    <h2>Purchase Order Processing</h2>
    <div class="muted">
        @if($search !== '')
            Filtered by: &ldquo;{{ $search }}&rdquo;
        @else
            All purchase orders
        @endif
        &middot; {{ $orders->count() }} order(s) listed
        &middot; Generated: {{ now()->format('M d, Y H:i') }}
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Total POs</div>
                <div class="value">{{ $total }}</div>
            </td>
            <td>
                <div class="label">Awaiting 1st Approval</div>
                <div class="value">{{ $pendingFirst }}</div>
            </td>
            <td>
                <div class="label">Awaiting 2nd Approval</div>
                <div class="value">{{ $pendingSecond }}</div>
            </td>
            <td>
                <div class="label">Fully Approved</div>
                <div class="value">{{ $approved }}</div>
            </td>
        </tr>
    </table>

    <table class="records">
        <thead>
            <tr>
                <th>PO Number</th>
                <th class="amount">Amount (ETB)</th>
                <th>Approval Date</th>
                <th>1st Approval (GS Manager)</th>
                <th>2nd Approval (DDG)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->po_number }}</td>
                <td class="amount">{{ $order->amount !== null ? number_format((float) $order->amount, 2) : '-' }}</td>
                <td>{{ $order->approval_date ? $order->approval_date->format('M d, Y') : '-' }}</td>
                <td>
                    @if($order->isGsApproved())
                        <span class="status-approved">Approved</span>
                        <div>{{ $order->gsManagerApprover->name ?? 'Unknown' }}</div>
                        <div class="meta">{{ $order->gs_manager_approved_at?->format('M d, Y H:i') }}</div>
                    @else
                        <span class="status-pending">Pending</span>
                    @endif
                </td>
                <td>
                    @if($order->isDdgApproved())
                        <span class="status-approved">Approved</span>
                        <div>{{ $order->ddgApprover->name ?? 'Unknown' }}</div>
                        <div class="meta">{{ $order->ddg_approved_at?->format('M d, Y H:i') }}</div>
                    @elseif(!$order->requiresDdgApproval())
                        <span class="status-muted">Not Required</span>
                        <div class="meta">Amount is at or below ETB 400,000</div>
                    @elseif(!$order->isGsApproved())
                        <span class="status-locked">Locked</span>
                        <div class="locked-note">Awaiting 2nd approval</div>
                    @else
                        <span class="status-pending">Pending</span>
                    @endif
                </td>
                <td>
                    @php
                        $status = $order->getStatus();
                    @endphp
                    <span class="{{ $status === \App\Models\PurchaseOrder::STATUS_APPROVED ? 'status-approved' : ($status === \App\Models\PurchaseOrder::STATUS_FIRST_APPROVED ? 'status-first' : 'status-pending') }}">
                        {{ $order->getStatusLabel() }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">No purchase orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        All amounts in Ethiopian Birr (ETB). Purchase orders above ETB 400,000 need both approvals
        (GS Manager first, then DDG); at ETB 400,000 or less, the GS Manager approval completes the process.
    </div>
</body>
</html>
