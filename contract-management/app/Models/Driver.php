<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'license_number',
        'phone',
        'email',
        'photo',
        'license_expiry',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'license_expiry' => 'date',
    ];

    protected const AUDIT_FIELDS = [
        'name',
        'license_number',
        'phone',
        'email',
        'photo',
        'license_expiry',
        'status',
    ];

    protected array $pendingAuditChanges = [];

    protected static function booted(): void
    {
        static::created(function (Driver $driver) {
            ActivityLog::log(
                ActivityLog::TYPE_DRIVER_CREATE,
                null,
                'driver',
                null,
                $driver->auditSnapshot(),
                ActivityLog::SEVERITY_INFO,
                'Driver registered',
                $driver->id
            );
        });

        static::updating(function (Driver $driver) {
            // Capture originals before Eloquent clears the dirty set.
            $driver->pendingAuditChanges = collect($driver->getDirty())
                ->only(self::AUDIT_FIELDS)
                ->map(fn ($value, $field) => [
                    'old' => $driver->formatAuditValue($field, $driver->getRawOriginal($field)),
                    'new' => $driver->formatAuditValue($field, $value),
                ])
                ->all();
        });

        static::updated(function (Driver $driver) {
            foreach ($driver->pendingAuditChanges as $field => $change) {
                $isStatusChange = count($driver->pendingAuditChanges) === 1 && $field === 'status';

                ActivityLog::log(
                    $isStatusChange ? ActivityLog::TYPE_DRIVER_STATUS : ActivityLog::TYPE_DRIVER_UPDATE,
                    null,
                    $field,
                    $change['old'],
                    $change['new'],
                    $isStatusChange ? ActivityLog::SEVERITY_WARNING : ActivityLog::SEVERITY_INFO,
                    $isStatusChange ? 'Driver status changed' : 'Driver details updated',
                    $driver->id
                );
            }

            $driver->pendingAuditChanges = [];
        });

        static::deleted(function (Driver $driver) {
            ActivityLog::log(
                ActivityLog::TYPE_DRIVER_DELETE,
                null,
                'driver',
                $driver->auditSnapshot(),
                null,
                ActivityLog::SEVERITY_WARNING,
                'Driver deleted',
                $driver->id
            );
        });
    }

    protected function auditSnapshot(): string
    {
        $snapshot = collect(self::AUDIT_FIELDS)
            ->mapWithKeys(fn (string $field) => [
                $field => $this->formatAuditValue($field, $this->getAttribute($field)),
            ]);

        return json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    protected function formatAuditValue(string $field, $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($field === 'status') {
            return in_array($value, [true, 1, '1', 'true'], true) ? 'Active' : 'Inactive';
        }

        if ($field === 'license_expiry') {
            if ($value instanceof DateTimeInterface) {
                return $value->format('Y-m-d');
            }

            return substr((string) $value, 0, 10);
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        return (string) $value;
    }

    // Status constants
    const STATUS_ACTIVE = true;

    const STATUS_INACTIVE = false;

    // Relationship with tires (many-to-many through tire_gives)
    public function tires()
    {
        return $this->belongsToMany(Tire::class, 'tire_gives', 'driver_id', 'tire_id')
            ->withPivot(['give_date', 'given_kilometer', 'plate_number', 'is_returned'])
            ->withTimestamps();
    }

    // Relationship with tire gives
    public function tireGives()
    {
        return $this->hasMany(TireGive::class);
    }

    // Relationship with tire returns
    public function tireReturns()
    {
        return $this->hasManyThrough(TireReturn::class, TireGive::class, 'driver_id', 'tire_give_id');
    }

    // Check if license is expiring soon (within 30 days)
    public function isLicenseExpiringSoon()
    {
        if (! $this->license_expiry) {
            return false;
        }
        $days = now()->diffInDays($this->license_expiry, false);

        return $days > 0 && $days <= 30;
    }

    // Check if license is expired
    public function isLicenseExpired()
    {
        if (! $this->license_expiry) {
            return false;
        }

        return now()->greaterThan($this->license_expiry);
    }

    // Get days until license expiry
    public function getDaysUntilLicenseExpiry()
    {
        if (! $this->license_expiry) {
            return null;
        }

        return now()->diffInDays($this->license_expiry, false);
    }

    // Get active tires count
    public function getActiveTiresCountAttribute()
    {
        return $this->tireGives()->where('is_returned', false)->count();
    }

    // Scope for active drivers
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // Scope for inactive drivers
    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    // Scope for license expiring soon
    public function scopeLicenseExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('license_expiry')
            ->whereDate('license_expiry', '>', now())
            ->whereDate('license_expiry', '<=', now()->addDays($days));
    }

    // Scope for expired licenses
    public function scopeLicenseExpired($query)
    {
        return $query->whereNotNull('license_expiry')
            ->whereDate('license_expiry', '<', now());
    }
}
