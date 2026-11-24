<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Driver;
use App\Models\User;
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

        $drivers = $query->latest()->paginate(10);

        $modalDrivers = Driver::orderBy('name')->get();

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
            'drivers', 'modalDrivers', 'totalDrivers', 'activeDrivers', 'inactiveDrivers',
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

    public function history(Driver $driver)
    {
        $logs = ActivityLog::where('driver_id', $driver->id)->with('actor')->latest()->get();
        $hasCreateLog = $logs->contains('activity_type', ActivityLog::TYPE_DRIVER_CREATE);
        $creator = ! $hasCreateLog && $driver->created_by ? User::find($driver->created_by) : null;
        $gives = $driver->tireGives()->with(['tire', 'givenBy'])->get();
        $returns = $driver->tireReturns()->with(['tire', 'returnedBy'])->get();
        $events = [];

        if ($driver->created_at && ! $hasCreateLog) {
            $events[] = [
                'id' => 'driver-'.$driver->id,
                'type' => 'registered',
                'date' => $driver->created_at->format('Y-m-d'),
                'timestamp' => $driver->created_at->format('Y-m-d H:i:s'),
                'title' => 'Driver registered',
                'tire' => null,
                'tire_type' => null,
                'meta' => [],
                'user' => $creator?->name,
                'notes' => null,
            ];
        }

        foreach ($logs as $log) {
            $events[] = $this->driverAuditEvent($log);
        }

        foreach ($gives as $give) {
            $events[] = [
                'id' => 'give-'.$give->id,
                'type' => 'given',
                'date' => $give->give_date?->format('Y-m-d') ?? $give->created_at?->format('Y-m-d'),
                'timestamp' => $give->created_at?->format('Y-m-d H:i:s')
                    ?? $give->give_date?->format('Y-m-d 00:00:00'),
                'title' => 'Tire assigned',
                'tire' => $give->tire?->serial_number ?? 'Unknown tire',
                'tire_type' => $give->tire?->tire_type,
                'meta' => [
                    'KM at issue' => $give->given_kilometer,
                    'Plate' => $give->plate_number,
                    'Status' => $give->is_returned ? 'Returned' : 'In use',
                ],
                'user' => $give->givenBy?->name,
                'notes' => $give->notes,
            ];
        }

        foreach ($returns as $return) {
            $events[] = [
                'id' => 'return-'.$return->id,
                'type' => 'returned',
                'date' => $return->return_date?->format('Y-m-d') ?? $return->created_at?->format('Y-m-d'),
                'timestamp' => $return->created_at?->format('Y-m-d H:i:s')
                    ?? $return->return_date?->format('Y-m-d 00:00:00'),
                'title' => 'Tire returned',
                'tire' => $return->serial_number ?? $return->tire?->serial_number ?? 'Unknown tire',
                'tire_type' => $return->tire_type ?? $return->tire?->tire_type,
                'meta' => [
                    'Current KM' => $return->current_kilometer,
                    'Distance' => $return->km_difference,
                    'Plate' => $return->plate_number,
                ],
                'user' => $return->returnedBy?->name,
                'notes' => $return->notes,
            ];
        }

        $events = collect($events)
            ->sortByDesc(fn (array $event) => $event['timestamp'] ?? '')
            ->values()
            ->all();

        return response()->json([
            'driver' => [
                'name' => $driver->name,
                'status' => $driver->status ? 'Active' : 'Inactive',
            ],
            'summary' => [
                'changes' => $logs->count(),
                'assignments' => $gives->count(),
                'active' => $gives->where('is_returned', false)->count(),
                'returns' => $returns->count(),
            ],
            'history' => $events,
        ]);
    }

    private function driverAuditEvent(ActivityLog $log): array
    {
        $type = match ($log->activity_type) {
            ActivityLog::TYPE_DRIVER_CREATE => 'created',
            ActivityLog::TYPE_DRIVER_STATUS => 'status',
            ActivityLog::TYPE_DRIVER_DELETE => 'deleted',
            default => 'updated',
        };

        $title = match ($log->activity_type) {
            ActivityLog::TYPE_DRIVER_CREATE => 'Driver registered',
            ActivityLog::TYPE_DRIVER_STATUS => 'Status changed',
            ActivityLog::TYPE_DRIVER_DELETE => 'Driver deleted',
            default => $this->driverFieldLabel($log->field_name).' changed',
        };

        $meta = [];

        if (in_array($log->activity_type, [ActivityLog::TYPE_DRIVER_CREATE, ActivityLog::TYPE_DRIVER_DELETE], true)) {
            $value = $log->activity_type === ActivityLog::TYPE_DRIVER_CREATE ? $log->new_value : $log->old_value;
            $snapshot = json_decode($value ?? '', true);

            if (is_array($snapshot)) {
                foreach ($snapshot as $field => $snapshotValue) {
                    $formatted = $this->formatDriverAuditValue((string) $field, $snapshotValue);
                    if ($formatted !== null) {
                        $meta[$this->driverFieldLabel((string) $field)] = $formatted;
                    }
                }
            } elseif ($value) {
                $meta['Details'] = $value;
            }
        } else {
            $meta['Before'] = $this->formatDriverAuditValue($log->field_name, $log->old_value);
            $meta['After'] = $this->formatDriverAuditValue($log->field_name, $log->new_value);
        }

        return [
            'id' => 'activity-'.$log->id,
            'type' => $type,
            'date' => $log->created_at?->format('Y-m-d'),
            'timestamp' => $log->created_at?->format('Y-m-d H:i:s'),
            'title' => $title,
            'tire' => null,
            'tire_type' => null,
            'meta' => $meta,
            'user' => $log->edited_by ?: $log->actor?->name,
            'notes' => $log->reason,
        ];
    }

    private function driverFieldLabel(?string $field): string
    {
        return match ($field) {
            'name' => 'Name',
            'license_number' => 'License number',
            'phone' => 'Phone',
            'email' => 'Email',
            'photo' => 'Photo',
            'license_expiry' => 'License expiry',
            'status' => 'Status',
            'driver' => 'Driver details',
            default => ucfirst(str_replace('_', ' ', $field ?? 'details')),
        };
    }

    private function formatDriverAuditValue(?string $field, $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($field === 'status') {
            if (in_array($value, ['Active', 'Inactive'], true)) {
                return $value;
            }

            return in_array($value, [true, 1, '1', 'true'], true) ? 'Active' : 'Inactive';
        }

        if ($field === 'license_expiry') {
            return substr((string) $value, 0, 10);
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        return is_scalar($value) ? (string) $value : null;
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

    public function updateStatus(Request $request, Driver $driver)
    {
        $user = auth()->user();
        if (! $user->canEditDrivers()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to update driver status.');
        }

        $validated = $request->validate([
            'status' => 'required|boolean',
        ]);

        $driver->update(['status' => $validated['status']]);

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
