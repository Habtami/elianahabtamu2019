<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Battery Given and Return</title>
    <style>
        @page { margin: 12mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; }
        thead { display: table-header-group; }
        th {
            background: #eef4fb;
            color: #345572;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.02em;
            padding: 6px 5px;
            border: 1px solid #c9d7e6;
            text-align: left;
        }
        td { padding: 5px; border: 1px solid #dbe5ef; vertical-align: middle; }
        tr:nth-child(even) td { background: #f9fbfe; }
        .status { font-weight: bold; }
        .status-in-service { color: #166534; }
        .status-returned { color: #854d0e; }
        .diff-positive { color: #166534; font-weight: bold; }
        .diff-negative { color: #b91c1c; font-weight: bold; }
        .empty { text-align: center; color: #6b8299; padding: 14px; }
    </style>
</head>
<body>
    @include('partials.pdf-letterhead', ['reportMeta' => $reportMeta, 'letterheadTitle' => 'Batteries Register'])

    <table>
        <thead>
            <tr>
                <th>Date Mfg</th>
                <th>Serial No</th>
                <th>Material Code</th>
                <th>Battery Type</th>
                <th>Brand</th>
                <th>Plate No</th>
                <th>Driver</th>
                <th>Initial KM</th>
                <th>Return KM</th>
                <th>Difference</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($batteries as $battery)
            @php
                $initial = (int) $battery->initial_kilometer;
                $returned = (int) $battery->return_kilometer;
                $showDiff = $battery->status === 'returned' || $returned > $initial;
                $difference = $returned - $initial;
            @endphp
            <tr>
                <td>{{ $battery->date_of_manufacture ? $battery->date_of_manufacture->format('Y-m-d') : '—' }}</td>
                <td>{{ $battery->serial_number }}</td>
                <td>{{ $battery->material_code ?: '—' }}</td>
                <td>{{ $battery->battery_type }}</td>
                <td>{{ $battery->brand }}</td>
                <td>{{ $battery->plate_number }}</td>
                <td>{{ $battery->driver_name ?: '—' }}</td>
                <td style="text-align:right;">{{ number_format($initial) }}</td>
                <td style="text-align:right;">{{ number_format($returned) }}</td>
                <td style="text-align:right;">
                    @if($showDiff)
                        <span class="{{ $difference >= 0 ? 'diff-positive' : 'diff-negative' }}">{{ $difference >= 0 ? '+' : '' }}{{ number_format($difference) }}</span>
                    @else
                        —
                    @endif
                </td>
                <td class="status {{ $battery->status === 'returned' ? 'status-returned' : 'status-in-service' }}">{{ $battery->getStatusDisplayName() }}</td>
            </tr>
            @empty
            <tr><td colspan="11" class="empty">No batteries found</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
