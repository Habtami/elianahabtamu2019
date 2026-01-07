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
        'department'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean',
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

    // Get all roles
    public static function getRoles()
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

    // Get all departments
    public static function getDepartments()
    {
        return [
            self::DEPARTMENT_ICT => 'ICT Department',
            self::DEPARTMENT_CMD => 'CMD',
            self::DEPARTMENT_GENERAL_SERVICE => 'General Service',
            self::DEPARTMENT_FINANCE => 'Finance',
        ];
    }

    // Get display name for role
    public function getRoleDisplayName()
    {
        return self::getRoles()[$this->role] ?? $this->role;
    }

    // Get display name for department
    public function getDepartmentDisplayName()
    {
        return self::getDepartments()[$this->department] ?? $this->department;
    }

    // Check if user is admin
    public function isAdmin()
    {
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

    // Check if user can create contracts
    public function canCreateContracts()
    {
        return in_array($this->role, [
            self::ROLE_GENERAL_SERVICE,
            self::ROLE_CMD,
            self::ROLE_FINANCE
        ]);
    }

    // Check if user can edit all contracts
    public function canEditAllContracts()
    {
        return $this->role === self::ROLE_ADMIN || $this->role === self::ROLE_FINANCE;
    }

    // Check if user can view all contracts
    public function canViewAllContracts()
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
            self::ROLE_GENERAL_SERVICE,
            self::ROLE_CMD,
            self::ROLE_FINANCE,
            self::ROLE_GS_MANAGER,
            self::ROLE_CMD_MANAGER
        ]);
    }

    // Get user's allowed departments for viewing
    public function getAllowedDepartments()
    {
        switch ($this->role) {
            case self::ROLE_SUPER_ADMIN:
            case self::ROLE_ADMIN:
            case self::ROLE_GENERAL_SERVICE:
            case self::ROLE_CMD:
            case self::ROLE_FINANCE:
            case self::ROLE_GS_MANAGER:
            case self::ROLE_CMD_MANAGER:
                return array_keys(self::getDepartments());
            default:
                return [];
        }
    }

    // Relationship with contracts
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'created_by');
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