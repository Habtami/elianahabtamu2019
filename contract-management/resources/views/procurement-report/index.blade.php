@extends('layouts.app')

@section('title', 'Procurement Report')

@section('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .print-card { box-shadow: none !important; }
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-chart-pie text-[#0ea5e9] mr-3"></i>
                Procurement Report
            </h1>
            <p class="text-gray-600 mt-2">Total procurement by amount, split by purchase and by tender. Amounts in ETB.</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <a href="{{ route('procurement-report.export', array_merge(['format' => 'csv'], request()->query())) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-excel mr-2"></i>Excel
            </a>
            <a href="{{ route('procurement-report.export', array_merge(['format' => 'pdf'], request()->query())) }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-file-pdf mr-2"></i>PDF
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Total procurement -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="relative overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 print-card">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-[#0ea5e9]"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">Total Procurement</div>
            <div class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($grandTotal, 2) }} <span class="text-sm font-medium text-gray-500">ETB</span></div>
            <div class="text-xs text-gray-500 mt-1">{{ $purchaseCount + $tenderCount }} record(s)</div>
        </div>
        <div class="relative overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 print-card">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-blue-600"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">By Purchase</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($purchaseTotal, 2) }} <span class="text-sm font-medium text-gray-500">ETB</span></div>
            <div class="text-xs text-gray-500 mt-1">{{ $purchaseCount }} purchase order(s)</div>
        </div>
        <div class="relative overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 print-card">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-[#0d9488]"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">By Tender</div>
            <div class="text-2xl font-bold text-[#0d9488] mt-1">{{ number_format($tenderTotal, 2) }} <span class="text-sm font-medium text-gray-500">ETB</span></div>
            <div class="text-xs text-gray-500 mt-1">{{ $tenderCount }} contract(s)</div>
        </div>
    </div>

    <!-- Tender breakdown -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="relative overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 print-card">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-indigo-500"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">ICB Tenders</div>
            <div class="text-xl font-bold text-gray-800 mt-1">{{ number_format($icbTotal, 2) }} <span class="text-sm font-medium text-gray-500">ETB</span></div>
            <div class="text-xs text-gray-500 mt-1">{{ $icbCount }} contract(s)</div>
        </div>
        <div class="relative overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm pl-6 pr-5 py-4 print-card">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-amber-500"></span>
            <div class="text-xs uppercase tracking-wide text-gray-500">NCB Tenders</div>
            <div class="text-xl font-bold text-gray-800 mt-1">{{ number_format($ncbTotal, 2) }} <span class="text-sm font-medium text-gray-500">ETB</span></div>
            <div class="text-xs text-gray-500 mt-1">{{ $ncbCount }} contract(s)</div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('procurement-report.index') }}"
          class="bg-white rounded-lg shadow-sm border border-gray-200 px-5 py-4 mb-6 no-print">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="from" class="block text-sm font-medium text-gray-700 mb-1">From</label>
                <input type="date" id="from" name="from" value="{{ $from }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0ea5e9]">
            </div>
            <div>
                <label for="to" class="block text-sm font-medium text-gray-700 mb-1">To</label>
                <input type="date" id="to" name="to" value="{{ $to }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0ea5e9]">
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="category" name="category"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0ea5e9]">
                    <option value="">All categories</option>
                    <option value="purchase" @selected($category === 'purchase')>By Purchase</option>
                    <option value="tender" @selected($category === 'tender')>By Tender</option>
                </select>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-[#0ea5e9] text-white font-medium rounded-lg hover:bg-[#0284c7] transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('procurement-report.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-rotate-left mr-2"></i>Reset
                </a>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-3">
            Date range applies to the purchase order approval date and the contract start date.
        </p>
    </form>

    @if($rows->isNotEmpty())
    <!-- Chart report -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="relative overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm p-5 pl-6 print-card">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-[#0ea5e9]"></span>
            <h2 class="text-sm font-semibold text-gray-800 flex items-center">
                <i class="fas fa-chart-pie text-[#0ea5e9] mr-2"></i>Spend Share
            </h2>
            <p class="text-xs text-gray-500 mt-1">Purchase vs tender by amount (ETB)</p>
            <div class="relative h-56 mt-3">
                <canvas id="procurementShareChart"></canvas>
            </div>
        </div>

        <div class="relative overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm p-5 pl-6 print-card">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-amber-500"></span>
            <h2 class="text-sm font-semibold text-gray-800 flex items-center">
                <i class="fas fa-building text-amber-500 mr-2"></i>Top Suppliers
            </h2>
            <p class="text-xs text-gray-500 mt-1">Highest tender amounts by supplier (ETB)</p>
            <div class="relative h-56 mt-3">
                @if(count($chartSuppliers['labels']))
                    <canvas id="procurementSuppliersChart"></canvas>
                @else
                    <div class="h-full flex items-center justify-center text-sm text-gray-400">
                        <i class="fas fa-inbox mr-2"></i>No supplier data for this filter
                    </div>
                @endif
            </div>
        </div>

        <div class="relative overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm p-5 pl-6 print-card">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-indigo-500"></span>
            <h2 class="text-sm font-semibold text-gray-800 flex items-center">
                <i class="fas fa-layer-group text-indigo-500 mr-2"></i>Material Group Spend
            </h2>
            <p class="text-xs text-gray-500 mt-1">Tender amounts by material group (ETB)</p>
            <div class="relative h-56 mt-3">
                @if(count($chartMaterialGroups['labels']))
                    <canvas id="procurementMaterialGroupChart"></canvas>
                @else
                    <div class="h-full flex items-center justify-center text-sm text-gray-400">
                        <i class="fas fa-inbox mr-2"></i>No material group data for this filter
                    </div>
                @endif
            </div>
        </div>

        <div class="relative overflow-hidden lg:col-span-3 bg-white rounded-lg border border-gray-200 shadow-sm p-5 pl-6 print-card">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-[#0d9488]"></span>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div>
                    <h2 class="text-sm font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-chart-column text-[#0d9488] mr-2"></i>Monthly Procurement Trend
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Purchase and tender amounts per month (ETB)</p>
                </div>
                <span class="text-xs text-gray-500">{{ count($chartMonthly['labels']) }} month(s)</span>
            </div>
            <div class="relative h-72 mt-3">
                @if(count($chartMonthly['labels']))
                    <canvas id="procurementMonthlyChart"></canvas>
                @else
                    <div class="h-full flex items-center justify-center text-sm text-gray-400">
                        <i class="fas fa-inbox mr-2"></i>No dated records for this filter
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 print-card">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Procurement Records</h2>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">
                    {{ $paginatedRows->total() }} record(s) &middot; page {{ $paginatedRows->currentPage() }} of {{ $paginatedRows->lastPage() }}
                </span>
                <button type="button" class="table-tip-btn" data-table-tip aria-expanded="false" aria-controls="tipProcurementReport"><i class="fas fa-info-circle"></i> TIP: info</button>
            </div>
        </div>

        <div id="tipProcurementReport" class="table-tip-panel no-print"><h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4><ul><li>From and To dates filter purchase orders by approval date and contracts by start date; Category limits results to purchases or tenders.</li><li>KPI cards show total procurement, the purchase versus tender split, and ICB and NCB tender totals with record counts.</li><li>Four charts (spend share, top suppliers, material group, monthly trend) follow the same filters and hide when a group has no data.</li><li>Records list category, type, reference, supplier, date and amount, newest first, fifteen per page, with a hint when nothing matches.</li><li>Footer totals for purchase, tender and grand total cover every filtered record, not only the rows on this page.</li><li>Print, Excel and PDF buttons reuse the active filters and date range; tip panels and filter controls stay out of printouts.</li></ul></div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-6 py-3 font-medium">Category</th>
                        <th class="px-6 py-3 font-medium">Type</th>
                        <th class="px-6 py-3 font-medium">Reference</th>
                        <th class="px-6 py-3 font-medium">Supplier</th>
                        <th class="px-6 py-3 font-medium">Date</th>
                        <th class="px-6 py-3 font-medium text-right">Amount (ETB)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($paginatedRows as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($row->category === 'Purchase') bg-blue-100 text-blue-800
                                @else bg-teal-100 text-teal-800 @endif">
                                {{ $row->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $row->type }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $row->reference }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $row->supplier }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $row->date?->format('M d, Y') ?? '-' }}</td>
                        <td class="px-6 py-4 text-right font-medium text-gray-800">{{ number_format($row->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <i class="fas fa-chart-pie text-4xl text-gray-300"></i>
                            <p class="mt-4 text-gray-700 font-medium">No procurement amounts found</p>
                            <p class="mt-1 text-sm text-gray-500">Add amounts to purchase orders or contracts, or adjust the filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($paginatedRows->count() > 0)
                <tfoot class="bg-gray-50 text-gray-800">
                    <tr>
                        <td colspan="5" class="px-6 py-3 text-right font-medium">Total by Purchase</td>
                        <td class="px-6 py-3 text-right font-medium">{{ number_format($purchaseTotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="px-6 py-3 text-right font-medium">Total by Tender</td>
                        <td class="px-6 py-3 text-right font-medium">{{ number_format($tenderTotal, 2) }}</td>
                    </tr>
                    <tr class="border-t border-gray-300">
                        <td colspan="5" class="px-6 py-3 text-right font-bold">Total Procurement</td>
                        <td class="px-6 py-3 text-right font-bold">{{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        @if($paginatedRows->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 no-print">
            {{ $paginatedRows->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') return;

        const formatAmount = (value) => Number(value || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        const compactTick = (value) => {
            const number = Number(value || 0);
            if (Math.abs(number) >= 1000000) return (number / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
            if (Math.abs(number) >= 1000) return (number / 1000).toFixed(0) + 'K';
            return number.toFixed(0);
        };

        const moneyTooltip = (ctx) => {
            const raw = ctx.parsed;
            const value = raw && typeof raw === 'object'
                ? (raw.y ?? raw.x ?? raw.r ?? 0)
                : raw;
            return (ctx.dataset.label ? ctx.dataset.label + ': ' : '') + 'ETB ' + formatAmount(value);
        };

        const shareCanvas = document.getElementById('procurementShareChart');
        if (shareCanvas) {
            new Chart(shareCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['By Purchase', 'By Tender'],
                    datasets: [{
                        data: @json([$chartShare['purchase'], $chartShare['tender']]),
                        backgroundColor: ['#0ea5e9', '#0d9488'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { callbacks: { label: moneyTooltip } },
                    },
                },
            });
        }

        const monthlyCanvas = document.getElementById('procurementMonthlyChart');
        if (monthlyCanvas) {
            new Chart(monthlyCanvas, {
                type: 'bar',
                data: {
                    labels: @json($chartMonthly['labels']),
                    datasets: [
                        {
                            label: 'Purchase',
                            data: @json($chartMonthly['purchase']),
                            backgroundColor: '#0ea5e9',
                            borderRadius: 4,
                            maxBarThickness: 30,
                        },
                        {
                            label: 'Tender',
                            data: @json($chartMonthly['tender']),
                            backgroundColor: '#0d9488',
                            borderRadius: 4,
                            maxBarThickness: 30,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { callbacks: { label: moneyTooltip } },
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            beginAtZero: true,
                            ticks: { callback: compactTick },
                            title: { display: true, text: 'ETB' },
                        },
                    },
                },
            });
        }

        const suppliersCanvas = document.getElementById('procurementSuppliersChart');
        if (suppliersCanvas) {
            new Chart(suppliersCanvas, {
                type: 'bar',
                data: {
                    labels: @json($chartSuppliers['labels']),
                    datasets: [{
                        label: 'Amount',
                        data: @json($chartSuppliers['amounts']),
                        backgroundColor: ['#f59e0b', '#fb923c', '#fbbf24', '#f97316', '#fcd34d'],
                        borderRadius: 4,
                        maxBarThickness: 26,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: moneyTooltip } },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { callback: compactTick },
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                callback: function (value) {
                                    const label = this.getLabelForValue(value);
                                    return label.length > 18 ? label.slice(0, 17) + '…' : label;
                                },
                            },
                        },
                    },
                },
            });
        }

        const materialCanvas = document.getElementById('procurementMaterialGroupChart');
        if (materialCanvas) {
            new Chart(materialCanvas, {
                type: 'polarArea',
                data: {
                    labels: @json($chartMaterialGroups['labels']),
                    datasets: [{
                        data: @json($chartMaterialGroups['amounts']),
                        backgroundColor: [
                            'rgba(14, 165, 233, 0.75)',
                            'rgba(13, 148, 136, 0.75)',
                            'rgba(99, 102, 241, 0.75)',
                            'rgba(245, 158, 11, 0.75)',
                            'rgba(236, 72, 153, 0.75)',
                            'rgba(34, 197, 94, 0.75)',
                        ],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { callbacks: { label: moneyTooltip } },
                    },
                    scales: {
                        r: {
                            ticks: { display: false },
                            grid: { color: 'rgba(148, 163, 184, 0.25)' },
                        },
                    },
                },
            });
        }
    });
</script>
@endsection
