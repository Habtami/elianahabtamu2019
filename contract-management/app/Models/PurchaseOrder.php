<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number',
        'amount',
        'approval_date',
        'remarks',
        'gs_manager_approved_by',
        'gs_manager_approved_at',
        'ddg_approved_by',
        'ddg_approved_at',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approval_date' => 'date',
        'gs_manager_approved_at' => 'datetime',
        'ddg_approved_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_FIRST_APPROVED = 'first_approved';
    public const STATUS_APPROVED = 'approved';

    // Amounts above this need the DDG second approval; at or below it, GS Manager approval completes the flow.
    public const SECOND_APPROVAL_THRESHOLD = 400000;

    public function gsManagerApprover()
    {
        return $this->belongsTo(User::class, 'gs_manager_approved_by');
    }

    public function ddgApprover()
    {
        return $this->belongsTo(User::class, 'ddg_approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isGsApproved()
    {
        return $this->gs_manager_approved_at !== null;
    }

    public function isDdgApproved()
    {
        return $this->ddg_approved_at !== null;
    }

    public function requiresDdgApproval()
    {
        return (float) $this->amount > self::SECOND_APPROVAL_THRESHOLD;
    }

    public function isFullyApproved()
    {
        return $this->requiresDdgApproval()
            ? $this->isDdgApproved()
            : $this->isGsApproved();
    }

    public function getStatus()
    {
        if ($this->requiresDdgApproval()) {
            if ($this->isDdgApproved()) {
                return self::STATUS_APPROVED;
            }

            if ($this->isGsApproved()) {
                return self::STATUS_FIRST_APPROVED;
            }

            return self::STATUS_PENDING;
        }

        return $this->isGsApproved() ? self::STATUS_APPROVED : self::STATUS_PENDING;
    }

    public function getSecondApprovalStatusLabel()
    {
        if ($this->isDdgApproved()) {
            return 'Approved';
        }

        if (!$this->requiresDdgApproval()) {
            return 'Not Required';
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
            self::STATUS_FIRST_APPROVED => 'First Approved',
            self::STATUS_APPROVED => 'Approved',
        ][$this->getStatus()];
    }
}
