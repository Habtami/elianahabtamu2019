<?php

namespace App\Http\Controllers;

use App\Models\Tire;
use App\Models\TireGive;
use App\Models\TireReturn;
use App\Models\TireType;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
                $q->where('serial_number', 'like', '%' . $search . '%')
                  ->orWhere('tire_type', 'like', '%' . $search . '%')
                  ->orWhere('plate_number', 'like', '%' . $search . '%');
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

        return view('tires.index', compact(
            'tires', 'tireTypes', 'totalTires', 'activeTires', 'inactiveTires', 'givenOutTires', 'returnedTires'
        ));
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user->canManageTires()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to create tires.');
        }

        $tireTypes = TireType::where('is_active', true)->orderBy('sort_order')->get();
        return view('tires.create', compact('tireTypes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->canManageTires()) {
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
        if (!$user->canManageTires()) {
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
        if (!$user->canManageTires()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to edit tires.');
        }

        if ($tire->hasBeenReturned()) {
            return redirect()->route('tires.show', $tire)
                ->with('error', 'This tire has been returned and cannot be edited.');
        }

        $validated = $request->validate([
            'tire_type' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:tires,serial_number,' . $tire->id,
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
        if (!$user->canManageTires()) {
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
        $tires = Tire::where('serial_number', 'like', '%' . $query . '%')
            ->orWhere('tire_type', 'like', '%' . $query . '%')
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
            'photo' => $tire->photo ? asset('storage/' . $tire->photo) : null,
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
                        'photo' => $give->photo ? asset('storage/' . $give->photo) : null,
                        'return_photo' => $return && $return->photo ? asset('storage/' . $return->photo) : null,
                        'return_date' => $return && $return->return_date ? $return->return_date->format('M d, Y') : null,
                        'current_kilometer' => $return ? $return->current_kilometer : null,
                    ];
                }),
        ]);
    }

    public function dashboard()
    {
        $user = auth()->user();
        if (!$user->canViewTireReports()) {
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
        
        $history = $gives->map(function($give) {
            return [
                'date' => $give->give_date->format('Y-m-d'),
                'type' => 'given',
                'driver' => $give->driver->name ?? '-',
                'km' => $give->given_kilometer,
                'user' => $give->givenBy->name ?? '-',
            ];
        })->concat($returns->map(function($return) {
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

    public function returnList(Request $request)
    {
        $user = auth()->user();
        if (!$user->canPerformTireOperations()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to return tires.');
        }

        $query = TireGive::where('is_returned', false)
            ->with(['tire', 'driver']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('tire', function ($tq) use ($search) {
                    $tq->where('serial_number', 'like', '%' . $search . '%');
                })
                ->orWhereHas('driver', function ($dq) use ($search) {
                    $dq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $gives = $query->orderBy('give_date', 'desc')->get();

        return view('tires.return-list', compact('gives'));
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