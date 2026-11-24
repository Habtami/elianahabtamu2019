<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_type',
        'severity',
        'user_id',
        'contract_id',
        'driver_id',
        'field_name',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
        'edited_by',
        'reason',
    ];

    // Activity Types
    const TYPE_LOGIN = 'login';

    const TYPE_FAILED_LOGIN = 'failed_login';

    const TYPE_LOGOUT = 'logout';

    const TYPE_CONTRACT_CREATE = 'contract_create';

    const TYPE_CONTRACT_UPDATE = 'contract_update';

    const TYPE_CONTRACT_DELETE = 'contract_delete';

    const TYPE_DRIVER_CREATE = 'driver_create';

    const TYPE_DRIVER_UPDATE = 'driver_update';

    const TYPE_DRIVER_STATUS = 'driver_status';

    const TYPE_DRIVER_DELETE = 'driver_delete';

    const TYPE_ADMIN_ACTION = 'admin_action';

    const TYPE_SYSTEM_ERROR = 'system_error';

    const TYPE_PASSWORD_RESET_REQUEST = 'password_reset_request';

    const TYPE_PASSWORD_CHANGE = 'password_change';

    // Severities
    const SEVERITY_INFO = 'info';

    const SEVERITY_WARNING = 'warning';

    const SEVERITY_DANGER = 'danger';

    protected $casts = [
        'old_value' => 'string',
        'new_value' => 'string',
        'created_at' => 'datetime',
    ];

    // Relationship with contract
    public function contract()
    {
        return $this->belongsTo(Contract::class)->withTrashed();
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class)->withTrashed();
    }

    // Relationship with user (actor)
    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Get description of the activity
    public function getDescriptionAttribute()
    {
        switch ($this->activity_type) {
            case self::TYPE_LOGIN:
                return 'User logged into the system';
            case self::TYPE_FAILED_LOGIN:
                return 'Failed login attempt';
            case self::TYPE_LOGOUT:
                return 'User logged out of the system';
            case self::TYPE_CONTRACT_CREATE:
                return 'Created new contract: '.($this->contract ? $this->contract->name : 'N/A');
            case self::TYPE_CONTRACT_UPDATE:
                $fieldName = ucfirst(str_replace('_', ' ', $this->field_name));
                $contractName = $this->contract ? $this->contract->name : 'a deleted contract';

                return "Updated {$fieldName} for {$contractName}";
            case self::TYPE_CONTRACT_DELETE:
                return 'Deleted contract';
            case self::TYPE_DRIVER_CREATE:
                return 'Driver registered: '.($this->driver?->name ?? 'a deleted driver');
            case self::TYPE_DRIVER_UPDATE:
                $fieldName = ucfirst(str_replace('_', ' ', $this->field_name));

                return "Updated {$fieldName} for ".($this->driver?->name ?? 'a deleted driver');
            case self::TYPE_DRIVER_STATUS:
                return 'Changed status for '.($this->driver?->name ?? 'a deleted driver');
            case self::TYPE_DRIVER_DELETE:
                return 'Deleted driver';
            case self::TYPE_ADMIN_ACTION:
                $labels = [
                    'user_create' => 'User account created',
                    'user_update' => 'User account updated',
                    'user_delete' => 'User account deleted',
                    'user_status' => 'User account status changed',
                    'password_reset' => 'Password reset to default',
                ];
                if (isset($labels[$this->field_name])) {
                    return $labels[$this->field_name];
                }

                return 'Performed administrative action: '.$this->field_name;
            case self::TYPE_SYSTEM_ERROR:
                return 'System error occurred: '.$this->field_name;
            case self::TYPE_PASSWORD_RESET_REQUEST:
                return 'Requested password reset';
            case self::TYPE_PASSWORD_CHANGE:
                return 'Changed own password';
            default:
                return $this->field_name ?? 'Unknown activity';
        }
    }

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    /**
     * Helper to log activities
     */
    public static function log($type, $contractId = null, $fieldName = null, $oldValue = null, $newValue = null, $severity = self::SEVERITY_INFO, $reason = null, $driverId = null)
    {
        return self::create([
            'activity_type' => $type,
            'severity' => $severity,
            'user_id' => auth()->id(),
            'contract_id' => $contractId,
            'driver_id' => $driverId,
            'field_name' => $fieldName ?? $type,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'edited_by' => auth()->user() ? auth()->user()->name : 'System',
            'reason' => $reason,
        ]);
    }

    // Get formatted old value
    public function getFormattedOldValue()
    {
        if ($this->field_name === 'performance_guarantee') {
            $data = json_decode($this->old_value, true);
            if ($data && isset($data['amount']) && isset($data['end_date'])) {
                return 'Amount: '.number_format($data['amount'], 2).', End Date: '.$data['end_date'];
            }
        }

        return $this->old_value ?? 'N/A';
    }

    // Get formatted new value
    public function getFormattedNewValue()
    {
        if ($this->field_name === 'performance_guarantee') {
            $data = json_decode($this->new_value, true);
            if ($data && isset($data['amount']) && isset($data['end_date'])) {
                return 'Amount: '.number_format($data['amount'], 2).', End Date: '.$data['end_date'];
            }
        }

        return $this->new_value ?? 'N/A';
    }
}
