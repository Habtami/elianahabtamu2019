<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'field_name',
        'old_value',
        'new_value',
        'edited_by',
        'reason',
    ];

    /**
     * Get the contract that owns the history.
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Get formatted old value for performance guarantee
     */
    public function getFormattedOldValue()
    {
        if ($this->field_name === 'performance_guarantee') {
            $data = json_decode($this->old_value, true);
            return $data ? "Amount: " . number_format($data['amount'], 2) . " | End Date: " . ($data['end_date'] ?? 'N/A') : 'N/A';
        }
        return $this->old_value ?? 'N/A';
    }

    /**
     * Get formatted new value for performance guarantee
     */
    public function getFormattedNewValue()
    {
        if ($this->field_name === 'performance_guarantee') {
            $data = json_decode($this->new_value, true);
            return $data ? "Amount: " . number_format($data['amount'], 2) . " | End Date: " . ($data['end_date'] ?? 'N/A') : 'N/A';
        }
        return $this->new_value ?? 'N/A';
    }
}
