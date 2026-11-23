<?php

namespace App\Http\Controllers;

use App\Models\TireType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TireTypeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->canManageTires()) {
            abort(403, 'Unauthorized');
        }
        $tireTypes = TireType::orderBy('sort_order')->paginate(25);
        return view('tires.types.index', compact('tireTypes'));
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user->canManageTires()) {
            abort(403, 'Unauthorized');
        }
        return view('tires.types.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->canManageTires()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tire_types,name',
            'code' => 'nullable|string|max:20|unique:tire_types,code',
            'category' => 'nullable|string|max:100',
            'width' => 'nullable|numeric',
            'aspect_ratio' => 'nullable|numeric',
            'rim_diameter' => 'nullable|numeric',
            'load_index' => 'nullable|integer',
            'season' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? TireType::max('sort_order') + 1;

        $tireType = TireType::create($validated);
        $tireType->clearCache();

        return redirect()->route('tires.types.index')->with('success', 'Tire type created successfully.');
    }

    public function edit(TireType $tireType)
    {
        $user = auth()->user();
        if (!$user->canManageTires()) {
            abort(403, 'Unauthorized');
        }
        return view('tires.types.edit', compact('tireType'));
    }

    public function update(Request $request, TireType $tireType)
    {
        $user = auth()->user();
        if (!$user->canManageTires()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tire_types,name,' . $tireType->id,
            'code' => 'nullable|string|max:20|unique:tire_types,code,' . $tireType->id,
            'category' => 'nullable|string|max:100',
            'width' => 'nullable|numeric',
            'aspect_ratio' => 'nullable|numeric',
            'rim_diameter' => 'nullable|numeric',
            'load_index' => 'nullable|integer',
            'season' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $tireType->update($validated);
        $tireType->clearCache();

        return redirect()->route('tires.types.index')->with('success', 'Tire type updated successfully.');
    }

    public function destroy(TireType $tireType)
    {
        $user = auth()->user();
        if (!$user->canManageTires()) {
            abort(403, 'Unauthorized');
        }

        $tireType->delete();
        $tireType->clearCache();

        return back()->with('success', 'Tire type deleted successfully.');
    }
}