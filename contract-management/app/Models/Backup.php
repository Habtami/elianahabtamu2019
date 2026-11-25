<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Backup extends Model
{
    protected $table = 'backups';

    protected $fillable = [
        'filename',
        'file_path',
        'size_bytes',
        'content_hash',
        'was_skipped',
        'skip_reason',
        'backup_type',
        'created_by',
        'destination_path',
        'copy_status',
        'email_status',
        'email_recipients',
    ];

    protected $casts = [
        'was_skipped' => 'boolean',
        'size_bytes' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
