<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'department',
        'start_date',
        'end_date',
        'alert_days',
        'performance_guarantee_amount',
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
        'extended_count' => 'integer'
    ];

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

    // Relationship with performance guarantee histories
    public function performanceGuaranteeHistories()
    {
        return $this->hasMany(ActivityLog::class)->where('field_name', 'performance_guarantee');
    }

    // Calculate days until contract end
    public function getDaysUntilContractEnd()
    {
        return now()->diffInDays($this->end_date, false);
    }

    // Calculate days until performance guarantee end
    public function getDaysUntilPerformanceGuaranteeEnd()
    {
        if (!$this->performance_guarantee_end_date) {
            return null;
        }
        return now()->diffInDays($this->performance_guarantee_end_date, false);
    }

    // Check if contract is expiring soon
    public function isExpiringSoon()
    {
        $daysUntilEnd = $this->getDaysUntilContractEnd();
        return $daysUntilEnd > 0 && $daysUntilEnd <= $this->alert_days;
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
        ActivityLog::create([
            'contract_id' => $this->id,
            'field_name' => 'contract_end_date',
            'old_value' => $oldDate,
            'new_value' => $newEndDate,
            'edited_by' => $editedBy ?: auth()->user()->name,
            'reason' => $reason
        ]);

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
        ActivityLog::create([
            'contract_id' => $this->id,
            'field_name' => 'performance_guarantee',
            'old_value' => json_encode(['amount' => $oldAmount, 'end_date' => $oldDate]),
            'new_value' => json_encode(['amount' => $newAmount, 'end_date' => $newEndDate]),
            'edited_by' => $editedBy ?: auth()->user()->name,
            'reason' => $reason
        ]);
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