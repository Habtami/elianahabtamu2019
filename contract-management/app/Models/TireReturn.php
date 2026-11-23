<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TireReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'tire_give_id',
        'tire_id',
        'driver_id',
        'returned_by',
        'return_date',
        'current_kilometer',
        'registered_kilometer',
        'km_difference',
        'tire_type',
        'plate_number',
        'serial_number',
        'photo',
        'notes',
    ];

    protected $casts = [
        'return_date' => 'date',
        'current_kilometer' => 'integer',
        'registered_kilometer' => 'integer',
        'km_difference' => 'integer',
    ];

    // Relationship with tire give
    public function tireGive()
    {
        return $this->belongsTo(TireGive::class);
    }

    // Relationship with tire
    public function tire()
    {
        return $this->belongsTo(Tire::class);
    }

    // Relationship with driver
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    // Relationship with user who returned the tire
    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    // Relationship with creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    // Get status display name
    public function getStatusDisplayName()
    {
        return 'Returned';
    }

    // Calculate km difference
    public static function calculateKmDifference($currentKm, $registeredKm)
    {
        return $currentKm - $registeredKm;
    }

    // Scope for returns in date range
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('return_date', [$startDate, $endDate]);
    }
}