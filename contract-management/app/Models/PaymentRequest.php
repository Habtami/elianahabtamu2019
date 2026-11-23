<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    protected $fillable = [
        'payment_request_date',
        'document_no',
        'payment_type',
        'amount',
        'quantity',
        'material_group',
        'procurement_type',
        'remarks',
        'gs_approved_by',
        'gs_approved_at',
        'finance_approved_by',
        'finance_approved_at',
        'created_by',
    ];

    protected $casts = [
        'payment_request_date' => 'date',
        'amount' => 'decimal:2',
        'quantity' => 'decimal:2',
        'gs_approved_at' => 'datetime',
        'finance_approved_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETE = 'complete';

    public function getProcurementTypeLabel()
    {
        return $this->procurement_type ?: '—';
    }

    public function gsApprover()
    {
        return $this->belongsTo(User::class, 'gs_approved_by');
    }

    public function financeApprover()
    {
        return $this->belongsTo(User::class, 'finance_approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getReference()
    {
        return 'PR-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function isGsApproved()
    {
        return $this->gs_approved_at !== null;
    }

    public function isFinanceCompleted()
    {
        return $this->finance_approved_at !== null;
    }

    public function getStatus()
    {
        return $this->isFinanceCompleted() ? self::STATUS_COMPLETE : self::STATUS_PENDING;
    }

    public function getFinanceStatusLabel()
    {
        if ($this->isFinanceCompleted()) {
            return 'Completed';
        }

        if (!$this->isGsApproved()) {
            return 'Locked';
        }

        return 'Pending';
    }

    public function getStatusLabel()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETE => 'Complete',
        ][$this->getStatus()];
    }
}
