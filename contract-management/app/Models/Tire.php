<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tire extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tire_type',
        'serial_number',
        'plate_number',
        'photo',
        'initial_kilometer',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'initial_kilometer' => 'integer',
        'is_active' => 'boolean',
    ];

    // Tire type constants
    const TYPE_MICHELIN = 'Michelin';
    const TYPE_BRIDGESTONE = 'Bridgestone';
    const TYPE_GOODYEAR = 'Goodyear';
    const TYPE_CONTINENTAL = 'Continental';
    const TYPE_PIRELLI = 'Pirelli';
    const TYPE_DUNLOP = 'Dunlop';
    const TYPE_OTHER = 'Other';

    // Get all tire types (dynamic from tire_types table, with hardcoded fallback)
    public static function getTireTypes()
    {
        $types = TireType::where('is_active', true)->orderBy('sort_order')->get();
        if ($types->isEmpty()) {
            return [
                'Michelin' => 'Michelin',
                'Bridgestone' => 'Bridgestone',
                'Goodyear' => 'Goodyear',
                'Continental' => 'Continental',
                'Pirelli' => 'Pirelli',
                'Dunlop' => 'Dunlop',
                'Other' => 'Other',
            ];
        }
        return $types->pluck('name', 'name')->toArray();
    }

    // Get status display name
    public function getStatusDisplayName()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    // Relationship with tire gives
    public function tireGives()
    {
        return $this->hasMany(TireGive::class);
    }

    // Relationship with tire returns
    public function tireReturns()
    {
        return $this->hasMany(TireReturn::class);
    }

    // Relationship with current driver (active give)
    public function currentDriver()
    {
        return $this->belongsTo(Driver::class, 'current_driver_id');
    }

    // Check if tire is currently given out
    public function isGivenOut()
    {
        return $this->tireGives()->where('is_returned', false)->exists();
    }

    // Check if tire has ever been returned
    public function hasBeenReturned()
    {
        return $this->tireGives()->where('is_returned', true)->exists();
    }

    // Get current give record
    public function currentGive()
    {
        return $this->tireGives()->where('is_returned', false)->latest()->first();
    }

    // Scope for active tires
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for inactive tires
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // Scope for tires currently given out
    public function scopeGivenOut($query)
    {
        return $query->whereHas('tireGives', function ($q) {
            $q->where('is_returned', false);
        });
    }

    // Scope for available tires
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->whereDoesntHave('tireGives', function ($q) {
                $q->where('is_returned', false);
            });
    }
}