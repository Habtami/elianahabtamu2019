@php
    $filters = $filters ?? collect();
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tire Inventory</title>
    <style>
        @page { size: A4 portrait; margin: 8mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7.5px; color: #1f2937; }
        h2 { margin: 0 0 4px; font-size: 15px; }
        .muted { color: #6b7280; margin-bottom: 8px; font-size: 8px; }
        table.records { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.records th { background: #f3f4f6; text-align: left; padding: 3px 3px; border: 1px solid #d1d5db; font-size: 6.5px; text-transform: uppercase; word-break: break-word; }
        table.records td { padding: 3px 3px; border: 1px solid #e5e7eb; vertical-align: top; word-break: break-word; overflow-wrap: anywhere; }
        table.records td.roll { color: #6b7280; }
        table.records td.num { text-align: right; }
        table.records th.num { text-align: right; }
        .footer { margin-top: 8px; font-size: 7px; color: #6b7280; }
    </style>
</head>
<body>
    <h2>Tire Inventory</h2>
    <div class="muted">
        {{ $rows->count() }} tire(s)
        @if($filters->isNotEmpty())
            &middot; {{ $filters->implode(' · ') }}
        @endif
        &middot; Generated: {{ now()->format('M d, Y H:i') }}
    </div>

    <table class="records">
        <colgroup>
            <col style="width:5%;">
            <col style="width:7%;">
            <col style="width:13%;">
            <col style="width:15%;">
            <col style="width:11%;">
            <col style="width:12%;">
            <col style="width:12%;">
            <col style="width:7%;">
            <col style="width:7%;">
            <col style="width:7%;">
            <col style="width:4%;">
        </colgroup>
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Tire Type</th>
                <th>Serial Number</th>
                <th>Plate Number</th>
                <th>Given By</th>
                <th>Driver</th>
                <th class="num">Initial KM</th>
                <th class="num">Current KM</th>
                <th class="num">KM Difference</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td class="roll">{{ $loop->iteration }}</td>
                <td>#{{ $row['id'] }}</td>
                <td>{{ $row['type'] }}</td>
                <td>{{ $row['serial'] }}</td>
                <td>{{ $row['plate'] ?: '-' }}</td>
                <td>{{ $row['giver'] ?: '-' }}</td>
                <td>{{ $row['driver'] ?: '-' }}</td>
                <td class="num">{{ $row['initial_km'] !== null ? number_format($row['initial_km']) : '-' }}</td>
                <td class="num">{{ $row['current_km'] !== null ? number_format($row['current_km']) : '-' }}</td>
                <td class="num">{{ $row['km_diff'] >= 0 ? '+' : '' }}{{ number_format($row['km_diff']) }}</td>
                <td>{{ $row['status'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11">No tires found for the selected filters.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Generated from the tire inventory using the active search and filters.</div>
</body>
</html>
