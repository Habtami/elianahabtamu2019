<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contract Closings</title>
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
        .status-complete { color: #047857; font-weight: bold; }
        .status-pending { color: #92400e; font-weight: bold; }
        .meta { color: #6b7280; font-size: 9px; }
        .footer { margin-top: 12px; font-size: 9px; color: #6b7280; }
    </style>
</head>
<body>
    @include('partials.pdf-letterhead', ['reportMeta' => $reportMeta, 'letterheadTitle' => 'Contract Closings Register'])

    <table class="summary">
        <tr>
            <td>
                <div class="label">Total Closings</div>
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
            <td>
                <div class="label">Closing Today</div>
                <div class="value">{{ $today }}</div>
            </td>
        </tr>
    </table>

    <table class="records">
        <thead>
            <tr>
                <th>Contract</th>
                <th>Department</th>
                <th>Contract End Date</th>
                <th>Closing Date</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($closures as $closure)
            <tr>
                <td>
                    <div>{{ $closure->contract->name ?? 'Deleted contract' }}</div>
                    <div class="meta">{{ $closure->contract->supplier_name ?? '' }}</div>
                </td>
                <td>{{ $closure->contract ? $closure->contract->getDepartmentDisplayName() : '-' }}</td>
                <td>{{ $closure->contract?->end_date ? $closure->contract->end_date->format('M d, Y') : '-' }}</td>
                <td>{{ $closure->closing_date ? $closure->closing_date->format('M d, Y') : '-' }}</td>
                <td>
                    <span class="{{ $closure->isComplete() ? 'status-complete' : 'status-pending' }}">
                        {{ $closure->getStatusLabel() }}
                    </span>
                    @if($closure->isComplete())
                        <div class="meta">
                            {{ $closure->completer->name ?? 'Unknown' }}
                            &middot; {{ $closure->completed_at?->format('M d, Y H:i') }}
                        </div>
                    @endif
                </td>
                <td>{{ $closure->remarks ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6">No contract closings found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        A closing stays Pending until Finance or an administrator marks it Complete.
    </div>
</body>
</html>
