<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractClosure extends Model
{
    protected $fillable = [
        'contract_id',
        'closing_date',
        'status',
        'remarks',
        'completed_by',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'closing_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETE = 'complete';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETE => 'Complete',
        ];
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isComplete()
    {
        return $this->status === self::STATUS_COMPLETE;
    }

    public function getStatusLabel()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }
}
