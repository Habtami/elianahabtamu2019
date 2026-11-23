<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TireGive extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tire_id',
        'driver_id',
        'given_by',
        'give_date',
        'given_kilometer',
        'plate_number',
        'photo',
        'notes',
        'is_returned',
        'created_by',
    ];

    protected $casts = [
        'give_date' => 'date',
        'given_kilometer' => 'integer',
        'is_returned' => 'boolean',
    ];

    // Status constants
    const STATUS_GIVEN = 'given';
    const STATUS_RETURNED = 'returned';

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

    // Relationship with user who gave the tire
    public function givenBy()
    {
        return $this->belongsTo(User::class, 'given_by');
    }

    // Relationship with tire returns
    public function tireReturn()
    {
        return $this->hasOne(TireReturn::class);
    }

    // Relationship with creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Check if tire has been returned
    public function isReturned()
    {
        return $this->is_returned;
    }

    // Scope for active (not returned) gives
    public function scopeActive($query)
    {
        return $query->where('is_returned', false);
    }

    // Scope for returned gives
    public function scopeReturned($query)
    {
        return $query->where('is_returned', true);
    }
}