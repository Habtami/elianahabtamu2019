<?php

namespace App\Http\Controllers;

use App\Models\Tire;
use App\Models\TireGive;
use App\Models\TireReturn;
use App\Models\Driver;
use App\Helpers\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TireReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user->canViewTireReports()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view tire reports.');
        }

        // Date range handling
        $dateFrom = $request->get('date_from') ? Carbon::parse($request->get('date_from'))->startOfDay() : now()->subMonths(5)->startOfMonth();
        $dateTo = $request->get('date_to') ? Carbon::parse($request->get('date_to'))->endOfDay() : now()->endOfDay();

        // Statistics
        $totalTires = Tire::count();
        $activeTires = Tire::where('is_active', true)->count();
        $givenOutTires = Tire::whereHas('tireGives', function ($q) {
            $q->where('is_returned', false);
        })->count();
        $returnedToday = TireReturn::whereDate('return_date', now())->count();
        $givenToday = TireGive::whereDate('give_date', now())->count();

        // Tire type distribution
        $tireTypeDistribution = Tire::select('tire_type', DB::raw('count(*) as count'))
            ->groupBy('tire_type')
            ->orderByDesc('count')
            ->get();

        // Monthly give/return (dynamic range based on date filter)
        $monthsDiff = $dateFrom->diffInMonths($dateTo) + 1;
        $startDate = $dateFrom->copy()->startOfMonth();
        $endDate = $dateTo->copy()->endOfMonth();

        $monthlyGives = TireGive::select(DB::raw("DATE_FORMAT(give_date, '%Y-%m') as month"), DB::raw('count(*) as count'))
            ->whereBetween('give_date', [$startDate, $endDate])
            ->groupBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        $monthlyReturns = TireReturn::select(DB::raw("DATE_FORMAT(return_date, '%Y-%m') as month"), DB::raw('count(*) as count'))
            ->whereBetween('return_date', [$startDate, $endDate])
            ->groupBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        $monthlyData = collect();
        $current = clone $startDate;
        while ($current <= $endDate) {
            $monthKey = $current->format('Y-m');
            $monthlyData->push((object)[
                'month' => $current->format('M Y'),
                'gives' => $monthlyGives[$monthKey] ?? 0,
                'returns' => $monthlyReturns[$monthKey] ?? 0,
            ]);
            $current->addMonth();
        }

        // Top drivers with more metrics
        $topDrivers = Driver::withCount([
            'tireGives as active_gives' => function ($q) {
                $q->where('is_returned', false);
            },
            'tireGives as total_gives' => function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('give_date', [$dateFrom, $dateTo]);
            },
            'tireGives as returned_gives' => function ($q) use ($dateFrom, $dateTo) {
                $q->where('is_returned', true)->whereBetween('give_date', [$dateFrom, $dateTo]);
            }
        ])->having('active_gives', '>', 0)
          ->orderBy('active_gives', 'desc')
          ->take(15)
          ->get();

        // Driver performance metrics
        $driverPerformance = Driver::withCount([
            'tireGives as total_gives' => function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('give_date', [$dateFrom, $dateTo]);
            },
            'tireGives as returned_count' => function ($q) use ($dateFrom, $dateTo) {
                $q->where('is_returned', true)->whereBetween('give_date', [$dateFrom, $dateTo]);
            }
        ])->get()
        ->map(function ($d) {
            $d->return_rate = $d->total_gives > 0 ? round(($d->returned_count / $d->total_gives) * 100, 1) : 0;
            return $d;
        })
        ->filter(fn($d) => $d->total_gives > 0)
        ->sortByDesc('return_rate')
        ->take(10)
        ->values();

        // Recent activities (paginated)
        $recentGives = TireGive::with(['tire', 'driver', 'givenBy'])
            ->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('give_date', [$dateFrom, $dateTo]))
            ->latest()->paginate(10, ['*'], 'gives_page')->appends($request->query());

        $recentReturns = TireReturn::with(['tire', 'driver', 'returnedBy', 'tireGive.driver'])
            ->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('return_date', [$dateFrom, $dateTo]))
            ->latest()->paginate(10, ['*'], 'returns_page')->appends($request->query());

        // KM Analysis
        $kmStats = TireReturn::whereBetween('return_date', [$dateFrom, $dateTo])
            ->select(
                DB::raw('AVG(km_difference) as avg_km_diff'),
                DB::raw('MIN(km_difference) as min_km_diff'),
                DB::raw('MAX(km_difference) as max_km_diff'),
                DB::raw('SUM(km_difference) as total_km_diff'),
                DB::raw('COUNT(*) as return_count')
            )->first();

        // Tire utilization by type
        $inUseByType = TireGive::where('is_returned', false)
            ->join('tires', 'tire_gives.tire_id', '=', 'tires.id')
            ->select('tires.tire_type', DB::raw('count(*) as in_use'))
            ->groupBy('tires.tire_type')
            ->pluck('in_use', 'tire_type')
            ->toArray();

        $tireUtilization = Tire::select('tire_type', DB::raw('count(*) as total'))
            ->groupBy('tire_type')
            ->get()
            ->map(fn($t) => [
                'type' => $t->tire_type,
                'total' => $t->total,
                'in_use' => $inUseByType[$t->tire_type] ?? 0,
                'available' => $t->total - ($inUseByType[$t->tire_type] ?? 0),
                'utilization' => $t->total > 0 ? round((($inUseByType[$t->tire_type] ?? 0) / $t->total) * 100, 1) : 0
            ]);

        // All tires for modals
        $allTires = Tire::orderBy('serial_number')->get();
        $activeTireList = Tire::where('is_active', true)->orderBy('serial_number')->get();
        $givenOutTiresList = TireGive::where('is_returned', false)->with(['tire', 'driver'])->latest()->get();
        $givenTodayList = TireGive::whereDate('give_date', today())->with(['tire', 'driver'])->latest()->get();
        $returnedTodayList = TireReturn::whereDate('return_date', today())->with(['tire', 'driver', 'tireGive.driver'])->latest()->get();

        // Return rate overall
        $totalGivesInPeriod = TireGive::whereBetween('give_date', [$dateFrom, $dateTo])->count();
        $totalReturnsInPeriod = TireReturn::whereBetween('return_date', [$dateFrom, $dateTo])->count();
        $overallReturnRate = $totalGivesInPeriod > 0 ? round(($totalReturnsInPeriod / $totalGivesInPeriod) * 100, 1) : 0;

        // Average days in use
        $avgDaysInUse = TireGive::where('is_returned', true)
            ->whereBetween('give_date', [$dateFrom, $dateTo])
            ->whereHas('tireReturn')
            ->get()
            ->avg(function ($give) {
                return $give->tireReturn->return_date->diffInDays($give->give_date);
            });

        return view('tires.reports', compact(
            'totalTires', 'activeTires', 'givenOutTires', 'returnedToday', 'givenToday',
            'tireTypeDistribution', 'monthlyData', 'topDrivers', 'recentGives', 'recentReturns',
            'allTires', 'activeTireList', 'givenOutTiresList', 'givenTodayList', 'returnedTodayList',
            'driverPerformance', 'kmStats', 'tireUtilization', 'overallReturnRate', 'avgDaysInUse',
            'dateFrom', 'dateTo', 'totalGivesInPeriod', 'totalReturnsInPeriod'
        ));
    }

    public function exportCsv()
    {
        $tires = Tire::all();

        $excel = new ExcelExport(['ID', 'Tire Type', 'Serial Number', 'Plate Number', 'Initial KM', 'Status', 'Created At'], 'Tires');

        foreach ($tires as $tire) {
            $excel->addRow([
                $tire->id,
                $tire->tire_type,
                $tire->serial_number,
                $tire->plate_number ?? '-',
                $tire->initial_kilometer,
                $tire->is_active ? 'Active' : 'Inactive',
                $tire->created_at->format('Y-m-d'),
            ]);
        }

        $excel->download('tires_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportDriversCsv()
    {
        $drivers = Driver::all();

        $excel = new ExcelExport(['ID', 'Name', 'License', 'Phone', 'Email', 'License Expiry', 'Status'], 'Drivers');

        foreach ($drivers as $driver) {
            $excel->addRow([
                $driver->id,
                $driver->name,
                $driver->license_number,
                $driver->phone ?? '-',
                $driver->email ?? '-',
                $driver->license_expiry ? $driver->license_expiry->format('Y-m-d') : '-',
                $driver->status ? 'Active' : 'Inactive',
            ]);
        }

        $excel->download('drivers_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportReportCsv(Request $request)
    {
        $dateFrom = $request->get('date_from') ? Carbon::parse($request->get('date_from'))->startOfDay() : now()->subMonths(5)->startOfMonth();
        $dateTo = $request->get('date_to') ? Carbon::parse($request->get('date_to'))->endOfDay() : now()->endOfDay();

        $type = $request->get('type', 'gives');

        if ($type === 'gives') {
            $data = TireGive::with(['tire', 'driver', 'givenBy'])
                ->whereBetween('give_date', [$dateFrom, $dateTo])
                ->latest()->get();

            $excel = new ExcelExport([
                'Date', 'Tire Type', 'Serial Number', 'Plate Number',
                'Driver', 'KM at Give', 'Given By', 'Status', 'Return Date'
            ], 'Tire Gives Report');

            foreach ($data as $give) {
                $excel->addRow([
                    $give->give_date->format('Y-m-d'),
                    $give->tire->tire_type,
                    $give->tire->serial_number,
                    $give->tire->plate_number ?? '-',
                    $give->driver->name,
                    $give->given_kilometer,
                    $give->givenBy->name ?? 'N/A',
                    $give->is_returned ? 'Returned' : 'In Use',
                    $give->tireReturn ? $give->tireReturn->return_date->format('Y-m-d') : '-',
                ]);
            }

            $excel->download('tire-gives-report_' . now()->format('Y-m-d') . '.xlsx');
        } elseif ($type === 'returns') {
            $data = TireReturn::with(['tire', 'driver', 'returnedBy', 'tireGive.driver'])
                ->whereBetween('return_date', [$dateFrom, $dateTo])
                ->latest()->get();

            $excel = new ExcelExport([
                'Date', 'Tire Type', 'Serial Number', 'Plate Number',
                'Driver', 'Current KM', 'Registered KM', 'KM Difference',
                'Returned By', 'Days in Use'
            ], 'Tire Returns Report');

            foreach ($data as $ret) {
                $daysInUse = $ret->tireGive ? $ret->tireGive->give_date->diffInDays($ret->return_date) : '-';
                $excel->addRow([
                    $ret->return_date->format('Y-m-d'),
                    $ret->tire->tire_type,
                    $ret->serial_number,
                    $ret->tire->plate_number ?? '-',
                    $ret->driver->name ?? 'N/A',
                    $ret->current_kilometer,
                    $ret->registered_kilometer,
                    $ret->km_difference,
                    $ret->returnedBy->name ?? 'N/A',
                    $daysInUse,
                ]);
            }

            $excel->download('tire-returns-report_' . now()->format('Y-m-d') . '.xlsx');
        } elseif ($type === 'tires') {
            $data = Tire::orderBy('serial_number')->get();

            $excel = new ExcelExport([
                'ID', 'Tire Type', 'Serial Number', 'Plate Number',
                'Initial KM', 'Current KM', 'Status', 'Created At'
            ], 'Tire Inventory Report');

            foreach ($data as $tire) {
                $excel->addRow([
                    $tire->id,
                    $tire->tire_type,
                    $tire->serial_number,
                    $tire->plate_number ?? '-',
                    $tire->initial_kilometer,
                    $tire->current_kilometer ?? '-',
                    $tire->is_active ? 'Active' : 'Inactive',
                    $tire->created_at->format('Y-m-d'),
                ]);
            }

            $excel->download('tire-inventory-report_' . now()->format('Y-m-d') . '.xlsx');
        }
    }
}