<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'field_name',
        'old_value',
        'new_value',
        'edited_by',
        'reason'
    ];

    protected $casts = [
        'old_value' => 'string',
        'new_value' => 'string',
    ];

    // Relationship with contract
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    // Get formatted old value
    public function getFormattedOldValue()
    {
        if ($this->field_name === 'performance_guarantee') {
            $data = json_decode($this->old_value, true);
            if ($data && isset($data['amount']) && isset($data['end_date'])) {
                return 'Amount: ' . number_format($data['amount'], 2) . ', End Date: ' . $data['end_date'];
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
                return 'Amount: ' . number_format($data['amount'], 2) . ', End Date: ' . $data['end_date'];
            }
        }
        return $this->new_value ?? 'N/A';
    }
}