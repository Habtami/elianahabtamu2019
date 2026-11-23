<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\MaterialGroup;
use App\Models\PurchasingActivity;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PurchasingActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->canViewPurchasingActivities()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view purchasing activities.');
        }

        $query = PurchasingActivity::with(['creator', 'completer']);

        if ($request->filled('status') && array_key_exists($request->status, PurchasingActivity::getStatuses())) {
            $query->where('status', $request->status);
        }

        if ($request->filled('document_type') && array_key_exists($request->document_type, PurchasingActivity::getDocumentTypes())) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('procurement_type') && array_key_exists($request->procurement_type, PurchasingActivity::getProcurementTypes())) {
            $query->where('procurement_type', $request->procurement_type);
        }

        if ($request->filled('material_group') && in_array($request->material_group, Contract::getMaterialGroups(), true)) {
            $query->where('material_group', $request->material_group);
        }

        if ($request->filled('from')) {
            $query->whereDate('request_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('request_date', '<=', $request->to);
        }

        $activities = $query->orderByDesc('request_date')->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        $total = PurchasingActivity::count();
        $pending = PurchasingActivity::where('status', PurchasingActivity::STATUS_PENDING)->count();
        $complete = PurchasingActivity::where('status', PurchasingActivity::STATUS_COMPLETE)->count();
        $today = PurchasingActivity::whereDate('request_date', now()->toDateString())->count();

        $materialGroups = Contract::getMaterialGroups();
        $units = Unit::activeNames();
        $statuses = PurchasingActivity::getStatuses();
        $documentTypes = PurchasingActivity::getDocumentTypes();
        $procurementTypes = PurchasingActivity::getProcurementTypes();

        return view('purchasing-activities.index', compact(
            'activities', 'total', 'pending', 'complete', 'today',
            'materialGroups', 'units', 'statuses', 'documentTypes', 'procurementTypes'
        ));
    }

    public function summary(string $scope)
    {
        $user = auth()->user();

        if (!$user->canViewPurchasingActivities()) {
            return response()->json(['message' => 'You are not authorized to view purchasing activities.'], 403);
        }

        $query = PurchasingActivity::with(['creator', 'completer']);

        switch ($scope) {
            case 'pending':
                $query->where('status', PurchasingActivity::STATUS_PENDING);
                $label = 'Pending Activities';
                break;
            case 'complete':
                $query->where('status', PurchasingActivity::STATUS_COMPLETE);
                $label = 'Complete Activities';
                break;
            case 'today':
                $query->whereDate('request_date', now()->toDateString());
                $label = "Today's Requests";
                break;
            default:
                $label = 'Total Activities';
                break;
        }

        $total = (clone $query)->count();

        $items = $query->orderByDesc('request_date')
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->map(fn (PurchasingActivity $activity) => [
                'date' => $activity->request_date?->format('M d, Y'),
                'document' => $activity->getDocumentTypeLabel(),
                'material_group' => $activity->material_group,
                'remarks' => $activity->remarks,
                'unit' => $activity->unit,
                'procurement_type' => $activity->getProcurementTypeLabel(),
                'status' => $activity->getStatusLabel(),
                'is_complete' => $activity->isComplete(),
                'creator' => $activity->creator->name ?? 'Unknown',
                'completed_by' => $activity->completer->name ?? null,
                'completed_at' => $activity->completed_at?->format('M d, Y H:i'),
            ]);

        return response()->json([
            'scope' => $scope,
            'label' => $label,
            'total' => $total,
            'items' => $items,
            'limited' => $total > $items->count(),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->canCreatePurchasingActivities()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to create purchasing activities.');
        }

        $validated = $request->validate([
            'request_date' => 'required|date',
            'document_type' => ['required', Rule::in(array_keys(PurchasingActivity::getDocumentTypes()))],
            'material_group' => ['required', 'string', Rule::in(Contract::getMaterialGroups())],
            'unit' => ['required', 'string', 'max:50', Rule::in(Unit::activeNames())],
            'procurement_type' => ['required', Rule::in(array_keys(PurchasingActivity::getProcurementTypes()))],
            'remarks' => 'nullable|string|max:1000',
        ]);

        PurchasingActivity::create([
            'request_date' => $validated['request_date'],
            'document_type' => $validated['document_type'],
            'material_group' => $validated['material_group'],
            'unit' => trim($validated['unit']),
            'procurement_type' => $validated['procurement_type'],
            'remarks' => filled($validated['remarks'] ?? null) ? trim($validated['remarks']) : null,
            'status' => PurchasingActivity::STATUS_PENDING,
            'created_by' => $user->id,
        ]);

        return redirect()->route('purchasing-activities.index')
            ->with('success', 'Purchasing activity recorded successfully.');
    }

    public function storeMaterialGroup(Request $request)
    {
        $user = auth()->user();

        if (!$user->canCreatePurchasingActivities()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You are not authorized to register material groups.'], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to register material groups.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
        ], [
            'name.required' => 'Enter the material group name.',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $validated['name']));

        $group = MaterialGroup::firstOrCreate(
            ['name' => $name],
            [
                'is_active' => true,
                'sort_order' => ((int) MaterialGroup::max('sort_order')) + 1,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'name' => $group->name,
                'created' => $group->wasRecentlyCreated,
                'message' => $group->wasRecentlyCreated
                    ? 'Material group registered.'
                    : 'That material group is already registered.',
            ], $group->wasRecentlyCreated ? 201 : 200);
        }

        return back()->with('success', 'Material group registered.');
    }

    public function storeUnit(Request $request)
    {
        $user = auth()->user();

        if (!$user->canCreatePurchasingActivities()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You are not authorized to register units.'], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to register units.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:50',
        ], [
            'name.required' => 'Enter the unit name.',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $validated['name']));

        $unit = Unit::firstOrCreate(
            ['name' => $name],
            [
                'is_active' => true,
                'sort_order' => ((int) Unit::max('sort_order')) + 1,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'name' => $unit->name,
                'created' => $unit->wasRecentlyCreated,
                'message' => $unit->wasRecentlyCreated
                    ? 'Unit registered.'
                    : 'That unit is already registered.',
            ], $unit->wasRecentlyCreated ? 201 : 200);
        }

        return back()->with('success', 'Unit registered.');
    }

    public function complete(PurchasingActivity $purchasingActivity)
    {
        $user = auth()->user();

        if (!$user->canCompletePurchasingActivities()) {
            return redirect()->route('purchasing-activities.index')
                ->with('error', 'You are not authorized to complete purchasing activities.');
        }

        if ($purchasingActivity->isComplete()) {
            return redirect()->route('purchasing-activities.index')
                ->with('error', 'That purchasing activity is already complete.');
        }

        $purchasingActivity->update([
            'status' => PurchasingActivity::STATUS_COMPLETE,
            'completed_by' => $user->id,
            'completed_at' => now(),
        ]);

        return redirect()->route('purchasing-activities.index')
            ->with('success', 'Purchasing activity marked complete.');
    }
}
