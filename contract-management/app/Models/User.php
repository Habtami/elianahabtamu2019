<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'department',
        'must_change_password',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean',
        'must_change_password' => 'boolean',
        'password_changed_at' => 'datetime',
    ];

    // Role constants
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_GENERAL_SERVICE = 'general_service';
    const ROLE_CMD = 'cmd';
    const ROLE_FINANCE = 'finance';
    const ROLE_GS_MANAGER = 'gs_manager';
    const ROLE_CMD_MANAGER = 'cmd_manager';

    // Department constants
    const DEPARTMENT_ICT = 'ict';
    const DEPARTMENT_CMD = 'cmd';
    const DEPARTMENT_GENERAL_SERVICE = 'general_service';
    const DEPARTMENT_FINANCE = 'finance';
    const DEPARTMENT_DDG_FPA_GS = 'ddg_fpa_gs';

    // Cached role records (key => metadata)
    protected static ?array $roleCache = null;

    // Roles that ship with the system, used as fallback when the table is missing
    public static function defaultRoles()
    {
        return [
            self::ROLE_SUPER_ADMIN => 'Super Administrator',
            self::ROLE_ADMIN => 'System Administrator',
            self::ROLE_GENERAL_SERVICE => 'General Service Officer',
            self::ROLE_CMD => 'CMD Officer',
            self::ROLE_FINANCE => 'Finance Officer',
            self::ROLE_GS_MANAGER => 'GS Manager',
            self::ROLE_CMD_MANAGER => 'CMD Manager',
        ];
    }

    // All role records, including deactivated ones
    public static function roleRecords(): array
    {
        if (static::$roleCache !== null) {
            return static::$roleCache;
        }

        $records = [];

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('roles')) {
                foreach (Role::orderBy('sort_order')->orderBy('id')->get() as $role) {
                    $records[$role->key] = [
                        'key' => $role->key,
                        'name' => $role->name,
                        'description' => $role->description,
                        'color' => $role->color ?: '#6c757d',
                        'is_admin' => (bool) $role->is_admin,
                        'is_active' => (bool) $role->is_active,
                        'sort_order' => (int) $role->sort_order,
                    ];
                }
            }
        } catch (\Throwable $e) {
            $records = [];
        }

        if (empty($records)) {
            foreach (self::defaultRoles() as $key => $name) {
                $records[$key] = [
                    'key' => $key,
                    'name' => $name,
                    'description' => null,
                    'color' => '#6c757d',
                    'is_admin' => in_array($key, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN], true),
                    'is_active' => true,
                    'sort_order' => 0,
                ];
            }
        }

        return static::$roleCache = $records;
    }

    // Forget the cached roles, for example after a role was changed
    public static function flushRoleCache(): void
    {
        static::$roleCache = null;
    }

    // Get all active roles
    public static function getRoles()
    {
        $roles = [];

        foreach (self::roleRecords() as $key => $record) {
            if ($record['is_active']) {
                $roles[$key] = $record['name'];
            }
        }

        return $roles;
    }

    // Get all roles, including the deactivated ones
    public static function getAllRoles()
    {
        $roles = [];

        foreach (self::roleRecords() as $key => $record) {
            $roles[$key] = $record['name'];
        }

        return $roles;
    }

    // Get all departments
    public static function getDepartments()
    {
        return [
            self::DEPARTMENT_ICT => 'ICT Department',
            self::DEPARTMENT_CMD => 'CMD',
            self::DEPARTMENT_GENERAL_SERVICE => 'General Service',
            self::DEPARTMENT_FINANCE => 'Finance',
            self::DEPARTMENT_DDG_FPA_GS => 'DDG F,PA & GS',
        ];
    }

    // Departments that can own contracts
    public static function getContractDepartments()
    {
        return [
            self::DEPARTMENT_CMD => 'CMD',
            self::DEPARTMENT_GENERAL_SERVICE => 'General Service',
            self::DEPARTMENT_FINANCE => 'Finance',
        ];
    }

    // Get display name for role
    public function getRoleDisplayName()
    {
        return self::getAllRoles()[$this->role] ?? $this->role;
    }

    // Get display name for department
    public function getDepartmentDisplayName()
    {
        return self::getDepartments()[$this->department] ?? $this->department;
    }

    // Check if user is admin
    public function isAdmin()
    {
        $record = self::roleRecords()[$this->role] ?? null;

        if ($record) {
            return $record['is_admin'];
        }

        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN]);
    }

    // Check if user is super admin
    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    // Check if user is active
    public function isActive()
    {
        return $this->status;
    }

    // Landing page for this user's role
    public function homeRoute(): string
    {
        return self::routeForRole($this->role);
    }

    public static function routeForRole(?string $role): string
    {
        switch ($role) {
            case self::ROLE_ADMIN:
                return 'admin.users.index';
            case self::ROLE_GS_MANAGER:
            case self::ROLE_CMD_MANAGER:
                return 'contracts.reports';
            default:
                return 'contracts.index';
        }
    }

    // Check if the role of this user holds the given permission
    public function hasPermission(string $permission): bool
    {
        return app(\App\Services\PermissionService::class)->allows($this->role, $permission);
    }

    // Check if user can create contracts
    public function canCreateContracts()
    {
        return $this->hasPermission('create_contracts');
    }

    // Check if user can edit all contracts
    public function canEditAllContracts()
    {
        return $this->hasPermission('edit_all_contracts');
    }

    // Check if user can view all contracts
    public function canViewAllContracts()
    {
        return $this->hasPermission('view_all_contracts');
    }

    // Get user's allowed departments for viewing
    public function getAllowedDepartments()
    {
        switch ($this->role) {
            case self::ROLE_SUPER_ADMIN:
            case self::ROLE_ADMIN:
            case self::ROLE_FINANCE:
                // Admins and finance can view all departments
                return array_keys(self::getDepartments());
            case self::ROLE_GENERAL_SERVICE:
            case self::ROLE_GS_MANAGER:
                // General service users can only view general service department
                return [self::DEPARTMENT_GENERAL_SERVICE];
            case self::ROLE_CMD:
            case self::ROLE_CMD_MANAGER:
                // CMD users can only view CMD department
                return [self::DEPARTMENT_CMD];
            default:
                // Custom roles are not bound to a single department
                return array_keys(self::getDepartments());
        }
    }

    // Relationship with contracts
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'created_by');
    }

    // Tire Tracking access-control helpers
    public function canAccessTireTracking()
    {
        return $this->hasPermission('access_tire_tracking');
    }

    public function canManageTires()
    {
        return $this->hasPermission('manage_tires');
    }

    public function canViewTireReports()
    {
        return $this->hasPermission('view_tire_reports');
    }

    public function canViewBatteryReports()
    {
        return $this->hasPermission('view_battery_reports');
    }

    // Battery Given and Return access-control helpers
    public function canViewBatteries()
    {
        return $this->hasPermission('view_batteries');
    }

    public function canGiveBatteries()
    {
        return $this->hasPermission('give_batteries');
    }

    public function canReturnBatteries()
    {
        return $this->hasPermission('return_batteries');
    }

    public function canEditBatteries()
    {
        return $this->hasPermission('edit_batteries');
    }

    public function canDeleteBatteries()
    {
        return $this->hasPermission('delete_batteries');
    }

    public function canExportBatteries()
    {
        return $this->hasPermission('export_batteries');
    }

    // Purchase Order Processing access-control helpers
    public function canViewPurchaseOrders()
    {
        return $this->hasPermission('view_purchase_orders');
    }

    public function canCreatePurchaseOrders()
    {
        return $this->hasPermission('create_purchase_orders');
    }

    public function canApprovePurchaseOrdersGs()
    {
        return $this->hasPermission('approve_purchase_orders_gs');
    }

    public function canApprovePurchaseOrdersDdg()
    {
        return $this->hasPermission('approve_purchase_orders_ddg');
    }

    // Payment Request Processing access-control helpers
    public function canViewPaymentRequests()
    {
        return $this->hasPermission('view_payment_requests');
    }

    public function canCreatePaymentRequests()
    {
        return $this->hasPermission('create_payment_requests');
    }

    public function canApprovePaymentRequestsGs()
    {
        return $this->hasPermission('approve_payment_requests_gs');
    }

    public function canCompletePaymentRequestsFinance()
    {
        return $this->hasPermission('complete_payment_requests_finance');
    }

    // Contract Closing access-control helpers
    public function canViewContractClosures()
    {
        return $this->hasPermission('view_contract_closures');
    }

    public function canCreateContractClosures()
    {
        return $this->hasPermission('create_contract_closures');
    }

    public function canCompleteContractClosures()
    {
        return $this->hasPermission('complete_contract_closures');
    }

    public function canViewProcurementReport()
    {
        return $this->hasPermission('view_procurement_report');
    }

    // Purchasing Daily Activity access-control helpers
    public function canViewPurchasingActivities()
    {
        return $this->hasPermission('view_purchasing_activities');
    }

    public function canCreatePurchasingActivities()
    {
        return $this->hasPermission('create_purchasing_activities');
    }

    public function canCompletePurchasingActivities()
    {
        return $this->hasPermission('complete_purchasing_activities');
    }

    public function canPerformTireOperations()
    {
        return $this->hasPermission('perform_tire_operations');
    }

    public function canEditDrivers()
    {
        return $this->hasPermission('edit_drivers');
    }

    // Get user's own department
    public function getUserDepartment()
    {
        if ($this->role === self::ROLE_GENERAL_SERVICE) {
            return self::DEPARTMENT_GENERAL_SERVICE;
        } elseif ($this->role === self::ROLE_CMD) {
            return self::DEPARTMENT_CMD;
        } elseif ($this->role === self::ROLE_FINANCE) {
            return self::DEPARTMENT_FINANCE;
        }
        return null;
    }
}
