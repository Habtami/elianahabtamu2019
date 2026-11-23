<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\User;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function performanceEvaluation()
    {
        $user = auth()->user();

        // Get all suppliers that have at least one contract
        $supplierIdsWithContracts = Contract::whereNotNull('supplier_id')
            ->pluck('supplier_id')
            ->unique();

        // Only users who have created at least one contract may rate suppliers
        $canRate = Contract::where('created_by', $user->id)->exists();

        $suppliers = Supplier::whereIn('id', $supplierIdsWithContracts)
            ->get()
            ->map(function ($supplier) use ($canRate) {
                // Count contracts for this supplier
                $supplier->contracts_count = Contract::where('supplier_id', $supplier->id)->count();
                $supplier->can_rate = $canRate;

                return $supplier;
            });

        // Statistics
        $totalSuppliers = $suppliers->count();
        $avgRating = $suppliers->whereNotNull('overall_rating')->avg('overall_rating') ?: 0;
        $topPerformers = $suppliers->where('performance_status', 'Top Performer')->count();
        $needsImprovement = $suppliers->where('performance_status', 'Needs Improvement')->count();

        return view('suppliers.performance-evaluation', compact(
            'suppliers',
            'totalSuppliers',
            'avgRating',
            'topPerformers',
            'needsImprovement'
        ));
    }

    public function approveSupplier(Request $request, Supplier $supplier)
    {
        $user = auth()->user();

        // Only GS Managers can approve suppliers
        if ($user->role !== User::ROLE_GS_MANAGER) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Update supplier approval status
        $supplier->update([
            'last_evaluation_date' => now(),
        ]);

        return response()->json(['success' => 'Supplier approved successfully']);
    }

    public function updateRating(Request $request, Supplier $supplier)
    {
        $user = auth()->user();
        try {
            // Only users who have created at least one contract may rate suppliers
            $hasContract = Contract::where('created_by', $user->id)->exists();

            if (!$hasContract) {
                return response()->json(['error' => 'Only users who have created contracts may rate suppliers.'], 403);
            }

            $request->validate([
                'quality_rating' => 'required|numeric|min:0|max:100',
                'delivery_rating' => 'required|numeric|min:0|max:100',
                'cost_rating' => 'required|numeric|min:0|max:5',
                'communication_rating' => 'required', // we'll coerce below
                'overall_rating' => 'required|numeric|min:0|max:100',
                'performance_status' => 'required|string',
            ]);

            // Normalize performance_status to values allowed by DB enum
            $inputStatus = trim($request->performance_status);
            $statusMap = [
                'excellent' => 'Top Performer',
                'top performer' => 'Top Performer',
                'good' => 'Average',
                'average' => 'Average',
                'needs improvement' => 'Needs Improvement',
                'poor' => 'Needs Improvement'
            ];
            $normalized = $statusMap[strtolower($inputStatus)] ?? $inputStatus;

            // Coerce communication_rating to numeric if possible (frontend may send 1-5)
            $comm = $request->communication_rating;
            if (is_numeric($comm)) {
                $commVal = (float) $comm;
            } else {
                // if frontend sent text like 'Excellent', map to numeric fallback (5,4,3...)
                $commMap = ['excellent' => 5, 'good' => 4, 'average' => 3, 'needs improvement' => 2, 'poor' => 1];
                $commVal = $commMap[strtolower(trim($comm))] ?? null;
            }

            // Coerce and cap numeric inputs to match DB column limits
            $quality = min(max((float) $request->quality_rating, 0), 100);
            $delivery = min(max((float) $request->delivery_rating, 0), 100);
            $cost = min(max((float) $request->cost_rating, 0), 5);
            $communication = is_null($commVal) ? null : min(max((float) $commVal, 0), 5);
            // DB column is decimal(3,1) — max 99.9, so cap overall to 99.9
            $overall = min(max((float) $request->overall_rating, 0), 99.9);

            $supplier->update([
                'quality_rating' => $quality,
                'delivery_rating' => $delivery,
                'cost_rating' => $cost,
                'communication_rating' => $communication,
                'overall_rating' => $overall,
                'performance_status' => $normalized,
                'last_evaluation_date' => now(),
            ]);

            return response()->json(['success' => 'Rating updated successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging and return a friendly JSON error
            Log::error('Error updating supplier rating', ['exception' => $e->getMessage(), 'supplier_id' => $supplier->id, 'user_id' => $user->id]);
            return response()->json(['error' => 'Server error while saving rating. Please contact the administrator.'], 500);
        }
    }

    public function searchSuppliers(Request $request)
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 10);

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suppliers = Supplier::where('name', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->limit($limit)
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json($suppliers);
    }

    public function checkSupplier(Request $request)
    {
        $name = $request->get('name', '');

        if (empty($name)) {
            return response()->json(['similar' => []]);
        }

        // Find similar suppliers (case-insensitive partial match)
        $similar = Supplier::where('name', 'like', '%' . $name . '%')
            ->limit(5)
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json(['similar' => $similar]);
    }
}
