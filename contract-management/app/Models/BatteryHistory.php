<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatteryHistory extends Model
{
    protected $fillable = [
        'battery_id',
        'action',
        'status',
        'driver_name',
        'initial_kilometer',
        'return_kilometer',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'initial_kilometer' => 'integer',
        'return_kilometer' => 'integer',
    ];

    public function battery()
    {
        return $this->belongsTo(Battery::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getActionLabelAttribute(): string
    {
        $labels = [
            'registered' => 'Given',
            'updated' => 'Updated',
            'status_changed' => 'Status Changed',
            'returned' => 'Returned',
        ];

        return $labels[$this->action] ?? ucfirst(str_replace('_', ' ', (string) $this->action));
    }

    public function getStatusLabelAttribute(): ?string
    {
        if (!$this->status) {
            return null;
        }

        return Battery::getStatuses()[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }
}
