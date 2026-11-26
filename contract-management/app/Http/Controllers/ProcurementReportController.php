<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;

class ProcurementReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->canViewProcurementReport()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view the procurement report.');
        }

        $data = $this->buildReport($request, $user);

        return view('procurement-report.index', $data);
    }

    public function export(Request $request, string $format)
    {
        $user = auth()->user();

        if (!$user->canViewProcurementReport()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view the procurement report.');
        }

        $data = $this->buildReport($request, $user);

        if ($format === 'pdf') {
            return Pdf::loadView('procurement-report.pdf', $data)
                ->download('procurement-report-' . now()->format('Ymd-His') . '.pdf');
        }

        $filename = 'procurement-report-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Category', 'Type', 'Reference', 'Supplier', 'Date', 'Amount (ETB)']);

            foreach ($data['rows'] as $row) {
                fputcsv($out, [
                    $row->category,
                    $row->type,
                    $row->reference,
                    $row->supplier,
                    $row->date?->format('Y-m-d'),
                    number_format($row->amount, 2, '.', ''),
                ]);
            }

            fputcsv($out, []);
            fputcsv($out, ['Total by Purchase', '', '', '', '', number_format($data['purchaseTotal'], 2, '.', '')]);
            fputcsv($out, ['Total by Tender', '', '', '', '', number_format($data['tenderTotal'], 2, '.', '')]);
            fputcsv($out, ['Total Procurement', '', '', '', '', number_format($data['grandTotal'], 2, '.', '')]);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function buildReport(Request $request, $user): array
    {
        $from = $request->filled('from') ? Carbon::parse($request->from)->toDateString() : null;
        $to = $request->filled('to') ? Carbon::parse($request->to)->toDateString() : null;
        $category = in_array($request->category, ['purchase', 'tender'], true) ? $request->category : null;

        $purchaseRows = collect();

        if ($category !== 'tender') {
            $query = PurchaseOrder::query();

            if ($from) {
                $query->whereDate('approval_date', '>=', $from);
            }

            if ($to) {
                $query->whereDate('approval_date', '<=', $to);
            }

            $purchaseRows = $query->get()->map(function (PurchaseOrder $order) {
                return (object) [
                    'category' => 'Purchase',
                    'type' => 'Purchase Order',
                    'reference' => $order->po_number,
                    'supplier' => '-',
                    'date' => $order->approval_date,
                    'sort_date' => $order->approval_date?->timestamp ?? 0,
                    'amount' => (float) ($order->amount ?? 0),
                ];
            });
        }

        $tenderRows = collect();

        if ($category !== 'purchase') {
            $query = Contract::forUser($user)->whereNotNull('contract_amount');

            if ($from) {
                $query->whereDate('start_date', '>=', $from);
            }

            if ($to) {
                $query->whereDate('start_date', '<=', $to);
            }

            $tenderRows = $query->get()->map(function (Contract $contract) {
                return (object) [
                    'category' => 'Tender',
                    'type' => $contract->contract_type ?: 'Tender',
                    'reference' => $contract->name,
                    'supplier' => $contract->supplier_name ?: '-',
                    'material_group' => $contract->material_group ?: null,
                    'date' => $contract->start_date,
                    'sort_date' => $contract->start_date?->timestamp ?? 0,
                    'amount' => (float) ($contract->contract_amount ?? 0),
                ];
            });
        }

        $rows = $purchaseRows->concat($tenderRows)->sortByDesc('sort_date')->values();

        $purchaseTotal = (float) $purchaseRows->sum('amount');
        $tenderTotal = (float) $tenderRows->sum('amount');
        $icbTotal = (float) $tenderRows->where('type', 'ICB')->sum('amount');
        $ncbTotal = (float) $tenderRows->where('type', 'NCB')->sum('amount');

        $page = Paginator::resolveCurrentPage('page');
        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $paginatedRows = new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $monthly = $rows->filter(fn ($row) => $row->date !== null)
            ->groupBy(fn ($row) => $row->date->format('Y-m'))
            ->sortKeys();

        $monthlyLabels = [];
        $monthlyPurchase = [];
        $monthlyTender = [];

        foreach ($monthly as $month => $group) {
            $monthlyLabels[] = Carbon::createFromFormat('Y-m-d', $month . '-01')->format('M Y');
            $monthlyPurchase[] = round((float) $group->where('category', 'Purchase')->sum('amount'), 2);
            $monthlyTender[] = round((float) $group->where('category', 'Tender')->sum('amount'), 2);
        }

        $supplierTotals = $rows->filter(fn ($row) => !empty($row->supplier) && $row->supplier !== '-')
            ->groupBy('supplier')
            ->map(fn ($group) => round((float) $group->sum('amount'), 2))
            ->sortDesc()
            ->take(5);

        $materialGroupTotals = $tenderRows->filter(fn ($row) => !empty($row->material_group))
            ->groupBy('material_group')
            ->map(fn ($group) => round((float) $group->sum('amount'), 2))
            ->sortDesc()
            ->take(6);

        return [
            'rows' => $rows,
            'paginatedRows' => $paginatedRows,
            'purchaseTotal' => $purchaseTotal,
            'tenderTotal' => $tenderTotal,
            'icbTotal' => $icbTotal,
            'ncbTotal' => $ncbTotal,
            'grandTotal' => $purchaseTotal + $tenderTotal,
            'purchaseCount' => $purchaseRows->count(),
            'tenderCount' => $tenderRows->count(),
            'icbCount' => $tenderRows->where('type', 'ICB')->count(),
            'ncbCount' => $tenderRows->where('type', 'NCB')->count(),
            'chartShare' => [
                'purchase' => round($purchaseTotal, 2),
                'tender' => round($tenderTotal, 2),
            ],
            'chartMonthly' => [
                'labels' => $monthlyLabels,
                'purchase' => $monthlyPurchase,
                'tender' => $monthlyTender,
            ],
            'chartSuppliers' => [
                'labels' => $supplierTotals->keys()->values()->all(),
                'amounts' => $supplierTotals->values()->all(),
            ],
            'chartMaterialGroups' => [
                'labels' => $materialGroupTotals->keys()->values()->all(),
                'amounts' => $materialGroupTotals->values()->all(),
            ],
            'from' => $request->from,
            'to' => $request->to,
            'category' => $category,
        ];
    }
}
