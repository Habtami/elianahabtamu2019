<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'name',
        'description',
        'department',
        'contract_type',
        'supplier_name',
        'material_group',
        'contract_amount',
        'start_date',
        'end_date',
        'alert_days',
        'performance_bond_type',
        'issued_by',

        'performance_guarantee_amount',
        'currency_type',
        'performance_guarantee_end_date',
        'performance_guarantee_alert_days',
        'created_by',
        'status',
        'extended_count'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',

        'performance_guarantee_end_date' => 'date',
        'alert_days' => 'integer',
        'performance_guarantee_alert_days' => 'integer',
        'performance_guarantee_amount' => 'decimal:2',
        'contract_amount' => 'decimal:2',
        'extended_count' => 'integer'
    ];

    protected static function booted()
    {
        static::created(function ($contract) {
            ActivityLog::log(
                ActivityLog::TYPE_CONTRACT_CREATE,
                $contract->id,
                'contract',
                null,
                $contract->name,
                ActivityLog::SEVERITY_INFO
            );
        });

        static::deleted(function ($contract) {
            ActivityLog::log(
                ActivityLog::TYPE_CONTRACT_DELETE,
                $contract->id,
                'contract',
                $contract->name,
                null,
                ActivityLog::SEVERITY_WARNING
            );
        });
    }

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRING = 'expiring';
    const STATUS_EXPIRED = 'expired';

    // Get status options
    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_EXPIRING => 'Expiring Soon',
            self::STATUS_EXPIRED => 'Expired',
        ];
    }

    // Get status display name
    public function getStatusDisplayName()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    // Get department display name
    public function getDepartmentDisplayName()
    {
        return User::getDepartments()[$this->department] ?? $this->department;
    }

    // Material groups available for contracts
    public static function getMaterialGroups()
    {
        return MaterialGroup::activeNames();
    }

    // Contract types available for contracts
    public static function getContractTypes()
    {
        return ContractType::activeNames();
    }

    // Currency types available for contracts
    public static function getCurrencyTypes()
    {
        return CurrencyType::activeNames();
    }

    // Performance bond types available for contracts
    public static function getPerformanceBondTypes()
    {
        return PerformanceBondType::activeNames();
    }

    // Relationship with supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relationship with the contract closing record
    public function closure()
    {
        return $this->hasOne(ContractClosure::class);
    }

    // Relationship with creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship with activity logs
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Relationship with contract histories
    public function histories()
    {
        return $this->hasMany(ContractHistory::class);
    }

    // Relationship with contract end date histories
    public function contractEndDateHistories()
    {
        return $this->hasMany(ActivityLog::class)->where('field_name', 'contract_end_date');
    }

    // Relationship with performance guarantee histories
    public function performanceGuaranteeHistories()
    {
        return $this->hasMany(ActivityLog::class)->where('field_name', 'performance_guarantee');
    }

    // Get performance guarantee edit count
    public function getPerformanceGuaranteeEditCountAttribute()
    {
        return $this->performanceGuaranteeHistories()->count();
    }

    // Calculate days until contract end
    public function getDaysUntilContractEnd()
    {
        if (!$this->end_date) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($this->end_date->copy()->startOfDay(), false);
    }

    // Calculate days until performance guarantee end
    public function getDaysUntilPerformanceGuaranteeEnd()
    {
        if (!$this->performance_guarantee_end_date) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->performance_guarantee_end_date->copy()->startOfDay(), false);
    }

    // Check if contract is expiring soon
    public function isExpiringSoon()
    {
        $daysUntilEnd = $this->getDaysUntilContractEnd();
        return $daysUntilEnd > 0 && $daysUntilEnd <= $this->alert_days;
    }

    // Check if contract should alert end date
    public function shouldAlertContractEndDate()
    {
        return $this->isExpiringSoon();
    }

    // Check if performance guarantee is expiring soon
    public function isPerformanceGuaranteeExpiringSoon()
    {
        if (!$this->performance_guarantee_end_date) {
            return false;
        }
        $daysUntilEnd = $this->getDaysUntilPerformanceGuaranteeEnd();
        return $daysUntilEnd > 0 && $daysUntilEnd <= $this->performance_guarantee_alert_days;
    }

    // Check if performance guarantee should alert
    public function shouldAlertPerformanceGuarantee()
    {
        return $this->isPerformanceGuaranteeExpiringSoon();
    }

    // Update contract status
    public function updateStatus()
    {
        $daysUntilEnd = $this->getDaysUntilContractEnd();
        
        if ($daysUntilEnd < 0) {
            $this->status = self::STATUS_EXPIRED;
        } elseif ($daysUntilEnd <= $this->alert_days) {
            $this->status = self::STATUS_EXPIRING;
        } else {
            $this->status = self::STATUS_ACTIVE;
        }
        
        $this->save();
    }

    // Check if user can edit this contract
    public function canUserEdit(User $user)
    {
        if ($user->isAdmin()) {
            return false; // Admin cannot edit contracts
        }

        if ($user->role === User::ROLE_FINANCE || $user->role === User::ROLE_CMD) {
            return $this->department === $user->getUserDepartment();
        }

        if ($user->role === User::ROLE_GENERAL_SERVICE) {
            return $this->created_by === $user->id;
        }

        return false; // Managers cannot edit
    }

    // Check if user can delete this contract
    public function canUserDelete(User $user)
    {
        // Only creator can delete their contract
        return $this->created_by === $user->id && !$user->isAdmin();
    }

    // Extend contract end date
    public function extendContractEnd($newEndDate, $reason, $editedBy = null)
    {
        $oldDate = $this->end_date;
        $this->end_date = $newEndDate;
        $this->extended_count = ($this->extended_count ?? 0) + 1;
        $this->save();

        // Log the activity
        ActivityLog::log(
            ActivityLog::TYPE_CONTRACT_UPDATE,
            $this->id,
            'contract_end_date',
            $oldDate,
            $newEndDate,
            ActivityLog::SEVERITY_INFO,
            $reason
        );

        $this->updateStatus();
    }

    // Extend performance guarantee end date
    public function extendPerformanceGuarantee($newAmount, $newEndDate, $reason, $editedBy = null)
    {
        $oldAmount = $this->performance_guarantee_amount;
        $oldDate = $this->performance_guarantee_end_date;
        $this->performance_guarantee_amount = $newAmount;
        $this->performance_guarantee_end_date = $newEndDate;
        $this->save();

        // Log the activity
        ActivityLog::log(
            ActivityLog::TYPE_CONTRACT_UPDATE,
            $this->id,
            'performance_guarantee',
            json_encode(['amount' => $oldAmount, 'end_date' => $oldDate]),
            json_encode(['amount' => $newAmount, 'end_date' => $newEndDate]),
            ActivityLog::SEVERITY_INFO,
            $reason
        );
    }

    // Scope for active contracts
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // Scope for expiring contracts
    public function scopeExpiring($query)
    {
        return $query->where('status', self::STATUS_EXPIRING);
    }

    // Scope for expired contracts
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    // Scope for user's allowed departments
    public function scopeForUser($query, User $user)
    {
        $allowedDepartments = $user->getAllowedDepartments();
        
        if ($user->canViewAllContracts()) {
            return $query->whereIn('department', $allowedDepartments);
        }
        
        // Non-admin users can only see contracts from their allowed departments
        return $query->whereIn('department', $allowedDepartments);
    }
}