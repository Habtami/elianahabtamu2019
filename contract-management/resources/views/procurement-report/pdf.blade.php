<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Procurement Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h2 { margin: 0 0 4px; font-size: 18px; }
        .muted { color: #6b7280; margin-bottom: 14px; }
        .summary { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
        .summary td { padding: 8px; border: 1px solid #d1d5db; width: 25%; }
        .summary .label { font-size: 9px; text-transform: uppercase; color: #6b7280; }
        .summary .value { font-size: 14px; font-weight: bold; }
        table.records { width: 100%; border-collapse: collapse; }
        table.records th { background: #f3f4f6; text-align: left; padding: 6px; border: 1px solid #d1d5db; }
        table.records td { padding: 6px; border: 1px solid #e5e7eb; }
        table.records td.amount, table.records th.amount { text-align: right; }
        table.records tfoot td { font-weight: bold; background: #f9fafb; }
        .footer { margin-top: 12px; font-size: 9px; color: #6b7280; }
    </style>
</head>
<body>
    @include('partials.pdf-letterhead', ['reportMeta' => $reportMeta, 'letterheadTitle' => 'Procurement Report'])

    <table class="summary">
        <tr>
            <td>
                <div class="label">Total Procurement</div>
                <div class="value">{{ number_format($grandTotal, 2) }} ETB</div>
            </td>
            <td>
                <div class="label">By Purchase</div>
                <div class="value">{{ number_format($purchaseTotal, 2) }} ETB</div>
                <div>{{ $purchaseCount }} order(s)</div>
            </td>
            <td>
                <div class="label">By Tender</div>
                <div class="value">{{ number_format($tenderTotal, 2) }} ETB</div>
                <div>{{ $tenderCount }} contract(s)</div>
            </td>
            <td>
                <div class="label">ICB / NCB</div>
                <div>{{ number_format($icbTotal, 2) }} / {{ number_format($ncbTotal, 2) }} ETB</div>
                <div>{{ $icbCount }} / {{ $ncbCount }} contract(s)</div>
            </td>
        </tr>
    </table>

    <table class="records">
        <thead>
            <tr>
                <th>Category</th>
                <th>Type</th>
                <th>Reference</th>
                <th>Supplier</th>
                <th>Date</th>
                <th class="amount">Amount (ETB)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ $row->category }}</td>
                <td>{{ $row->type }}</td>
                <td>{{ $row->reference }}</td>
                <td>{{ $row->supplier }}</td>
                <td>{{ $row->date?->format('M d, Y') ?? '-' }}</td>
                <td class="amount">{{ number_format($row->amount, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6">No procurement amounts found.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">Total by Purchase</td>
                <td class="amount">{{ number_format($purchaseTotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5">Total by Tender</td>
                <td class="amount">{{ number_format($tenderTotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5">Total Procurement</td>
                <td class="amount">{{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">All amounts in Ethiopian Birr (ETB).</div>
</body>
</html>
