<?php

namespace App\Models;

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
        if (!$this->license_expiry) {
            return false;
        }
        $days = now()->diffInDays($this->license_expiry, false);
        return $days > 0 && $days <= 30;
    }

    // Check if license is expired
    public function isLicenseExpired()
    {
        if (!$this->license_expiry) {
            return false;
        }
        return now()->greaterThan($this->license_expiry);
    }

    // Get days until license expiry
    public function getDaysUntilLicenseExpiry()
    {
        if (!$this->license_expiry) {
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