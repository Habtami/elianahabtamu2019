@php
    $statusLabels = \App\Models\Contract::getStatuses();
    $appliedFilters = collect([
        request()->filled('search') ? 'Search: ' . request('search') : null,
        request()->filled('status') && array_key_exists(request('status'), $statusLabels) ? 'Status: ' . $statusLabels[request('status')] : null,
        request()->filled('department') ? 'Department: ' . ucfirst(str_replace('_', ' ', request('department'))) : null,
    ])->filter();
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contracts Export</title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1f2937; }
        h2 { margin: 0 0 4px; font-size: 17px; }
        .muted { color: #6b7280; margin-bottom: 10px; }
        table.records { width: 100%; border-collapse: collapse; }
        table.records th { background: #f3f4f6; text-align: left; padding: 5px; border: 1px solid #d1d5db; font-size: 8px; text-transform: uppercase; }
        table.records td { padding: 5px; border: 1px solid #e5e7eb; vertical-align: top; }
        table.records td.roll { color: #6b7280; width: 28px; }
        table.records td.amount, table.records th.amount { text-align: right; }
        .footer { margin-top: 10px; font-size: 8px; color: #6b7280; }
    </style>
</head>
<body>
    <h2>Contracts</h2>
    <div class="muted">
        {{ $contracts->count() }} record(s)
        @if($appliedFilters->isNotEmpty())
            &middot; {{ $appliedFilters->implode(' · ') }}
        @endif
        &middot; Generated: {{ now()->format('M d, Y H:i') }}
    </div>

    <table class="records">
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Contract Name</th>
                <th>Supplier</th>
                <th>Department</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th class="amount">PG Amount</th>
                <th>Status</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contracts as $contract)
            <tr>
                <td class="roll">{{ $loop->iteration }}</td>
                <td>#{{ $contract->id }}</td>
                <td>{{ $contract->name }}</td>
                <td>{{ $contract->supplier_name ?? 'N/A' }}</td>
                <td>{{ $contract->department ? ucfirst(str_replace('_', ' ', $contract->department)) : '-' }}</td>
                <td>{{ $contract->start_date?->format('Y-m-d') ?? '-' }}</td>
                <td>
                    {{ $contract->end_date?->format('Y-m-d') ?? '-' }}
                    @if($contract->extended_count > 0)
                        (Extended {{ $contract->extended_count }}x)
                    @endif
                </td>
                <td class="amount">
                    {{ $contract->performance_guarantee_amount ? '$' . number_format($contract->performance_guarantee_amount, 2) : '-' }}
                </td>
                <td>{{ $contract->getStatusDisplayName() }}</td>
                <td>{{ $contract->creator->name ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10">No contracts found for the selected filters.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Generated from the contract register using the active search and filters.</div>
</body>
</html>
