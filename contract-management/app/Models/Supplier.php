<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'tin_number',
        'status',
        'quality_rating',
        'delivery_rating',
        'cost_rating',
        'communication_rating',
        'overall_rating',
        'performance_status',
        'last_evaluation_date',
        'created_by',
        'created_by_name',
    ];

    /**
     * Get the contracts for the supplier.
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Scope a query to only include active suppliers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
