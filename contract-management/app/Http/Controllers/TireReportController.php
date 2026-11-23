<?php

namespace App\Http\Controllers;

use App\Models\Tire;
use App\Models\TireGive;
use App\Models\TireReturn;
use App\Models\Driver;
use App\Helpers\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TireReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->canViewTireReports()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view tire reports.');
        }

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
            ->get();

        // Monthly give/return (last 6 months)
        $startDate = now()->subMonths(5)->startOfMonth();
        $monthlyGives = TireGive::select(DB::raw("DATE_FORMAT(give_date, '%Y-%m') as month"), DB::raw('count(*) as count'))
            ->where('give_date', '>=', $startDate)
            ->groupBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        $monthlyReturns = TireReturn::select(DB::raw("DATE_FORMAT(return_date, '%Y-%m') as month"), DB::raw('count(*) as count'))
            ->where('return_date', '>=', $startDate)
            ->groupBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        $monthlyData = collect();
        $current = clone $startDate;
        while ($current <= now()) {
            $monthKey = $current->format('Y-m');
            $monthlyData->push((object)[
                'month' => $current->format('M Y'),
                'gives' => $monthlyGives[$monthKey] ?? 0,
                'returns' => $monthlyReturns[$monthKey] ?? 0,
            ]);
            $current->addMonth();
        }

        // Top drivers
        $topDrivers = Driver::withCount(['tireGives as active_gives' => function ($q) {
            $q->where('is_returned', false);
        }])->orderBy('active_gives', 'desc')->take(10)->get();

        // Recent activities
        $recentGives = TireGive::with(['tire', 'driver', 'givenBy'])->latest()->take(10)->get();
        $recentReturns = TireReturn::with(['tire', 'driver', 'returnedBy', 'tireGive.driver'])->latest()->take(10)->get();

        // All tires for the Total Tires modal
        $allTires = Tire::orderBy('serial_number')->get();

        // Active tires for the Active Tires modal
        $activeTireList = Tire::where('is_active', true)->orderBy('serial_number')->get();

        // Given out (not returned) tires for the Given Out modal
        $givenOutTiresList = TireGive::where('is_returned', false)->with(['tire', 'driver'])->latest()->get();

        // Given today for the Given Today modal
        $givenTodayList = TireGive::whereDate('give_date', today())->with(['tire', 'driver'])->latest()->get();

        // Returned today for the Returned Today modal
        $returnedTodayList = TireReturn::whereDate('return_date', today())->with(['tire', 'driver', 'tireGive.driver'])->latest()->get();

        return view('tires.reports', compact(
            'totalTires', 'activeTires', 'givenOutTires', 'returnedToday', 'givenToday',
            'tireTypeDistribution', 'monthlyData', 'topDrivers', 'recentGives', 'recentReturns',
            'allTires', 'activeTireList', 'givenOutTiresList', 'givenTodayList', 'returnedTodayList'
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
}