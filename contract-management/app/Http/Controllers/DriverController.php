<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('license_number', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('status', true);
            } elseif ($request->status === 'inactive') {
                $query->where('status', false);
            } elseif ($request->status === 'license_expiring') {
                $query->whereNotNull('license_expiry')
                    ->whereDate('license_expiry', '>', now())
                    ->whereDate('license_expiry', '<=', now()->addDays(30));
            } elseif ($request->status === 'license_expired') {
                $query->whereNotNull('license_expiry')
                    ->whereDate('license_expiry', '<', now());
            }
        }

        $drivers = $query->withCount(['tireGives as active_tires_count' => function ($q) {
            $q->where('is_returned', false);
        }])
            ->latest()
            ->paginate(10);

        $totalDrivers = Driver::count();
        $activeDrivers = Driver::where('status', true)->count();
        $inactiveDrivers = Driver::where('status', false)->count();
        $licenseExpiring = Driver::whereNotNull('license_expiry')
            ->whereDate('license_expiry', '>', now())
            ->whereDate('license_expiry', '<=', now()->addDays(30))
            ->count();
        $licenseExpired = Driver::whereNotNull('license_expiry')
            ->whereDate('license_expiry', '<', now())
            ->count();

        return view('drivers.index', compact(
            'drivers', 'totalDrivers', 'activeDrivers', 'inactiveDrivers',
            'licenseExpiring', 'licenseExpired'
        ));
    }

    public function create()
    {
        $user = auth()->user();
        if (! $user->canEditDrivers()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to create drivers.');
        }

        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (! $user->canEditDrivers()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to create drivers.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'license_expiry' => 'nullable|date',
            'status' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('drivers', 'public');
        }

        $validated['status'] = $request->has('status');
        $validated['created_by'] = $user->id;

        Driver::create($validated);

        return redirect()->route('drivers.index')
            ->with('success', 'Driver created successfully.');
    }

    public function show(Driver $driver)
    {
        $tireGives = $driver->tireGives()->with(['tire', 'givenBy', 'tireReturn'])->latest()->get();
        $tireReturns = $driver->tireReturns()->with(['tire', 'returnedBy', 'tireGive.tire'])->latest()->get();
        $activeTires = $tireGives->where('is_returned', false)->count();
        $totalTires = $tireGives->count();

        return view('drivers.show', compact('driver', 'tireGives', 'tireReturns', 'activeTires', 'totalTires'));
    }

    public function edit(Driver $driver)
    {
        $user = auth()->user();
        if (! $user->canEditDrivers()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to edit drivers.');
        }

        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $user = auth()->user();
        if (! $user->canEditDrivers()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to edit drivers.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'license_expiry' => 'nullable|date',
            'status' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            if ($driver->photo) {
                Storage::disk('public')->delete($driver->photo);
            }
            $validated['photo'] = $request->file('photo')->store('drivers', 'public');
        }

        $validated['status'] = $request->has('status');

        $driver->update($validated);

        return redirect()->route('drivers.index')
            ->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver)
    {
        $user = auth()->user();
        if (! $user->canEditDrivers()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to delete drivers.');
        }

        if ($driver->photo) {
            Storage::disk('public')->delete($driver->photo);
        }

        $driver->delete();

        return redirect()->route('drivers.index')
            ->with('success', 'Driver deleted successfully.');
    }

    public function toggleStatus(Driver $driver)
    {
        $user = auth()->user();
        if (! $user->canEditDrivers()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to update driver status.');
        }

        $driver->update(['status' => ! $driver->status]);

        return back()->with('success', 'Driver status updated successfully.');
    }

    public function quickStore(Request $request)
    {
        $user = auth()->user();
        if (! $user->canPerformTireOperations() && ! $user->canEditDrivers()) {
            return response()->json(['message' => 'You are not authorized to register drivers.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
        ]);

        $name = trim($validated['name']);
        $driver = Driver::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();

        if (! $driver) {
            $driver = Driver::create([
                'name' => $name,
                'phone' => $validated['phone'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'status' => true,
                'created_by' => $user->id,
            ]);
        }

        return response()->json([
            'id' => $driver->id,
            'name' => $driver->name,
            'phone' => $driver->phone,
        ]);
    }

    public function searchDrivers(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        $drivers = Driver::where('name', 'like', '%'.$query.'%')
            ->orWhere('license_number', 'like', '%'.$query.'%')
            ->limit(10)
            ->get(['id', 'name', 'license_number', 'phone']);

        return response()->json($drivers);
    }
}
