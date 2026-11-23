<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'export_type',
        'format',
        'file_path',
        'records_count',
        'status',
        'filters',
    ];

    protected $casts = [
        'filters' => 'array',
        'created_at' => 'datetime',
    ];

    const EXPORT_TYPE_TIRES = 'tires';
    const EXPORT_TYPE_DRIVERS = 'drivers';
    const EXPORT_TYPE_RETURNS = 'returns';

    const FORMAT_CSV = 'csv';
    const FORMAT_EXCEL = 'excel';
    const FORMAT_PDF = 'pdf';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getExportTypeDisplayName()
    {
        return match($this->export_type) {
            self::EXPORT_TYPE_TIRES => 'Tires',
            self::EXPORT_TYPE_DRIVERS => 'Drivers',
            self::EXPORT_TYPE_RETURNS => 'Returns',
            default => $this->export_type,
        };
    }

    public function getFormatDisplayName()
    {
        return match($this->format) {
            self::FORMAT_CSV => 'CSV',
            self::FORMAT_EXCEL => 'Excel',
            self::FORMAT_PDF => 'PDF',
            default => $this->format,
        };
    }

    public function getFileSizeAttribute()
    {
        if (!$this->file_path) {
            return 0;
        }
        $fullPath = storage_path('app/' . $this->file_path);
        return file_exists($fullPath) ? filesize($fullPath) : 0;
    }

    public function getFileSizeHumanReadableAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}