<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Battery Issues Report</title>
    <style>
        @page { margin: 10mm; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 9pt; color: #1e293b; margin: 0; }
        h1 { font-size: 16pt; color: #0f2a44; margin: 0 0 4px; }
        .meta { font-size: 8.5pt; color: #475569; margin-bottom: 2px; }
        .summary { margin: 10px 0 12px; }
        .summary span { display: inline-block; margin-right: 14px; font-weight: bold; }
        .sev-high { color: #b91c1c; }
        .sev-medium { color: #92400e; }
        .sev-low { color: #075985; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #9aa9b8; padding: 4px 6px; text-align: left; vertical-align: top; }
        th {
            background: #eaf1f8;
            color: #345572;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .badge { font-weight: bold; text-transform: uppercase; font-size: 7pt; }
        .issues { font-size: 8pt; }
        .empty { padding: 18px; text-align: center; color: #166534; border: 1px solid #a7f3d0; background: #ecfdf5; }
    </style>
</head>
<body>
    <h1>Battery Report — Issues Requiring Attention</h1>
    <div class="meta">Generated: {{ $generatedAt->format('d M Y H:i') }}</div>
    <div class="meta">
        Date basis: {{ $dateField === 'manufacture' ? 'Date of Manufacture' : 'Date Given' }}
        ·
        @if($from && $to)
            {{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        @elseif($from)
            From {{ \Carbon\Carbon::parse($from)->format('d M Y') }}
        @elseif($to)
            Up to {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        @else
            All dates
        @endif
        · {{ $totalBatteries }} battery record(s)
    </div>

    <div class="summary">
        <span>{{ $rows->count() }} battery(ies) with issues</span>
        <span class="sev-high">{{ $issueCounts['high'] }} high</span>
        <span class="sev-medium">{{ $issueCounts['medium'] }} medium</span>
        <span class="sev-low">{{ $issueCounts['low'] }} low</span>
    </div>

    @if($rows->isEmpty())
        <div class="empty">No issues found for the selected range — all battery records look consistent.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">Severity</th>
                    <th style="width: 13%;">Serial No</th>
                    <th style="width: 10%;">Plate No</th>
                    <th style="width: 17%;">Type / Brand</th>
                    <th style="width: 13%;">Driver</th>
                    <th style="width: 10%;">Status</th>
                    <th>Issue(s)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    @php $battery = $row['battery']; @endphp
                    <tr>
                        <td><span class="badge sev-{{ $row['severity'] }}">{{ $row['severity'] }}</span></td>
                        <td>{{ $battery->serial_number ?: '—' }}</td>
                        <td>{{ $battery->plate_number ?: '—' }}</td>
                        <td>{{ $battery->battery_type ?: '—' }} / {{ $battery->brand ?: '—' }}</td>
                        <td>{{ $battery->driver_name ?: '—' }}</td>
                        <td>{{ $battery->getStatusDisplayName() }}</td>
                        <td class="issues">
                            @foreach($row['issues'] as $issue)
                                • {{ $issue['message'] }}<br>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
