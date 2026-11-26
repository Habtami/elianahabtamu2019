<?php

namespace App\Http\Controllers;

use App\Helpers\ReportMeta;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasPermission('admin_panel')) {
                return redirect()->route('dashboard')
                    ->with('error', 'You are not authorized to access the admin panel.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('manage_users')) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to manage users.');
        }

        $query = User::query(); // Show all users including admins

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('role', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->has('status_filter') && $request->status_filter !== '') {
            $query->where('status', $request->status_filter);
        }

        // Filter by role
        if ($request->has('role_filter') && $request->role_filter !== '') {
            $query->where('role', $request->role_filter);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Validate sort_by to prevent SQL injection
        $allowedSortColumns = ['name', 'email', 'role', 'department', 'status', 'created_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query->orderBy($sortBy, $sortDirection);

        // Per page
        $perPage = $request->get('per_page', 5);
        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 5;
        }

        $users = $query->paginate($perPage)->appends($request->query());

        $roles = User::getRoles();
        $departments = User::getDepartments();

        $userCounts = [
            'all' => User::count(),
            'active' => User::where('status', true)->count(),
            'admins' => User::where('role', User::ROLE_ADMIN)->count(),
            'inactive' => User::where('status', false)->count(),
        ];

        // Get filter values for view
        $statusFilter = $request->get('status_filter', '');
        $roleFilter = $request->get('role_filter', '');
        $search = $request->get('search', '');

        return view('admin.users.index', compact('users', 'roles', 'departments', 'sortBy', 'sortDirection', 'perPage', 'statusFilter', 'roleFilter', 'search', 'userCounts'));
    }

    public function userDetails(Request $request)
    {
        if (!auth()->user()->hasPermission('manage_users')) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to manage users.');
        }

        $validated = $request->validate([
            'scope' => 'required|in:all,active,admins,inactive',
            'page' => 'nullable|integer|min:1',
        ]);

        $query = User::query();

        match ($validated['scope']) {
            'active' => $query->where('status', true),
            'admins' => $query->where('role', User::ROLE_ADMIN),
            'inactive' => $query->where('status', false),
            default => null,
        };

        $users = $query->orderBy('name')->paginate(15);

        return response()->json([
            'scope' => $validated['scope'],
            'total' => $users->total(),
            'page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'first_item' => $users->firstItem(),
            'last_item' => $users->lastItem(),
            'users' => $users->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleDisplayName(),
                'department' => $user->department ? $user->getDepartmentDisplayName() : '—',
                'status' => $user->status,
                'edit_url' => route('admin.users.edit', $user),
            ]),
        ]);
    }

    public function exportUsers(Request $request)
    {
        if (!auth()->user()->hasPermission('manage_users')) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to manage users.');
        }

        $query = User::query();

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('role', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->has('status_filter') && $request->status_filter !== '') {
            $query->where('status', $request->status_filter);
        }

        // Filter by role
        if ($request->has('role_filter') && $request->role_filter !== '') {
            $query->where('role', $request->role_filter);
        }

        $users = $query->orderBy('name')->get();

        (new \App\Exports\UsersExport($users))->download('users_export.xlsx');
    }

    public function create()
    {
        if (!auth()->user()->hasPermission('manage_users')) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to manage users.');
        }

        $roles = User::getRoles();

        $departments = User::getDepartments();

        return view('admin.users.create', compact('roles', 'departments'));
    }
    
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('manage_users')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You do not have permission to manage users.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => ['required', 'string', Rule::in(array_keys(User::getRoles()))],
            'department' => 'nullable|string|in:ict,cmd,general_service,finance,ddg_fpa_gs',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Check department based on role
        $role = $request->role;
        $department = $request->department;

        if ($role === User::ROLE_ADMIN && empty($department)) {
            $department = User::DEPARTMENT_ICT;
        }

        if ($role !== User::ROLE_ADMIN && (
            ($role === User::ROLE_GENERAL_SERVICE && $department !== User::DEPARTMENT_GENERAL_SERVICE) ||
            ($role === User::ROLE_CMD && $department !== User::DEPARTMENT_CMD) ||
            ($role === User::ROLE_FINANCE && $department !== User::DEPARTMENT_FINANCE)
        )) {
            return back()->with('error', 'Invalid department for selected role.');
        }

        $usesDefaultPassword = ! $request->filled('password');
        $defaultPassword = config('app.default_password');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $role,
            'department' => $department,
            'password' => Hash::make($usesDefaultPassword ? $defaultPassword : $request->password),
            'status' => true,
            'must_change_password' => true,
        ]);

        \App\Models\ActivityLog::create([
            'activity_type' => \App\Models\ActivityLog::TYPE_ADMIN_ACTION,
            'user_id' => $user->id,
            'edited_by' => auth()->user()->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'severity' => \App\Models\ActivityLog::SEVERITY_INFO,
            'field_name' => 'user_create',
            'reason' => 'User account created by administrator',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', $usesDefaultPassword
                ? 'User created successfully. Default password: ' . $defaultPassword . ' — the user must change it on first login.'
                : 'User created successfully. The user must change the password on first login.');
    }
    
    public function edit(User $user)
    {
        if (!auth()->user()->hasPermission('manage_users')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You do not have permission to manage users.');
        }

        $roles = User::getRoles();

        // Keep a deactivated role selectable for the user being edited
        if (!isset($roles[$user->role])) {
            $roles[$user->role] = $user->role;
        }

        $departments = User::getDepartments();

        return view('admin.users.edit', compact('user', 'roles', 'departments'));
    }
    
    public function update(Request $request, User $user)
    {
        if (!auth()->user()->hasPermission('manage_users')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You do not have permission to manage users.');
        }

        $roles = User::getRoles();

        if (!isset($roles[$user->role])) {
            $roles[$user->role] = $user->role;
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => ['required', 'string', Rule::in(array_keys($roles))],
            'department' => 'required|string|in:ict,cmd,general_service,finance,ddg_fpa_gs',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Check department based on role
        $role = $request->role;
        $department = $request->department;

        if ($role === User::ROLE_ADMIN && empty($department)) {
            $department = User::DEPARTMENT_ICT;
        }

        if ($role !== User::ROLE_ADMIN && (
            ($role === User::ROLE_GENERAL_SERVICE && $department !== User::DEPARTMENT_GENERAL_SERVICE) ||
            ($role === User::ROLE_CMD && $department !== User::DEPARTMENT_CMD) ||
            ($role === User::ROLE_FINANCE && $department !== User::DEPARTMENT_FINANCE)
        )) {
            return back()->with('error', 'Invalid department for selected role.');
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $role,
            'department' => $department,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
            $updateData['must_change_password'] = true;
            $updateData['password_changed_at'] = null;
        }

        $user->update($updateData);

        \App\Models\ActivityLog::create([
            'activity_type' => \App\Models\ActivityLog::TYPE_ADMIN_ACTION,
            'user_id' => $user->id,
            'edited_by' => auth()->user()->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'severity' => \App\Models\ActivityLog::SEVERITY_INFO,
            'field_name' => 'user_update',
            'reason' => 'User account updated by administrator',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }
    
    public function destroy(User $user)
    {
        if (!auth()->user()->hasPermission('manage_users')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You do not have permission to manage users.');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete super admin user.');
        }

        // Check if user has contracts
        if ($user->contracts()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete user with existing contracts.');
        }

        \App\Models\ActivityLog::create([
            'activity_type' => \App\Models\ActivityLog::TYPE_ADMIN_ACTION,
            'user_id' => $user->id,
            'edited_by' => auth()->user()->name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'severity' => \App\Models\ActivityLog::SEVERITY_DANGER,
            'field_name' => 'user_delete',
            'reason' => 'User account deleted by administrator',
        ]);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
    
    public function toggleStatus(User $user)
    {
        if (!auth()->user()->hasPermission('manage_users')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You do not have permission to manage users.');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot change status of super admin user.');
        }

        $user->status = !$user->status;
        $user->save();

        $status = $user->status ? 'activated' : 'deactivated';

        \App\Models\ActivityLog::create([
            'activity_type' => \App\Models\ActivityLog::TYPE_ADMIN_ACTION,
            'user_id' => $user->id,
            'edited_by' => auth()->user()->name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'severity' => \App\Models\ActivityLog::SEVERITY_WARNING,
            'field_name' => 'user_status',
            'new_value' => $status,
            'reason' => "User account {$status} by administrator",
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$status} successfully.");
    }

    public function resetPassword(User $user)
    {
        if (!auth()->user()->hasPermission('manage_users')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You do not have permission to manage users.');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot reset password of super admin user.');
        }

        // Reset password to the configured default
        $user->password = Hash::make(config('app.default_password'));
        $user->must_change_password = true;
        $user->password_changed_at = null;
        $user->save();

        \App\Models\ActivityLog::create([
            'activity_type' => \App\Models\ActivityLog::TYPE_ADMIN_ACTION,
            'user_id' => $user->id,
            'edited_by' => auth()->user()->name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'severity' => \App\Models\ActivityLog::SEVERITY_DANGER,
            'field_name' => 'password_reset',
            'reason' => 'Password reset to default by administrator',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Password for {$user->name} has been reset to default. The user must change it on next login.");
    }
    
    public function permissions()
    {
        if (!auth()->user()->hasPermission('manage_permissions')) {
            abort(403, 'You do not have permission to manage role permissions.');
        }

        $service = app(PermissionService::class);

        $roles = User::getRoles();
        $modules = $service->modules();
        $catalog = $service->catalog();
        $matrix = $service->matrix();
        $defaults = $service->defaults();

        // The super administrator bypasses every permission check
        $matrix[User::ROLE_SUPER_ADMIN] = array_keys($catalog);

        return view('admin.permissions', compact('roles', 'modules', 'catalog', 'matrix', 'defaults'));
    }

    public function roles()
    {
        if (!auth()->user()->hasPermission('manage_permissions')) {
            abort(403, 'You do not have permission to manage roles.');
        }

        $service = app(PermissionService::class);
        $matrix = $service->matrix();
        $catalog = $service->catalog();

        $roles = collect(User::roleRecords())->map(function ($record, $key) use ($matrix, $catalog) {
            $record['users_count'] = User::where('role', $key)->count();
            $record['permissions_count'] = count(array_intersect($matrix[$key] ?? [], array_keys($catalog)));

            return $record;
        })->sortBy('sort_order')->values();

        return view('admin.roles', compact('roles'));
    }

    public function storeRole(Request $request)
    {
        if (!auth()->user()->hasPermission('manage_permissions')) {
            abort(403, 'You do not have permission to manage roles.');
        }

        $request->validate([
            'key' => ['required', 'string', 'max:50', 'regex:/^[a-z][a-z0-9_]*$/', 'unique:roles,key'],
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
        ]);

        $role = Role::create([
            'key' => $request->key,
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?: '#6c757d',
            'is_admin' => false,
            'is_active' => true,
            'sort_order' => (int) Role::max('sort_order') + 1,
        ]);

        $this->refreshPermissionCaches();

        ActivityLog::log(
            ActivityLog::TYPE_ADMIN_ACTION,
            null,
            'role_management',
            null,
            "Created role: {$role->name} ({$role->key})",
            ActivityLog::SEVERITY_INFO
        );

        return redirect()->route('admin.roles')
            ->with('success', "Role \"{$role->name}\" created. Grant it permissions on the permissions page.");
    }

    public function updateRole(Request $request, string $role)
    {
        if (!auth()->user()->hasPermission('manage_permissions')) {
            abort(403, 'You do not have permission to manage roles.');
        }

        $model = Role::where('key', $role)->first();

        if (!$model) {
            return redirect()->route('admin.roles')->with('error', 'Role not found.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'is_admin' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $isProtected = in_array($model->key, Role::protectedKeys(), true);

        $model->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?: $model->color,
            'is_admin' => $isProtected ? true : $request->boolean('is_admin'),
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->refreshPermissionCaches();

        ActivityLog::log(
            ActivityLog::TYPE_ADMIN_ACTION,
            null,
            'role_management',
            json_encode(['name' => $model->name, 'is_active' => $model->is_active]),
            json_encode(['name' => $request->name, 'is_active' => $model->is_active]),
            ActivityLog::SEVERITY_INFO,
            "Updated role: {$model->key}"
        );

        return redirect()->route('admin.roles')
            ->with('success', "Role \"{$model->name}\" updated.");
    }

    public function destroyRole(string $role)
    {
        if (!auth()->user()->hasPermission('manage_permissions')) {
            abort(403, 'You do not have permission to manage roles.');
        }

        if (in_array($role, Role::protectedKeys(), true)) {
            return redirect()->route('admin.roles')
                ->with('error', 'System roles cannot be deleted.');
        }

        $model = Role::where('key', $role)->first();

        if (!$model) {
            return redirect()->route('admin.roles')->with('error', 'Role not found.');
        }

        if (User::where('role', $role)->exists()) {
            return redirect()->route('admin.roles')
                ->with('error', 'This role is still assigned to users. Reassign them before deleting it.');
        }

        $name = $model->name;
        $model->delete();

        \App\Models\RolePermission::where('role', $role)->delete();

        $this->refreshPermissionCaches();

        ActivityLog::log(
            ActivityLog::TYPE_ADMIN_ACTION,
            null,
            'role_management',
            $name,
            null,
            ActivityLog::SEVERITY_WARNING,
            "Deleted role: {$role}"
        );

        return redirect()->route('admin.roles')
            ->with('success', "Role \"{$name}\" deleted.");
    }

    /**
     * Forget the cached roles and permissions
     */
    protected function refreshPermissionCaches(): void
    {
        User::flushRoleCache();
        app(PermissionService::class)->flush();
    }

    public function updatePermissions(Request $request)
    {
        if (!auth()->user()->hasPermission('manage_permissions')) {
            abort(403, 'You do not have permission to manage role permissions.');
        }

        $service = app(PermissionService::class);
        $known = array_keys($service->catalog());
        $submitted = $request->input('permissions', []);

        $matrix = [];
        foreach (array_keys(User::getRoles()) as $role) {
            $granted = $submitted[$role] ?? [];
            $matrix[$role] = is_array($granted)
                ? array_values(array_intersect($granted, $known))
                : [];
        }

        $before = $service->matrix();

        $service->sync($matrix);

        ActivityLog::log(
            ActivityLog::TYPE_ADMIN_ACTION,
            null,
            'role_permissions',
            json_encode($before),
            json_encode($service->matrix()),
            ActivityLog::SEVERITY_WARNING,
            'Updated role permissions'
        );

        return redirect()->route('admin.permissions')
            ->with('success', 'Role permissions updated successfully.');
    }

    public function resetPermissions()
    {
        if (!auth()->user()->hasPermission('manage_permissions')) {
            abort(403, 'You do not have permission to manage role permissions.');
        }

        $service = app(PermissionService::class);

        $service->reset();

        ActivityLog::log(
            ActivityLog::TYPE_ADMIN_ACTION,
            null,
            'role_permissions',
            json_encode($service->matrix()),
            json_encode($service->defaults()),
            ActivityLog::SEVERITY_WARNING,
            'Reset role permissions to defaults'
        );

        return redirect()->route('admin.permissions')
            ->with('success', 'Role permissions restored to the default configuration.');
    }

    public function activityLogs(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50], true)) {
            $perPage = 10;
        }

        $query = $this->activityLogQuery();

        // Default: Show all, but allow time-based filtering
        $range = $request->get('range', 'all');
        if ($range !== 'all') {
            $startDate = match($range) {
                'today' => now()->startOfDay(),
                'yesterday' => now()->subDay()->startOfDay(),
                'week' => now()->subDays(7)->startOfDay(),
                'month' => now()->subDays(30)->startOfDay(),
                'year' => now()->subDays(365)->startOfDay(),
                default => null
            };
            
            if ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }
        }

        // Filter by activity type
        if ($request->has('type') && $request->type !== '') {
            $query->where('activity_type', $request->type);
        }

        // Filter by severity
        if ($request->has('severity') && $request->severity !== '') {
            $query->where('severity', $request->severity);
        }

        // Search in edited_by, reason, or IP
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('edited_by', 'like', '%' . $search . '%')
                  ->orWhere('reason', 'like', '%' . $search . '%')
                  ->orWhere('field_name', 'like', '%' . $search . '%')
                  ->orWhere('ip_address', 'like', '%' . $search . '%');
            });
        }

        // If export is requested
        if ($request->has('export')) {
            $exportLogs = $query->get();
            $reportMeta = ReportMeta::build($exportLogs, $this->activityLogFilters($request), null, null, 'created_at');

            return $this->exportActivityLogs($exportLogs, $range, $reportMeta);
        }

        $logs = $query->paginate($perPage)->appends($request->query());

        $reportMeta = ReportMeta::build(
            $logs->getCollection(),
            $this->activityLogFilters($request),
            null,
            null,
            'created_at',
            $logs->total()
        );

        $statGroups = [
            'total' => $this->activityLogQuery()->get(),
            'logins' => $this->activityLogQuery()->where('activity_type', 'login')->get(),
            'updates' => $this->activityLogQuery()->where('activity_type', 'contract_update')->get(),
            'critical' => $this->activityLogQuery()->where('severity', 'danger')->get(),
        ];

        return view('admin.activity_logs.index', compact('logs', 'statGroups', 'reportMeta'));
    }

    private function activityLogQuery()
    {
        return ActivityLog::with(['contract' => function ($q) {
            $q->withTrashed();
        }, 'actor'])->latest();
    }

    /**
     * @return array<int, string|null>
     */
    private function activityLogFilters(Request $request): array
    {
        $typeLabels = [
            'login' => 'Successful logins',
            'failed_login' => 'Failed logins',
            'logout' => 'Logouts',
            'contract_create' => 'Insertions',
            'contract_update' => 'Updates',
            'contract_delete' => 'Deletions',
            'system_error' => 'System errors',
            'admin_action' => 'Admin actions',
            'password_reset_request' => 'Password resets',
        ];
        $severityLabels = ['info' => 'Info', 'warning' => 'Warning', 'danger' => 'Critical'];
        $rangeLabels = [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'week' => 'Last 7 days',
            'month' => 'Last 30 days',
            'year' => 'Last year',
        ];

        return [
            isset($rangeLabels[$request->input('range')]) ? 'Range: '.$rangeLabels[$request->input('range')] : null,
            isset($typeLabels[$request->input('type')]) ? 'Category: '.$typeLabels[$request->input('type')] : null,
            isset($severityLabels[$request->input('severity')]) ? 'Severity: '.$severityLabels[$request->input('severity')] : null,
            $request->filled('search') ? 'Search: '.$request->input('search') : null,
        ];
    }

    public function userHistory(User $user)
    {
        $logs = ActivityLog::where('user_id', $user->id)
            ->orWhere('edited_by', $user->email)
            ->latest()
            ->limit(50)
            ->get();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'logs' => $logs->map(function ($log) {
                return [
                    'type' => $log->activity_type,
                    'description' => $log->description,
                    'severity' => $log->severity,
                    'ip_address' => $log->ip_address,
                    'edited_by' => $log->edited_by,
                    'reason' => $log->reason,
                    'created_at' => $log->created_at ? $log->created_at->format('Y-m-d H:i') : null,
                ];
            })->values(),
        ]);
    }

    private function exportActivityLogs($logs, $range, ?array $reportMeta = null)
    {
        $filename = "activity_report_" . ($range ?: 'all') . "_" . date('Y-m-d') . ".csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Timestamp', 'Type', 'Severity', 'Actor', 'Description', 'Reason', 'IP Address', 'User Agent'];

        $callback = function() use($logs, $columns, $reportMeta) {
            $file = fopen('php://output', 'w');

            foreach (ReportMeta::sheetLines($reportMeta, 'Activity Logs') as $line) {
                fputcsv($file, [$line]);
            }
            if ($reportMeta) {
                fputcsv($file, []);
            }

            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at,
                    $log->activity_type,
                    $log->severity,
                    $log->edited_by,
                    $log->description,
                    $log->reason,
                    $log->ip_address,
                    $log->user_agent
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
