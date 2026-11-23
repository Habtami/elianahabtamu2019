<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasingActivity extends Model
{
    protected $fillable = [
        'request_date',
        'document_type',
        'material_group',
        'unit',
        'procurement_type',
        'remarks',
        'status',
        'completed_by',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'request_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETE = 'complete';

    public const DOCUMENT_PROFORMA = 'proforma';
    public const DOCUMENT_RFQ = 'rfq';

    public const PROCUREMENT_PURCHASE = 'purchase';
    public const PROCUREMENT_TENDER = 'tender';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETE => 'Complete',
        ];
    }

    public static function getDocumentTypes()
    {
        return [
            self::DOCUMENT_PROFORMA => 'Proforma',
            self::DOCUMENT_RFQ => 'Request for Quotation',
        ];
    }

    public static function getProcurementTypes()
    {
        return [
            self::PROCUREMENT_PURCHASE => 'Purchase',
            self::PROCUREMENT_TENDER => 'Tender',
        ];
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

    public function getDocumentTypeLabel()
    {
        return self::getDocumentTypes()[$this->document_type] ?? $this->document_type;
    }

    public function getProcurementTypeLabel()
    {
        return self::getProcurementTypes()[$this->procurement_type] ?? $this->procurement_type;
    }
}
