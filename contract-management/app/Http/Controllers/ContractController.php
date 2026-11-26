<?php

namespace App\Http\Controllers;

use App\Helpers\ReportMeta;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\CurrencyType;
use App\Models\Department;
use App\Models\MaterialGroup;
use App\Models\PerformanceBondType;
use App\Models\Supplier;
use App\Models\User;
use App\Models\ActivityLog;
use App\Rules\GuaranteeAlertDays;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Contract::forUser($user)->with('creator');
        
        // Search
        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->input('search') . '%');
        });

        // Filter by status
        $query->when(
            $request->filled('status') && array_key_exists($request->input('status'), Contract::getStatuses()),
            function ($q) use ($request) {
                $q->where('status', $request->input('status'));
            }
        );

        // Filter by department
        $query->when($request->filled('department') && $user->canViewAllContracts(), function ($q) use ($request) {
            $q->where('department', $request->input('department'));
        });

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $allowedSorts = ['id', 'name', 'supplier_name', 'department', 'start_date', 'end_date', 'performance_guarantee_amount', 'status', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        $sortDirection = strtolower($request->get('sort_direction', 'desc'));
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        // Summary cards always reflect the full set allowed for the user,
        // matching the /dashboard/summary endpoints behind the detail modals.
        $statContracts = Contract::forUser($user)
            ->orderByDesc('id')
            ->get(['id', 'name', 'supplier_name', 'department', 'start_date', 'end_date', 'status', 'performance_guarantee_amount', 'extended_count']);

        $statRows = [
            'total' => $statContracts,
            'active' => $statContracts->where('status', Contract::STATUS_ACTIVE)->values(),
            'expiring' => $statContracts->where('status', Contract::STATUS_EXPIRING)->values(),
            'expired' => $statContracts->where('status', Contract::STATUS_EXPIRED)->values(),
        ];

        $totalContracts = $statRows['total']->count();
        $totalActive = $statRows['active']->count();
        $totalExpiring = $statRows['expiring']->count();
        $totalExpired = $statRows['expired']->count();

        $perPage = $request->get('per_page', 5);
        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 5;
        }

        $query->orderBy($sortBy, $sortDirection);
        if ($sortBy !== 'id' && $sortBy !== 'created_at') {
            $query->orderBy('id', 'desc');
        }
        $contracts = $query->paginate($perPage)->withQueryString();

        $departments = User::getContractDepartments();
        $statuses = Contract::getStatuses();
        $reportMeta = ReportMeta::build(
            $contracts->getCollection(),
            $this->letterheadFilters($request, $user),
            null,
            null,
            'created_at',
            $contracts->total()
        );

        return view('contracts.index', compact(
            'contracts', 'departments', 'statuses', 'totalContracts', 'totalActive', 'totalExpiring', 'totalExpired',
            'statRows', 'sortBy', 'sortDirection', 'perPage', 'reportMeta'
        ));
    }

    /**
     * @return array<int, string|null>
     */
    private function letterheadFilters(Request $request, $user): array
    {
        $statusLabels = Contract::getStatuses();

        return [
            $request->filled('search') ? 'Search: '.$request->search : null,
            $request->filled('status') && array_key_exists($request->status, $statusLabels) ? 'Status: '.$statusLabels[$request->status] : null,
            $request->filled('department') && $user->canViewAllContracts() ? 'Department: '.ucfirst(str_replace('_', ' ', $request->department)) : null,
        ];
    }

    public function exportList(Request $request)
    {
        $user = auth()->user();
        $query = Contract::forUser($user)->with('creator');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('department') && $user->canViewAllContracts()) {
            $query->where('department', $request->department);
        }

        $contracts = $query->orderBy('name')->get();
        $reportMeta = ReportMeta::build($contracts, $this->letterheadFilters($request, $user));

        if ($request->input('format') === 'pdf') {
            return Pdf::loadView('contracts.pdf', compact('contracts', 'reportMeta'))
                ->download('contracts-' . now()->format('Ymd-His') . '.pdf');
        }

        (new \App\Exports\ContractsExport($contracts, $reportMeta))->download('contracts_export.xlsx');
    }
    
    public function create()
    {
        $user = auth()->user();

        if (!$user->canCreateContracts()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to create contracts.');
        }

        $departments = [];

        // Set department based on user role
        if ($user->role === User::ROLE_GENERAL_SERVICE) {
            $departments = [User::DEPARTMENT_GENERAL_SERVICE => 'General Service'];
        } elseif ($user->role === User::ROLE_CMD) {
            $departments = [User::DEPARTMENT_CMD => 'CMD'];
        } elseif ($user->role === User::ROLE_FINANCE) {
            $departments = [User::DEPARTMENT_FINANCE => 'Finance'];
        } elseif (in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_GS_MANAGER, User::ROLE_CMD_MANAGER])) {
            // Managers and admins can create contracts for all departments
            $departments = User::getContractDepartments();
        }

        $materialGroups = Contract::getMaterialGroups();
        $contractTypes = Contract::getContractTypes();
        $currencyTypes = Contract::getCurrencyTypes();
        $performanceBondTypes = Contract::getPerformanceBondTypes();

        return view('contracts.create', compact('departments', 'materialGroups', 'contractTypes', 'currencyTypes', 'performanceBondTypes'));
    }

    public function storeMaterialGroup(Request $request)
    {
        $user = auth()->user();

        if (! $user->canCreateContracts()) {
            return response()->json(['message' => 'You are not authorized to register material groups.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
        ], [
            'name.required' => 'Enter the material group name.',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $validated['name']));

        $group = MaterialGroup::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();

        if ($group) {
            if (! $group->is_active) {
                $group->update(['is_active' => true]);
            }
        } else {
            $group = MaterialGroup::create([
                'name' => $name,
                'is_active' => true,
                'sort_order' => ((int) MaterialGroup::max('sort_order')) + 1,
            ]);
        }

        return response()->json([
            'name' => $group->name,
            'created' => $group->wasRecentlyCreated,
            'message' => $group->wasRecentlyCreated
                ? 'Material group registered.'
                : 'That material group is already registered.',
        ], $group->wasRecentlyCreated ? 201 : 200);
    }

    public function storeDepartment(Request $request)
    {
        $user = auth()->user();

        $canManageAllDepartments = in_array($user->role, [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_ADMIN,
            User::ROLE_GS_MANAGER,
            User::ROLE_CMD_MANAGER,
        ], true);

        if (! $canManageAllDepartments) {
            return response()->json(['message' => 'You are not authorized to register departments.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
        ], [
            'name.required' => 'Enter the department name.',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $validated['name']));
        $lookup = mb_strtolower($name);

        foreach (User::getContractDepartments() as $key => $label) {
            if (mb_strtolower((string) $key) === $lookup || mb_strtolower($label) === $lookup) {
                return response()->json([
                    'value' => (string) $key,
                    'label' => $label,
                    'created' => false,
                    'message' => 'That department is already registered.',
                ]);
            }
        }

        $department = Department::whereRaw('LOWER(name) = ?', [$lookup])->first();

        if ($department) {
            if (! $department->is_active) {
                $department->update(['is_active' => true]);
            }
        } else {
            $department = Department::create([
                'name' => $name,
                'is_active' => true,
                'sort_order' => ((int) Department::max('sort_order')) + 1,
            ]);
        }

        return response()->json([
            'value' => $department->name,
            'label' => $department->name,
            'created' => $department->wasRecentlyCreated,
            'message' => $department->wasRecentlyCreated
                ? 'Department registered.'
                : 'That department is already registered.',
        ], $department->wasRecentlyCreated ? 201 : 200);
    }

    public function storeContractType(Request $request)
    {
        $user = auth()->user();

        if (! $user->canCreateContracts()) {
            return response()->json(['message' => 'You are not authorized to register contract types.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
        ], [
            'name.required' => 'Enter the contract type name.',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $validated['name']));
        $lookup = mb_strtolower($name);

        foreach (Contract::getContractTypes() as $type) {
            if (mb_strtolower($type) === $lookup) {
                return response()->json([
                    'name' => $type,
                    'created' => false,
                    'message' => 'That contract type is already registered.',
                ]);
            }
        }

        $contractType = ContractType::whereRaw('LOWER(name) = ?', [$lookup])->first();

        if ($contractType) {
            if (! $contractType->is_active) {
                $contractType->update(['is_active' => true]);
            }
        } else {
            $contractType = ContractType::create([
                'name' => $name,
                'is_active' => true,
                'sort_order' => ((int) ContractType::max('sort_order')) + 1,
            ]);
        }

        return response()->json([
            'name' => $contractType->name,
            'created' => $contractType->wasRecentlyCreated,
            'message' => $contractType->wasRecentlyCreated
                ? 'Contract type registered.'
                : 'That contract type is already registered.',
        ], $contractType->wasRecentlyCreated ? 201 : 200);
    }

    public function storeCurrencyType(Request $request)
    {
        $user = auth()->user();

        if (! $user->canCreateContracts()) {
            return response()->json(['message' => 'You are not authorized to register currency types.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
        ], [
            'name.required' => 'Enter the currency type name.',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $validated['name']));
        $lookup = mb_strtolower($name);

        foreach (Contract::getCurrencyTypes() as $type) {
            if (mb_strtolower($type) === $lookup) {
                return response()->json([
                    'name' => $type,
                    'created' => false,
                    'message' => 'That currency type is already registered.',
                ]);
            }
        }

        $currencyType = CurrencyType::whereRaw('LOWER(name) = ?', [$lookup])->first();

        if ($currencyType) {
            if (! $currencyType->is_active) {
                $currencyType->update(['is_active' => true]);
            }
        } else {
            $currencyType = CurrencyType::create([
                'name' => $name,
                'is_active' => true,
                'sort_order' => ((int) CurrencyType::max('sort_order')) + 1,
            ]);
        }

        return response()->json([
            'name' => $currencyType->name,
            'created' => $currencyType->wasRecentlyCreated,
            'message' => $currencyType->wasRecentlyCreated
                ? 'Currency type registered.'
                : 'That currency type is already registered.',
        ], $currencyType->wasRecentlyCreated ? 201 : 200);
    }

    public function storePerformanceBondType(Request $request)
    {
        $user = auth()->user();

        if (! $user->canCreateContracts()) {
            return response()->json(['message' => 'You are not authorized to register performance bond types.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
        ], [
            'name.required' => 'Enter the performance bond type name.',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $validated['name']));
        $lookup = mb_strtolower($name);

        foreach (Contract::getPerformanceBondTypes() as $type) {
            if (mb_strtolower($type) === $lookup) {
                return response()->json([
                    'name' => $type,
                    'created' => false,
                    'message' => 'That performance bond type is already registered.',
                ]);
            }
        }

        $bondType = PerformanceBondType::whereRaw('LOWER(name) = ?', [$lookup])->first();

        if ($bondType) {
            if (! $bondType->is_active) {
                $bondType->update(['is_active' => true]);
            }
        } else {
            $bondType = PerformanceBondType::create([
                'name' => $name,
                'is_active' => true,
                'sort_order' => ((int) PerformanceBondType::max('sort_order')) + 1,
            ]);
        }

        return response()->json([
            'name' => $bondType->name,
            'created' => $bondType->wasRecentlyCreated,
            'message' => $bondType->wasRecentlyCreated
                ? 'Performance bond type registered.'
                : 'That performance bond type is already registered.',
        ], $bondType->wasRecentlyCreated ? 201 : 200);
    }
    
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->canCreateContracts()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to create contracts.');
        }

        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'material_group' => ['required', 'string', Rule::in(Contract::getMaterialGroups())],
            'contract_amount' => 'nullable|numeric|min:0',
            'department' => ['required', 'string', Rule::in(array_keys(User::getContractDepartments()))],
            'contract_type' => ['required', 'string', Rule::in(Contract::getContractTypes())],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'alert_days' => 'required|integer|min:0',
            'performance_bond_type' => ['nullable', Rule::in(Contract::getPerformanceBondTypes())],
            'issued_by' => 'nullable|string|max:255',
            'currency_type' => ['nullable', Rule::in(Contract::getCurrencyTypes())],
            'performance_guarantee_amount' => 'nullable|numeric|min:0',
            'performance_guarantee_end_date' => 'nullable|date|after_or_equal:start_date',
            'alert_days_before_guarantee_end' => [
                'nullable',
                'integer',
                'min:0',
                'max:365',
                new GuaranteeAlertDays($request->performance_guarantee_end_date),
            ],
        ], [
            'end_date.after_or_equal' => 'የመጨረሻው ቀን ከመጀመሪያው ቀን በፊት መሆን የለበትም።',
            'performance_guarantee_end_date.after_or_equal' => 'የዋስትና ማብቂያ ቀን ከመጀመሪያው ቀን በፊት መሆን የለበትም።',
        ]);
        
        // Check if user can create contract for selected department
        $userDepartment = $user->getUserDepartment();
        if ($userDepartment && $request->department !== $userDepartment) {
            return back()->with('error', 'You can only create contracts for your department.');
        }

        // Handle supplier logic
        $supplier = null;

        // CASE 1: Supplier ID is provided (user selected from autocomplete)
        if ($request->has('supplier_id') && $request->supplier_id) {
            $supplier = Supplier::find($request->supplier_id);
        }
        // CASE 2: No supplier ID, check if supplier exists by name
        else {
            // Normalize supplier name for comparison
            $supplierName = trim($request->supplier_name);

            // Check if supplier already exists (case-insensitive search)
            $supplier = Supplier::whereRaw('LOWER(name) = ?', [strtolower($supplierName)])->first();

            // If supplier doesn't exist, create new one
            if (!$supplier) {
                $supplier = Supplier::create([
                    'name' => $supplierName,
                    'created_by' => auth()->id(),
                    'created_by_name' => auth()->user()->name,
                    'created_at' => now(),
                ]);
            }
        }

        // Create contract with supplier_id and supplier_name
        $contract = Contract::create([
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'material_group' => $validated['material_group'],
            'contract_amount' => $validated['contract_amount'] ?? null,
            'department' => $validated['department'],
            'contract_type' => $validated['contract_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'alert_days' => $validated['alert_days'],
            'performance_bond_type' => $validated['performance_bond_type'] ?? null,
            'issued_by' => $validated['issued_by'] ?? null,
            'currency_type' => $validated['currency_type'] ?? null,
            'performance_guarantee_amount' => $validated['performance_guarantee_amount'] ?? null,
            'performance_guarantee_end_date' => $validated['performance_guarantee_end_date'] ?? null,
            'alert_days_before_guarantee_end' => $validated['alert_days_before_guarantee_end'] ?? null,
            'created_by' => auth()->id(),
            'status' => 'active',
        ]);
        
        $contract->updateStatus();
        
        return redirect()->route('contracts.index')
            ->with('success', 'Contract created successfully.');
    }
    
    public function show(Contract $contract)
    {
        $user = auth()->user();

        // Check if user can view this contract
        if (!in_array($contract->department, $user->getAllowedDepartments())) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view this contract.');
        }

        $contract->load('histories');

        $activityLogs = ActivityLog::where('contract_id', $contract->id)
            ->latest()
            ->get();

        return view('contracts.show', compact('contract', 'activityLogs'));
    }
    
    public function edit(Contract $contract)
    {
        $user = auth()->user();

        // Check if user can edit this contract
        if (!$contract->canUserEdit($user)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'You are not authorized to edit this contract.');
        }

        $departments = [];

        // Set department based on user role
        if ($user->role === User::ROLE_GENERAL_SERVICE) {
            $departments = [User::DEPARTMENT_GENERAL_SERVICE => 'General Service'];
        } elseif ($user->role === User::ROLE_CMD) {
            $departments = [User::DEPARTMENT_CMD => 'CMD'];
        } elseif ($user->role === User::ROLE_FINANCE) {
            $departments = [User::DEPARTMENT_FINANCE => 'Finance'];
        }

        $materialGroups = Contract::getMaterialGroups();
        $contractTypes = Contract::getContractTypes();
        $currencyTypes = Contract::getCurrencyTypes();
        $performanceBondTypes = Contract::getPerformanceBondTypes();

        return view('contracts.edit', compact('contract', 'departments', 'materialGroups', 'contractTypes', 'currencyTypes', 'performanceBondTypes'));
    }

    public function update(Request $request, Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user can edit this contract
        if (!$contract->canUserEdit($user)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'You are not authorized to edit this contract.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'material_group' => ['required', 'string', Rule::in(Contract::getMaterialGroups())],
            'contract_type' => ['required', 'string', Rule::in(Contract::getContractTypes())],
            'department' => ['required', 'string', Rule::in(array_keys(User::getDepartments()))],
            'supplier_name' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'alert_days' => [
                'required',
                'integer',
                'min:0',
                'max:365',
                function ($attribute, $value, $fail) use ($request) {
                    $startDate = new \DateTime($request->start_date);
                    $endDate = new \DateTime($request->end_date);
                    $contractDuration = $startDate->diff($endDate)->days;

                    if ($value > $contractDuration) {
                        $fail("Alert days ({$value}) cannot be greater than the contract duration ({$contractDuration} days).");
                    }
                },
            ],
            'performance_bond_type' => ['nullable', 'string', Rule::in(Contract::getPerformanceBondTypes())],
            'issued_by' => 'nullable|string|max:255',
            'contract_amount' => 'nullable|numeric|min:0',
            'performance_guarantee_amount' => 'nullable|numeric|min:0',
            'currency_type' => ['nullable', 'string', Rule::in(Contract::getCurrencyTypes())],
            'performance_guarantee_end_date' => 'nullable|date|after_or_equal:start_date|before_or_equal:end_date',
            'alert_days_before_guarantee_end' => [
                'nullable',
                'integer',
                'min:0',
                'max:365',
                new GuaranteeAlertDays($request->performance_guarantee_end_date),
            ],
        ], [
            'end_date.after_or_equal' => 'የመጨረሻው ቀን ከመጀመሪያው ቀን በፊት መሆን የለበትም።',
            'performance_guarantee_end_date.after_or_equal' => 'የዋስትና ማብቂያ ቀን ከመጀመሪያው ቀን በፊት መሆን የለበትም።',
            'performance_guarantee_end_date.before_or_equal' => 'የዋስትና ማብቂያ ቀን ከውል ማብቂያ ቀን በኋላ መሆን የለበትም።',
        ]);
        
        // Check if user can update contract for selected department
        if ($request->department !== $contract->department) {
            return back()->with('error', 'You cannot change the department of this contract.');
        }
        
        $newGuaranteeEndDate = $request->performance_guarantee_end_date ?: null;
        $guaranteeWindowChanged = (int) $request->alert_days_before_guarantee_end !== (int) $contract->alert_days_before_guarantee_end
            || $newGuaranteeEndDate !== $contract->performance_guarantee_end_date?->format('Y-m-d');

        $attributes = [
            'name' => $request->name,
            'description' => $request->description,
            'material_group' => $request->material_group,
            'contract_amount' => $request->contract_amount,
            'contract_type' => $request->contract_type,
            'department' => $request->department,
            'supplier_name' => $request->supplier_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'alert_days' => $request->alert_days,
            'performance_bond_type' => $request->performance_bond_type,
            'issued_by' => $request->issued_by,
            'performance_guarantee_amount' => $request->performance_guarantee_amount,
            'currency_type' => $request->currency_type,
            'performance_guarantee_end_date' => $newGuaranteeEndDate,
            'alert_days_before_guarantee_end' => $request->alert_days_before_guarantee_end,
        ];

        if ($guaranteeWindowChanged) {
            $attributes['guarantee_alert_sent_at'] = null;
        }

        $contract->update($attributes);
        
        $contract->updateStatus();
        
        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract updated successfully.');
    }
    
    public function destroy(Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user can delete this contract
        if (!$contract->canUserDelete($user)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'You are not authorized to delete this contract.');
        }
        
        $contract->delete();
        
        return redirect()->route('contracts.index')
            ->with('success', 'Contract deleted successfully.');
    }
    
    public function alerts()
    {
        $user = auth()->user();
        $alertContracts = [];
        
        $contracts = Contract::forUser($user)->get();
        
        foreach ($contracts as $contract) {
            $alerts = [];
            
            // Check contract end alert
            if ($contract->isExpiringSoon()) {
                $days = $contract->getDaysUntilContractEnd();
                $alerts[] = [
                    'type' => 'contract_end',
                    'message' => "Contract ends in {$days} days",
                    'severity' => $days <= 7 ? 'high' : 'medium'
                ];
            }
            
            // Check performance guarantee alert
            if ($contract->performance_guarantee_end_date && $contract->isPerformanceGuaranteeExpiringSoon()) {
                $days = $contract->getDaysUntilPerformanceGuaranteeEnd();
                $alerts[] = [
                    'type' => 'performance_guarantee',
                    'message' => "Performance guarantee ends in {$days} days",
                    'severity' => $days <= 7 ? 'high' : 'medium'
                ];
            }
            
            if (!empty($alerts)) {
                $alertContracts[] = [
                    'contract' => $contract,
                    'alerts' => $alerts
                ];
            }
        }
        
        // Sort by severity and days remaining
        usort($alertContracts, function ($a, $b) {
            $aSeverity = $this->getSeverityScore($a['alerts']);
            $bSeverity = $this->getSeverityScore($b['alerts']);
            return $bSeverity <=> $aSeverity;
        });
        
        return view('contracts.alerts', compact('alertContracts'));
    }
    
    private function getSeverityScore($alerts)
    {
        $score = 0;
        foreach ($alerts as $alert) {
            if ($alert['severity'] === 'high') {
                $score += 3;
            } elseif ($alert['severity'] === 'medium') {
                $score += 1;
            }
        }
        return $score;
    }
    
    public function extendContractEnd(Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user can extend this contract
        if (!$contract->canUserEdit($user)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'You are not authorized to extend this contract.');
        }
        
        return view('contracts.extend-contract-end', compact('contract'));
    }
    
    public function updateContractEnd(Request $request, Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user can extend this contract
        if (!$contract->canUserEdit($user)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'You are not authorized to extend this contract.');
        }
        
        $request->validate([
            'new_end_date' => 'required|date|after:' . $contract->end_date,
            'reason' => 'required|string|max:500',
        ]);
        
        $editedBy = auth()->user()->name . ' (' . auth()->user()->getDepartmentDisplayName() . ')';
        $contract->extendContractEnd($request->new_end_date, $request->reason, $editedBy);
        
        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract end date extended successfully.');
    }
    
    public function extendPerformanceGuarantee(Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user can extend performance guarantee
        if (!$contract->canUserEdit($user)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'You are not authorized to extend performance guarantee.');
        }
        
        if (!$contract->performance_guarantee_end_date) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'This contract does not have a performance guarantee.');
        }
        
        return view('contracts.extend-performance-guarantee', compact('contract'));
    }
    
    public function updatePerformanceGuarantee(Request $request, Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user can extend performance guarantee
        if (!$contract->canUserEdit($user)) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'You are not authorized to extend performance guarantee.');
        }
        
        if (!$contract->performance_guarantee_end_date) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'This contract does not have a performance guarantee.');
        }
        
        $request->validate([
            'new_amount' => 'required|numeric|min:0',
            'new_end_date' => 'required|date|after:' . $contract->performance_guarantee_end_date,
            'reason' => 'required|string|max:500',
        ]);

        $editedBy = auth()->user()->name . ' (' . auth()->user()->getDepartmentDisplayName() . ')';
        $contract->extendPerformanceGuarantee($request->new_amount, $request->new_end_date, $request->reason, $editedBy);
        
        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Performance guarantee end date extended successfully.');
    }
    
    public function reports(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_FINANCE, User::ROLE_GS_MANAGER, User::ROLE_CMD_MANAGER])) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view reports.');
        }

        // Get date range from request or default to last 12 months
        $dateRange = $request->get('date_range', '12_months');
        $startDate = $request->has('start_date') ? \Carbon\Carbon::parse($request->start_date) : $this->getStartDateForRange($dateRange);
        $endDate = $request->has('end_date') ? \Carbon\Carbon::parse($request->end_date) : now();

        // Get statistics
        $totalContracts = Contract::forUser($user)->count();
        $activeContracts = Contract::forUser($user)->active()->count();
        $expiringContracts = Contract::forUser($user)->expiring()->count();
        $expiredContracts = Contract::forUser($user)->expired()->count();

        // Calculate percentages
        $activePercentage = $totalContracts > 0 ? round(($activeContracts / $totalContracts) * 100, 1) : 0;
        $expiringPercentage = $totalContracts > 0 ? round(($expiringContracts / $totalContracts) * 100, 1) : 0;
        $expiredPercentage = $totalContracts > 0 ? round(($expiredContracts / $totalContracts) * 100, 1) : 0;

        // Contracts by department
        $departments = User::getContractDepartments();
        $contractsByDepartment = Contract::forUser($user)
            ->select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
            ->get()
            ->map(function($item) use ($departments) {
                return (object)[
                    'department' => $departments[$item->department] ?? ucfirst(str_replace('_', ' ', $item->department)),
                    'count' => $item->count
                ];
            });

        // Contracts by month (dynamic based on date range)
        $monthlyData = Contract::forUser($user)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Fill in missing months to ensure a continuous line in the chart
        $contractsByMonth = collect();
        $current = clone $startDate;
        $end = clone $endDate;
        
        // Normalize to start of month for comparison
        $current->day(1)->hour(0)->minute(0)->second(0);
        $end->day(1)->hour(0)->minute(0)->second(0);

        while ($current <= $end) {
            $monthKey = $current->format('Y-m');
            $contractsByMonth->push((object)[
                'month' => $current->format('M Y'),
                'count' => $monthlyData[$monthKey] ?? 0
            ]);
            $current->addMonth();
        }

        // If we only have one month (e.g., in January for YTD), add the previous month 
        // with 0 to make the chart render better as a line
        if ($contractsByMonth->count() === 1) {
            $firstMonth = \Carbon\Carbon::createFromFormat('M Y', $contractsByMonth->first()->month);
            $prevMonth = $firstMonth->subMonth();
            $contractsByMonth->prepend((object)[
                'month' => $prevMonth->format('M Y'),
                'count' => 0
            ]);
        }

        // Contracts by status
        $contractsByStatus = Contract::forUser($user)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Recent activity (latest contract updates)
        $recentActivity = ActivityLog::with(['contract' => function ($query) {
            $query->withTrashed();
        }])
            ->whereHas('contract', function ($query) use ($user) {
                $query->withTrashed()->forUser($user);
            })
            ->latest()
            ->take(10)
            ->get();

        // Last updated timestamp
        $lastUpdated = now();

        return view('contracts.reports', compact(
            'user',
            'totalContracts',
            'activeContracts',
            'expiringContracts',
            'expiredContracts',
            'activePercentage',
            'expiringPercentage',
            'expiredPercentage',
            'contractsByDepartment',
            'contractsByMonth',
            'contractsByStatus',
            'recentActivity',
            'dateRange',
            'startDate',
            'endDate',
            'lastUpdated'
        ));
    }

    private function getStartDateForRange($range)
    {
        return match($range) {
            '6_months' => now()->subMonths(6),
            '12_months' => now()->subMonths(12),
            'year_to_date' => now()->startOfYear(),
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            default => now()->subMonths(12)
        };
    }

    public function exportReports()
    {
        $user = auth()->user();

        if (!in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_FINANCE, User::ROLE_GS_MANAGER, User::ROLE_CMD_MANAGER])) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to export reports.');
        }

        // Get the same data as the reports method
        $totalContracts = Contract::forUser($user)->count();
        $activeContracts = Contract::forUser($user)->active()->count();
        $expiringContracts = Contract::forUser($user)->expiring()->count();
        $expiredContracts = Contract::forUser($user)->expired()->count();

        $contractsByDepartment = Contract::forUser($user)
            ->select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
            ->get();

        $contractsByStatus = Contract::forUser($user)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $contracts = Contract::forUser($user)->with('creator')->get();

        // Create CSV content
        $csvData = [];

        // Summary section
        $csvData[] = ['Contract Management Report'];
        $csvData[] = ['Generated on', now()->format('Y-m-d H:i:s')];
        $csvData[] = ['Generated by', $user->name . ' (' . $user->getDepartmentDisplayName() . ')'];
        $csvData[] = [''];
        $csvData[] = ['Summary Statistics'];
        $csvData[] = ['Total Contracts', $totalContracts];
        $csvData[] = ['Active Contracts', $activeContracts];
        $csvData[] = ['Expiring Soon', $expiringContracts];
        $csvData[] = ['Expired Contracts', $expiredContracts];
        $csvData[] = [''];

        // Contracts by Department
        $csvData[] = ['Contracts by Department'];
        $csvData[] = ['Department', 'Count'];
        foreach ($contractsByDepartment as $dept) {
            $csvData[] = [ucfirst(str_replace('_', ' ', $dept->department)), $dept->count];
        }
        $csvData[] = [''];

        // Contracts by Status
        $csvData[] = ['Contracts by Status'];
        $csvData[] = ['Status', 'Count'];
        foreach ($contractsByStatus as $status) {
            $csvData[] = [ucfirst($status->status), $status->count];
        }
        $csvData[] = [''];

        // Contract Details
        $csvData[] = ['Contract Details'];
        $csvData[] = ['Name', 'Supplier Name', 'Department', 'Contract Type', 'Start Date', 'End Date', 'Status', 'Created By', 'Currency', 'Performance Guarantee Amount', 'Performance Guarantee End Date', 'Performance Bond Type', 'Issued By'];
        foreach ($contracts as $contract) {
            $csvData[] = [
                $contract->name,
                $contract->supplier_name ?? 'N/A',
                ucfirst(str_replace('_', ' ', $contract->department)),
                $contract->contract_type ?? 'N/A',
                $contract->start_date?->format('Y-m-d'),
                $contract->end_date?->format('Y-m-d'),
                ucfirst($contract->status),
                $contract->creator->name ?? 'N/A',
                $contract->currency_type ?? 'N/A',
                $contract->performance_guarantee_amount ? number_format($contract->performance_guarantee_amount, 2) : 'N/A',
                $contract->performance_guarantee_end_date?->format('Y-m-d') ?? 'N/A',
                $contract->performance_bond_type ?? 'N/A',
                $contract->issued_by ?? 'N/A'
            ];
        }

        // Generate CSV response
        $filename = 'contract-report-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export(Request $request)
    {
        $format = $request->input('format', 'csv');
        $scope = $request->input('scope', 'all');

        // Generate report based on parameters
        $data = $this->generateExportData($request);

        if ($format === 'pdf') {
            return $this->exportAsPdf($data);
        } elseif ($format === 'excel') {
            return $this->exportAsExcel($data);
        } elseif ($format === 'csv') {
            return $this->exportAsCsv($data);
        }

        // Default to CSV
        return $this->exportAsCsv($data);
    }

    private function generateExportData(Request $request)
    {
        $user = auth()->user();
        $scope = $request->input('scope', 'all');

        // Base query
        $query = Contract::forUser($user);

        // Apply scope filters
        if ($scope === 'active') {
            $query->active();
        } elseif ($scope === 'expiring') {
            $query->expiring();
        } elseif ($scope === 'expired') {
            $query->expired();
        }

        $contracts = $query->with('creator')->get();

        // Get summary data
        $summary = [
            'total_contracts' => $contracts->count(),
            'active_contracts' => $contracts->where('status', 'active')->count(),
            'expiring_contracts' => $contracts->where('status', 'expiring')->count(),
            'expired_contracts' => $contracts->where('status', 'expired')->count(),
            'generated_by' => $user->name . ' (' . $user->getDepartmentDisplayName() . ')',
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];

        return [
            'summary' => $summary,
            'contracts' => $contracts,
        ];
    }

    private function exportAsCsv($data)
    {
        $filename = 'contracts-export-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Summary section
            fputcsv($file, ['Contract Export Report']);
            fputcsv($file, ['Generated on', $data['summary']['generated_at']]);
            fputcsv($file, ['Generated by', $data['summary']['generated_by']]);
            fputcsv($file, ['']);
            fputcsv($file, ['Summary Statistics']);
            fputcsv($file, ['Total Contracts', $data['summary']['total_contracts']]);
            fputcsv($file, ['Active Contracts', $data['summary']['active_contracts']]);
            fputcsv($file, ['Expiring Soon', $data['summary']['expiring_contracts']]);
            fputcsv($file, ['Expired Contracts', $data['summary']['expired_contracts']]);
            fputcsv($file, ['']);

            // Contract Details
            fputcsv($file, ['Contract Details']);
            fputcsv($file, ['Name', 'Supplier Name', 'Department', 'Contract Type', 'Start Date', 'End Date', 'Status', 'Created By', 'Currency', 'Performance Guarantee Amount', 'Performance Guarantee End Date', 'Performance Bond Type', 'Issued By']);

            foreach ($data['contracts'] as $contract) {
                fputcsv($file, [
                    $contract->name,
                    $contract->supplier_name ?? 'N/A',
                    ucfirst(str_replace('_', ' ', $contract->department)),
                    $contract->contract_type ?? 'N/A',
                    $contract->start_date?->format('Y-m-d'),
                    $contract->end_date?->format('Y-m-d'),
                    ucfirst($contract->status),
                    $contract->creator->name ?? 'N/A',
                    $contract->currency_type ?? 'N/A',
                    $contract->performance_guarantee_amount ? number_format($contract->performance_guarantee_amount, 2) : 'N/A',
                    $contract->performance_guarantee_end_date?->format('Y-m-d') ?? 'N/A',
                    $contract->performance_bond_type ?? 'N/A',
                    $contract->issued_by ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportAsPdf($data)
    {
        // Simple HTML-based PDF export (basic implementation)
        $html = $this->generateHtmlReport($data);

        $filename = 'contracts-export-' . now()->format('Y-m-d-H-i-s') . '.html';
        $headers = [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        return response($html, 200, $headers);
    }

    private function exportAsExcel($data)
    {
        // For now, export as CSV with Excel filename
        // In a real implementation, you'd use Laravel Excel or similar package
        $filename = 'contracts-export-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Summary section
            fputcsv($file, ['Contract Export Report']);
            fputcsv($file, ['Generated on', $data['summary']['generated_at']]);
            fputcsv($file, ['Generated by', $data['summary']['generated_by']]);
            fputcsv($file, ['']);
            fputcsv($file, ['Summary Statistics']);
            fputcsv($file, ['Total Contracts', $data['summary']['total_contracts']]);
            fputcsv($file, ['Active Contracts', $data['summary']['active_contracts']]);
            fputcsv($file, ['Expiring Soon', $data['summary']['expiring_contracts']]);
            fputcsv($file, ['Expired Contracts', $data['summary']['expired_contracts']]);
            fputcsv($file, ['']);

            // Contract Details
            fputcsv($file, ['Contract Details']);
            fputcsv($file, ['Name', 'Department', 'Contract Type', 'Start Date', 'End Date', 'Status', 'Created By', 'Currency', 'Performance Guarantee Amount', 'Performance Guarantee End Date']);

            foreach ($data['contracts'] as $contract) {
                fputcsv($file, [
                    $contract->name,
                    ucfirst(str_replace('_', ' ', $contract->department)),
                    $contract->contract_type ?? 'N/A',
                    $contract->start_date?->format('Y-m-d'),
                    $contract->end_date?->format('Y-m-d'),
                    ucfirst($contract->status),
                    $contract->creator->name ?? 'N/A',
                    $contract->currency_type ?? 'N/A',
                    $contract->performance_guarantee_amount ? number_format($contract->performance_guarantee_amount, 2) : 'N/A',
                    $contract->performance_guarantee_end_date?->format('Y-m-d') ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generateHtmlReport($data)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Contract Export Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1, h2 { color: #333; }
                table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .summary { background-color: #f9f9f9; padding: 15px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <h1>Contract Export Report</h1>
            <div class="summary">
                <p><strong>Generated on:</strong> ' . $data['summary']['generated_at'] . '</p>
                <p><strong>Generated by:</strong> ' . $data['summary']['generated_by'] . '</p>
                <h2>Summary Statistics</h2>
                <ul>
                    <li>Total Contracts: ' . $data['summary']['total_contracts'] . '</li>
                    <li>Active Contracts: ' . $data['summary']['active_contracts'] . '</li>
                    <li>Expiring Soon: ' . $data['summary']['expiring_contracts'] . '</li>
                    <li>Expired Contracts: ' . $data['summary']['expired_contracts'] . '</li>
                </ul>
            </div>

            <h2>Contract Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>PG Amount</th>
                        <th>PG End Date</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($data['contracts'] as $contract) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($contract->name) . '</td>';
            $html .= '<td>' . htmlspecialchars(ucfirst(str_replace('_', ' ', $contract->department))) . '</td>';
            $html .= '<td>' . ($contract->start_date ? $contract->start_date->format('Y-m-d') : 'N/A') . '</td>';
            $html .= '<td>' . ($contract->end_date ? $contract->end_date->format('Y-m-d') : 'N/A') . '</td>';
            $html .= '<td>' . htmlspecialchars(ucfirst($contract->status)) . '</td>';
            $html .= '<td>' . htmlspecialchars($contract->creator->name ?? 'N/A') . '</td>';
            $html .= '<td>' . ($contract->performance_guarantee_amount ? number_format($contract->performance_guarantee_amount, 2) : 'N/A') . '</td>';
            $html .= '<td>' . ($contract->performance_guarantee_end_date ? $contract->performance_guarantee_end_date->format('Y-m-d') : 'N/A') . '</td>';
            $html .= '</tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';

        return $html;
    }
}
