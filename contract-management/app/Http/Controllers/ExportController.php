<?php

namespace App\Http\Controllers;

use App\Exports\DriversExport;
use App\Exports\TiresExport;
use App\Helpers\ExcelExport;
use App\Models\ExportLog;
use App\Models\Driver;
use App\Models\Tire;
use App\Models\TireReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    public function index(Request $request)
    {
        $query = ExportLog::with('user');

        if ($request->filled('export_type')) {
            $query->where('export_type', $request->input('export_type'));
        }

        if ($request->filled('format')) {
            $query->where('format', $request->input('format'));
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('exports.index', compact('logs'));
    }

    public function drivers(Request $request)
    {
        $query = Driver::query();
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('license_number', 'like', "%{$search}%");
            });
        }

        $drivers = $query->get();

        $exportLog = ExportLog::create([
            'user_id' => Auth::id(),
            'export_type' => ExportLog::EXPORT_TYPE_DRIVERS,
            'format' => ExportLog::FORMAT_EXCEL,
            'records_count' => $drivers->count(),
        ]);

        return (new DriversExport($drivers))->download('drivers_export.xlsx');
    }

    public function tires(Request $request)
    {
        $query = Tire::query();
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $tires = $query->get();

        ExportLog::create([
            'user_id' => Auth::id(),
            'export_type' => ExportLog::EXPORT_TYPE_TIRES,
            'format' => ExportLog::FORMAT_EXCEL,
            'records_count' => $tires->count(),
        ]);

        return (new TiresExport($tires))->download('tires_export.xlsx');
    }

    public function returns(Request $request)
    {
        $query = TireReturn::with(['tire', 'driver']);
        if ($request->filled('date_from')) {
            $query->whereDate('return_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('return_date', '<=', $request->input('date_to'));
        }

        $returns = $query->get();

        $exportLog = ExportLog::create([
            'user_id' => Auth::id(),
            'export_type' => ExportLog::EXPORT_TYPE_RETURNS,
            'format' => ExportLog::FORMAT_EXCEL,
            'records_count' => $returns->count(),
        ]);

        $excel = new ExcelExport(
            ['ID', 'Tire ID', 'Driver ID', 'Serial', 'Tire Type', 'Current KM', 'Given KM', 'KM Difference', 'Return Date'],
            'Returns'
        );

        foreach ($returns as $return) {
            $excel->addRow([
                $return->id,
                $return->tire_id,
                $return->driver_id,
                $return->serial_number,
                $return->tire_type,
                $return->current_kilometer,
                $return->given_kilometer,
                $return->km_difference,
                $return->return_date ? $return->return_date->format('Y-m-d') : '',
            ]);
        }

        $excel->download('returns_export.xlsx');
    }

    public function logs()
    {
        return view('exports.logs');
    }
}
