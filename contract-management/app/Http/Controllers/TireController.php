<?php

namespace App\Http\Controllers;

use App\Exports\TireReturnsExport;
use App\Models\Driver;
use App\Models\ExportLog;
use App\Models\Tire;
use App\Models\TireGive;
use App\Models\TireReturn;
use App\Models\TireType;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TireController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Tire::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', '%'.$search.'%')
                    ->orWhere('tire_type', 'like', '%'.$search.'%')
                    ->orWhere('plate_number', 'like', '%'.$search.'%');
            });
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'in_use') {
                $query->whereHas('tireGives', function ($q) {
                    $q->where('is_returned', false);
                });
            } elseif ($request->status === 'returned') {
                $query->whereHas('tireGives', function ($q) {
                    $q->where('is_returned', true);
                })->whereDoesntHave('tireGives', function ($q) {
                    $q->where('is_returned', false);
                });
            }
        }

        // Filter by tire type
        if ($request->filled('tire_type')) {
            $query->where('tire_type', $request->tire_type);
        }

        $tires = $query->with(['tireGives.givenBy', 'tireGives.driver', 'tireReturns'])->latest()->get();

        $tireTypes = Tire::getTireTypes();
        $totalTires = Tire::count();
        $activeTires = Tire::where('is_active', true)->count();
        $inactiveTires = Tire::where('is_active', false)->count();
        $givenOutTires = Tire::whereHas('tireGives', function ($q) {
            $q->where('is_returned', false);
        })->count();
        $returnedTires = Tire::whereHas('tireGives', function ($q) {
            $q->where('is_returned', true);
        })->whereDoesntHave('tireGives', function ($q) {
            $q->where('is_returned', false);
        })->count();
        $activeDriversCount = Driver::active()->count();
        $tiresOutCount = $givenOutTires;
        $driversList = Driver::active()->orderBy('name')->get(['id', 'name', 'phone']);

        return view('tires.index', compact(
            'tires', 'tireTypes', 'totalTires', 'activeTires', 'inactiveTires', 'givenOutTires',
            'returnedTires', 'activeDriversCount', 'tiresOutCount', 'driversList'
        ));
    }

    public function summary(Request $request, string $scope)
    {
        $user = auth()->user();

        if (! $user->canAccessTireTracking()) {
            return response()->json(['message' => 'You are not authorized to view tire details.'], 403);
        }

        switch ($scope) {
            case 'total-tires':
                $title = 'Total Tires';
                $items = Tire::with('tireGives.driver')->orderByDesc('id')->limit(100)->get()
                    ->map(fn (Tire $tire) => $this->tireSummaryItem($tire));
                break;

            case 'in-use':
                $title = 'In Use';
                $items = Tire::with('tireGives.driver')
                    ->whereHas('tireGives', fn ($q) => $q->where('is_returned', false))
                    ->orderByDesc('id')->limit(100)->get()
                    ->map(fn (Tire $tire) => $this->tireSummaryItem($tire, 'in_use'));
                break;

            case 'returned':
                $title = 'Returned';
                $items = Tire::with('tireGives.driver')
                    ->whereHas('tireGives', fn ($q) => $q->where('is_returned', true))
                    ->whereDoesntHave('tireGives', fn ($q) => $q->where('is_returned', false))
                    ->orderByDesc('id')->limit(100)->get()
                    ->map(fn (Tire $tire) => $this->tireSummaryItem($tire, 'returned'));
                break;

            case 'active-drivers':
                $title = 'Active Drivers';
                $items = Driver::active()->orderBy('name')->limit(100)->get()
                    ->map(function (Driver $driver) {
                        $out = $driver->tireGives()->where('is_returned', false)->count();

                        return [
                            'title' => $driver->name,
                            'subtitle' => $driver->license_number ? 'License '.$driver->license_number : '—',
                            'meta' => $out.' tire(s) out',
                            'badge' => 'Active',
                            'tone' => 'green',
                            'url' => route('drivers.show', $driver->id),
                        ];
                    });
                break;

            default:
                return response()->json(['message' => 'Unknown summary scope.'], 404);
        }

        return response()->json([
            'title' => $title,
            'count' => $items->count(),
            'items' => $items->values(),
        ]);
    }

    private function tireSummaryItem(Tire $tire, ?string $state = null): array
    {
        $activeGive = $tire->tireGives->firstWhere('is_returned', false);
        $state = $state ?: ($activeGive
            ? 'in_use'
            : ($tire->tireGives->contains('is_returned', true) ? 'returned' : 'available'));

        $meta = [];

        if ($state === 'in_use' && $activeGive) {
            $meta[] = $activeGive->driver->name ?? 'No driver';
            if ($activeGive->give_date) {
                $meta[] = 'Given '.$activeGive->give_date->format('M d, Y');
            }
        }

        if ($tire->plate_number) {
            $meta[] = 'Plate '.$tire->plate_number;
        }

        return [
            'title' => $tire->serial_number,
            'subtitle' => $tire->tire_type,
            'meta' => $meta ? implode(' · ', $meta) : null,
            'badge' => ['in_use' => 'In Use', 'returned' => 'Returned', 'available' => 'Available'][$state],
            'tone' => ['in_use' => 'amber', 'returned' => 'green', 'available' => 'blue'][$state],
            'url' => route('tires.show', $tire),
        ];
    }

    public function create()
    {
        $user = auth()->user();
        if (! $user->canManageTires()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to create tires.');
        }

        $tireTypes = TireType::where('is_active', true)->orderBy('sort_order')->get();

        return view('tires.create', compact('tireTypes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (! $user->canManageTires()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to create tires.');
        }

        $validated = $request->validate([
            'tire_type' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:tires,serial_number',
            'plate_number' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'initial_kilometer' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('tires', 'public');
        }

        $validated['created_by'] = $user->id;
        $validated['is_active'] = $request->has('is_active');

        Tire::create($validated);

        return redirect()->route('tires.index')
            ->with('success', 'Tire registered successfully. Serial number is now locked.');
    }

    public function show(Tire $tire)
    {
        $tireGives = $tire->tireGives()->with(['driver', 'givenBy', 'tireReturn'])->latest()->get();
        $tireReturns = $tire->tireReturns()->with(['driver', 'returnedBy', 'tireGive.driver'])->latest()->get();

        return view('tires.show', compact('tire', 'tireGives', 'tireReturns'));
    }

    public function edit(Tire $tire)
    {
        $user = auth()->user();
        if (! $user->canManageTires()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to edit tires.');
        }

        if ($tire->hasBeenReturned()) {
            return redirect()->route('tires.show', $tire)
                ->with('error', 'This tire has been returned and cannot be edited.');
        }

        $tireTypes = TireType::where('is_active', true)->orderBy('sort_order')->get();

        return view('tires.edit', compact('tire', 'tireTypes'));
    }

    public function update(Request $request, Tire $tire)
    {
        $user = auth()->user();
        if (! $user->canManageTires()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to edit tires.');
        }

        if ($tire->hasBeenReturned()) {
            return redirect()->route('tires.show', $tire)
                ->with('error', 'This tire has been returned and cannot be edited.');
        }

        $validated = $request->validate([
            'tire_type' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:tires,serial_number,'.$tire->id,
            'plate_number' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'initial_kilometer' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('photo')) {
            if ($tire->photo) {
                Storage::disk('public')->delete($tire->photo);
            }
            $validated['photo'] = $request->file('photo')->store('tires', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $tire->update($validated);

        return redirect()->route('tires.index')
            ->with('success', 'Tire updated successfully.');
    }

    public function destroy(Tire $tire)
    {
        $user = auth()->user();
        if (! $user->canManageTires()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to delete tires.');
        }

        if ($tire->photo) {
            Storage::disk('public')->delete($tire->photo);
        }

        $tire->delete();

        return redirect()->route('tires.index')
            ->with('success', 'Tire deleted successfully.');
    }

    public function searchTires(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 1) {
            return response()->json([]);
        }
        $tires = Tire::where('serial_number', 'like', '%'.$query.'%')
            ->orWhere('tire_type', 'like', '%'.$query.'%')
            ->limit(10)
            ->get(['id', 'serial_number', 'tire_type', 'plate_number']);

        return response()->json($tires);
    }

    public function checkSerial(Request $request)
    {
        $serial = $request->get('serial', '');
        if (strlen($serial) < 1) {
            return response()->json(['exists' => false]);
        }
        $exists = Tire::where('serial_number', $serial)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function showApi(Tire $tire)
    {
        $tire->load(['tireGives.givenBy', 'tireGives.driver', 'tireGives.tireReturn', 'tireReturns.returnedBy']);

        return response()->json([
            'id' => $tire->id,
            'serial_number' => $tire->serial_number,
            'tire_type' => $tire->tire_type,
            'plate_number' => $tire->plate_number,
            'initial_kilometer' => $tire->initial_kilometer ?? 0,
            'photo' => $tire->photo ? asset('storage/'.$tire->photo) : null,
            'is_active' => $tire->is_active,
            'created_at' => $tire->created_at ? $tire->created_at->format('M d, Y') : null,
            'is_given_out' => $tire->isGivenOut(),
            'current_give' => $tire->tireGives()->where('is_returned', false)->with(['givenBy', 'driver'])->first(),
            'gives' => $tire->tireGives->map(function ($give) {
                $return = $give->tireReturn;

                return [
                    'id' => $give->id,
                    'driver_name' => $give->driver->name ?? 'N/A',
                    'driver' => $give->driver ? ['name' => $give->driver->name] : null,
                    'given_by' => $give->givenBy->name ?? 'N/A',
                    'givenBy' => $give->givenBy ? ['name' => $give->givenBy->name] : null,
                    'give_date' => $give->give_date ? $give->give_date->format('M d, Y') : null,
                    'given_kilometer' => $give->given_kilometer,
                    'is_returned' => $give->is_returned,
                    'photo' => $give->photo ? asset('storage/'.$give->photo) : null,
                    'return_photo' => $return && $return->photo ? asset('storage/'.$return->photo) : null,
                    'return_date' => $return && $return->return_date ? $return->return_date->format('M d, Y') : null,
                    'current_kilometer' => $return ? $return->current_kilometer : null,
                ];
            }),
        ]);
    }

    public function dashboard()
    {
        $user = auth()->user();
        if (! $user->canViewTireReports()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view the tire dashboard.');
        }

        $startDate = Carbon::now()->subWeek();
        $endDate = Carbon::now();

        $stats = [
            'total_tires' => Tire::count(),
            'total_drivers' => Driver::count(),
            'tires_in_use' => TireGive::where('is_returned', false)->count(),
            'km_difference' => TireReturn::sum('km_difference'),
            'gave_this_week' => TireGive::whereBetween('give_date', [$startDate, $endDate])
                ->with(['tire', 'driver'])
                ->latest('give_date')
                ->get(),
            'returned_this_week' => TireReturn::whereBetween('return_date', [$startDate, $endDate])
                ->with(['tire', 'driver'])
                ->latest('return_date')
                ->get(),
            'recent_gives' => TireGive::with(['tire', 'driver'])->latest()->limit(5)->get(),
            'expiring_licenses' => Driver::whereNotNull('license_expiry')
                ->whereDate('license_expiry', '>', now())
                ->whereDate('license_expiry', '<=', now()->addDays(30))
                ->get(),
        ];

        return view('tires.dashboard', compact('stats'));
    }

    public function history(Tire $tire)
    {
        $gives = TireGive::where('tire_id', $tire->id)
            ->with(['driver', 'givenBy'])
            ->orderBy('give_date', 'desc')
            ->get();

        $returns = TireReturn::where('tire_id', $tire->id)
            ->with(['driver', 'returnedBy'])
            ->orderBy('return_date', 'desc')
            ->get();

        $history = $gives->map(function ($give) {
            return [
                'date' => $give->give_date->format('Y-m-d'),
                'type' => 'given',
                'driver' => $give->driver->name ?? '-',
                'km' => $give->given_kilometer,
                'user' => $give->givenBy->name ?? '-',
            ];
        })->concat($returns->map(function ($return) {
            return [
                'date' => $return->return_date->format('Y-m-d'),
                'type' => 'returned',
                'driver' => $return->driver->name ?? '-',
                'km' => $return->current_kilometer,
                'user' => $return->returnedBy->name ?? '-',
            ];
        }))->sortByDesc('date')->values()->toArray();

        return response()->json([
            'serial_number' => $tire->serial_number,
            'history' => $history,
        ]);
    }

    private const RETURN_SORT_COLUMNS = [
        'roll' => 'tire_gives.id',
        'serial_number' => 'tires.serial_number',
        'tire_specification' => 'tires.tire_type',
        'given_to' => 'drivers.name',
        'plate' => 'tire_gives.plate_number',
        'given_by' => 'given_by_users.name',
        'given_date' => 'tire_gives.give_date',
        'km_at_issue' => 'tire_gives.given_kilometer',
        'days_out' => 'tire_gives.give_date',
    ];

    public function returnList(Request $request)
    {
        $user = auth()->user();
        if (! $user->canPerformTireOperations()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to return tires.');
        }

        [$sort, $direction] = $this->resolveReturnSort($request);

        $tireReturns = $this->returnListQuery($request)
            ->orderBy(self::RETURN_SORT_COLUMNS[$sort], $direction)
            ->paginate(10)
            ->withQueryString();

        // Summary counts stay independent of search/filter so the cards never jump around
        $outstanding = TireGive::where('is_returned', false)
            ->with(['tire', 'driver'])
            ->get(['id', 'tire_id', 'driver_id', 'plate_number', 'give_date']);

        $daysOut = fn (TireGive $give) => $give->give_date
            ? (int) $give->give_date->diffInDays(now())
            : 0;

        $tiresOut = $outstanding->count();
        $driversOut = $outstanding->pluck('driver_id')->filter()->unique()->count();
        $overdueCount = $outstanding->filter(fn (TireGive $give) => $daysOut($give) > 30)->count();
        $averageDays = $tiresOut > 0
            ? (int) round($outstanding->avg(fn (TireGive $give) => $daysOut($give)))
            : 0;
        $recentCount = max($tiresOut - $overdueCount, 0);

        $tireRows = $outstanding
            ->sortByDesc(fn (TireGive $give) => $daysOut($give))
            ->map(fn (TireGive $give) => [
                'serial' => $give->tire?->serial_number ?? '—',
                'type' => $give->tire?->tire_type ?? '—',
                'driver' => $give->driver?->name ?? '—',
                'plate' => $give->plate_number ?: ($give->tire?->plate_number ?? '—'),
                'given' => $give->give_date?->format('Y-m-d') ?? '—',
                'days' => $daysOut($give),
            ])
            ->values();

        $driverRows = $outstanding
            ->filter(fn (TireGive $give) => $give->driver_id !== null)
            ->groupBy('driver_id')
            ->map(function ($group) use ($daysOut) {
                $driver = $group->first()->driver;
                $oldest = $group->sortBy('give_date')->first();

                return [
                    'name' => $driver?->name ?? '—',
                    'license' => $driver?->license_number ?? '—',
                    'phone' => $driver?->phone ?? '—',
                    'tires' => $group->count(),
                    'oldest' => $oldest?->give_date?->format('Y-m-d') ?? '—',
                    'days' => $oldest ? $daysOut($oldest) : 0,
                ];
            })
            ->sortByDesc('days')
            ->values();

        $kpiModal = [
            'tires' => [
                'title' => 'Tires Out',
                'subtitle' => 'Every tire currently issued to a driver',
                'columns' => ['Serial', 'Type', 'Driver', 'Plate', 'Given', 'Days Out'],
                'rows' => $tireRows->map(fn (array $row) => array_values($row))->all(),
            ],
            'drivers' => [
                'title' => 'Drivers',
                'subtitle' => 'Drivers who still hold at least one tire',
                'columns' => ['Driver', 'License', 'Phone', 'Tires', 'Oldest Give', 'Days Out'],
                'rows' => $driverRows->map(fn (array $row) => array_values($row))->all(),
            ],
            'overdue' => [
                'title' => 'Overdue',
                'subtitle' => 'Tires out for more than 30 days',
                'columns' => ['Serial', 'Type', 'Driver', 'Plate', 'Given', 'Days Out'],
                'rows' => $tireRows
                    ->filter(fn (array $row) => $row['days'] > 30)
                    ->map(fn (array $row) => array_values($row))
                    ->values()
                    ->all(),
            ],
            'average' => [
                'title' => 'Avg Days Out',
                'subtitle' => $tiresOut > 0
                    ? 'Average of '.$averageDays.' days across '.$tiresOut.' open '.($tiresOut === 1 ? 'issue' : 'issues')
                    : 'No tires are currently issued',
                'columns' => ['Serial', 'Type', 'Driver', 'Plate', 'Given', 'Days Out'],
                'rows' => $tireRows->map(fn (array $row) => array_values($row))->all(),
            ],
        ];

        return view('tires.return-list', compact(
            'tireReturns', 'sort', 'direction',
            'tiresOut', 'driversOut', 'overdueCount', 'averageDays', 'recentCount',
            'kpiModal'
        ));
    }

    public function exportReturnExcel(Request $request)
    {
        $user = auth()->user();
        if (! $user->canPerformTireOperations()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to export tire returns.');
        }

        [$sort, $direction] = $this->resolveReturnSort($request);
        $gives = $this->returnListQuery($request)
            ->orderBy(self::RETURN_SORT_COLUMNS[$sort], $direction)
            ->get();

        ExportLog::create([
            'user_id' => $user->id,
            'export_type' => ExportLog::EXPORT_TYPE_TIRES,
            'format' => ExportLog::FORMAT_EXCEL,
            'records_count' => $gives->count(),
        ]);

        return (new TireReturnsExport($gives))->download(
            'tire_returns_'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function exportReturnPdf(Request $request)
    {
        $user = auth()->user();
        if (! $user->canPerformTireOperations()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to export tire returns.');
        }

        [$sort, $direction] = $this->resolveReturnSort($request);
        $gives = $this->returnListQuery($request)
            ->orderBy(self::RETURN_SORT_COLUMNS[$sort], $direction)
            ->get();

        ExportLog::create([
            'user_id' => $user->id,
            'export_type' => ExportLog::EXPORT_TYPE_TIRES,
            'format' => ExportLog::FORMAT_PDF,
            'records_count' => $gives->count(),
        ]);

        return Pdf::loadView('tires.return-pdf', [
            'gives' => $gives,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape')
            ->download('tire_returns_'.now()->format('Y-m-d').'.pdf');
    }

    private function resolveReturnSort(Request $request): array
    {
        $sort = array_key_exists((string) $request->query('sort'), self::RETURN_SORT_COLUMNS)
            ? $request->query('sort')
            : 'roll';

        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';

        return [$sort, $direction];
    }

    private function returnListQuery(Request $request)
    {
        $query = TireGive::query()
            ->leftJoin('tires', 'tires.id', '=', 'tire_gives.tire_id')
            ->leftJoin('drivers', 'drivers.id', '=', 'tire_gives.driver_id')
            ->leftJoin('users as given_by_users', 'given_by_users.id', '=', 'tire_gives.given_by')
            ->where('tire_gives.is_returned', false)
            ->select('tire_gives.*')
            ->with(['tire', 'driver', 'givenBy']);

        if ($request->filled('search')) {
            $term = '%'.$request->search.'%';
            $query->where(function ($q) use ($term) {
                $q->where('tires.serial_number', 'like', $term)
                    ->orWhere('drivers.name', 'like', $term)
                    ->orWhere('given_by_users.name', 'like', $term)
                    ->orWhere('tires.tire_type', 'like', $term)
                    ->orWhere('tire_gives.plate_number', 'like', $term)
                    ->orWhere('tires.plate_number', 'like', $term);
            });
        }

        $age = $request->query('age');
        if (in_array($age, ['recent', 'overdue'], true)) {
            $cutoff = now()->subDays(30)->toDateString();
            $age === 'overdue'
                ? $query->where('tire_gives.give_date', '<', $cutoff)
                : $query->where('tire_gives.give_date', '>=', $cutoff);
        }

        return $query;
    }

    public function audit(Request $request)
    {
        $logs = \App\Models\ActivityLog::orderBy('created_at', 'desc')->paginate(25);

        return view('tires.audit', compact('logs'));
    }

    public function label(Tire $tire)
    {
        $currentGive = $tire->tireGives()->where('is_returned', false)->first();

        return view('tires.label', compact('tire', 'currentGive'));
    }

    public function pdf(Tire $tire)
    {
        $currentGive = $tire->tireGives()->where('is_returned', false)->first();
        $history = $tire->tireGives()->with(['driver'])->get()
            ->merge($tire->tireReturns()->with(['driver'])->get());

        return response()->view('tires.pdf', compact('tire', 'currentGive', 'history'))
            ->header('Content-Type', 'text/html');
    }
}
