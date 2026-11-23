<?php

namespace App\Http\Controllers;

use App\Models\Tire;
use App\Models\TireGive;
use App\Models\TireReturn;
use App\Models\TireType;
use App\Models\Driver;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TireGiveController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        if (!$user->canPerformTireOperations()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to give tires.');
        }

        $drivers = Driver::active()->get();
        $tireTypes = TireType::where('is_active', true)->orderBy('sort_order')->get();
        return view('tires.give', compact('drivers', 'tireTypes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->canPerformTireOperations()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to give tires.');
        }

        $validated = $request->validate([
            'tire_serial' => 'required|string|max:255|unique:tires,serial_number',
            'tire_type' => 'required|string|max:255',
            'driver_name' => 'required|string|max:255',
            'driver_id' => 'nullable|exists:drivers,id',
            'give_date' => 'required|date',
            'given_kilometer' => 'required|integer|min:0',
            'plate_number' => 'required|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string',
        ]);

        $tire = Tire::firstOrCreate(
            ['serial_number' => $validated['tire_serial']],
            [
                'tire_type' => $validated['tire_type'],
                'plate_number' => $validated['plate_number'],
                'initial_kilometer' => $validated['given_kilometer'],
                'is_active' => true,
                'created_by' => $user->id,
            ]
        );

        if ($tire->isGivenOut()) {
            return back()->with('error', 'This tire is currently given out.')
                ->withInput();
        }

        // Resolve driver: use existing driver_id or create new from driver_name
        $driver = null;
        if (!empty($validated['driver_id'])) {
            $driver = Driver::find($validated['driver_id']);
        } else {
            $driver = Driver::firstOrCreate(
                ['name' => $validated['driver_name']],
                [
                    'is_active' => true,
                    'created_by' => $user->id,
                ]
            );
        }

        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('tire-gives', 'public');
        }

        $tireGive = TireGive::create([
            'tire_id' => $tire->id,
            'driver_id' => $driver->id,
            'given_by' => $user->id,
            'give_date' => $validated['give_date'],
            'given_kilometer' => $validated['given_kilometer'],
            'plate_number' => $validated['plate_number'] ?? $tire->plate_number,
            'photo' => $photo,
            'notes' => $validated['notes'] ?? null,
            'is_returned' => false,
            'created_by' => $user->id,
        ]);

        ActivityLog::log(
            ActivityLog::TYPE_CONTRACT_CREATE,
            $tireGive->id,
            'tire_give',
            null,
            $tire->serial_number,
            ActivityLog::SEVERITY_INFO,
            'Tire given to driver: ' . $tireGive->driver->name
        );

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tire given successfully.',
                'tire_id' => $tire->id,
            ]);
        }

        return redirect()->route('tires.show', $tire)
            ->with('success', 'Tire given successfully.');
    }

    public function returnCreate(TireGive $tireGive)
    {
        $user = auth()->user();
        if (!$user->canPerformTireOperations()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to return tires.');
        }

        if ($tireGive->is_returned) {
            return redirect()->route('tires.show', $tireGive->tire)
                ->with('error', 'This tire has already been returned.');
        }

        return view('tires.return', compact('tireGive'));
    }

    public function returnStore(Request $request, TireGive $tireGive)
    {
        $user = auth()->user();
        if (!$user->canPerformTireOperations()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to return tires.');
        }

        if ($tireGive->is_returned) {
            return redirect()->route('tires.show', $tireGive->tire)
                ->with('error', 'This tire has already been returned.');
        }

         $validated = $request->validate([
             'return_date' => 'required|date',
             'current_kilometer' => 'required|integer|min:0|gt:' . ($tireGive->tire->initial_kilometer ?? 0),
             'driver_id' => 'nullable|exists:drivers,id',
             'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
             'notes' => 'nullable|string',
         ]);

         $registeredKm = $tireGive->given_kilometer;
        $kmDifference = TireReturn::calculateKmDifference($validated['current_kilometer'], $registeredKm);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('tire-returns', 'public');
        }

        $tireReturn = TireReturn::create([
            'tire_give_id' => $tireGive->id,
            'tire_id' => $tireGive->tire_id,
            'driver_id' => $validated['driver_id'] ?? $tireGive->driver_id,
            'returned_by' => $user->id,
            'return_date' => $validated['return_date'],
            'current_kilometer' => $validated['current_kilometer'],
            'registered_kilometer' => $registeredKm,
            'km_difference' => $kmDifference,
            'tire_type' => $tireGive->tire->tire_type,
            'plate_number' => $tireGive->plate_number,
            'serial_number' => $tireGive->tire->serial_number,
            'photo' => $validated['photo'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $tireGive->update([
            'is_returned' => true,
            'plate_number' => $validated['plate_number'] ?? $tireGive->plate_number,
        ]);

        ActivityLog::log(
            ActivityLog::TYPE_CONTRACT_UPDATE,
            $tireGive->id,
            'tire_return',
            $tireGive->tire->serial_number,
            'returned',
            ActivityLog::SEVERITY_INFO,
            'Tire returned'
        );

        return redirect()->route('tires.show', $tireGive->tire)
            ->with('success', 'Tire returned successfully.');
    }
}