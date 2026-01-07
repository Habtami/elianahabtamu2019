<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                return redirect()->route('dashboard')
                    ->with('error', 'You are not authorized to access the admin panel.');
            }
            return $next($request);
        });
    }
    
    public function index(Request $request)
    {
        $query = User::query(); // Show all users including admins

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
        $perPage = $request->get('per_page', 10);
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $users = $query->paginate($perPage)->appends($request->query());

        $roles = User::getRoles();
        $departments = User::getDepartments();

        return view('admin.users.index', compact('users', 'roles', 'departments', 'sortBy', 'sortDirection', 'perPage'));
    }
    
    public function create()
    {
        $roles = User::getRoles();

        $departments = User::getDepartments();

        return view('admin.users.create', compact('roles', 'departments'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:super_admin,admin,general_service,cmd,finance,gs_manager,cmd_manager',
            'department' => 'nullable|string|in:ict,cmd,general_service,finance',
            'password' => 'required|string|min:8|confirmed',
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

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $role,
            'department' => $department,
            'password' => Hash::make($request->password),
            'status' => true,
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }
    
    public function edit(User $user)
    {
        $roles = User::getRoles();

        $departments = User::getDepartments();

        return view('admin.users.edit', compact('user', 'roles', 'departments'));
    }
    
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:super_admin,admin,general_service,cmd,finance,gs_manager,cmd_manager',
            'department' => 'required|string|in:ict,cmd,general_service,finance',
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
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }
    
    public function destroy(User $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete super admin user.');
        }

        // Check if user has contracts
        if ($user->contracts()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete user with existing contracts.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
    
    public function toggleStatus(User $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot change status of super admin user.');
        }

        $user->status = !$user->status;
        $user->save();

        $status = $user->status ? 'activated' : 'deactivated';

        return redirect()->route('admin.users.index')
            ->with('success', "User {$status} successfully.");
    }
    
    public function permissions()
    {
        $roles = User::getRoles();
        $permissions = [
            'create_contracts' => ['general_service', 'cmd', 'finance'],
            'edit_all_contracts' => ['finance'],
            'view_all_contracts' => ['super_admin', 'admin', 'finance', 'gs_manager', 'cmd_manager'],
            'admin_panel' => ['super_admin', 'admin'],
            'view_reports' => ['super_admin', 'admin', 'finance', 'gs_manager', 'cmd_manager'],
            'manage_users' => ['super_admin', 'admin'],
        ];

        return view('admin.permissions', compact('roles', 'permissions'));
    }
}
