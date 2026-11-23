<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Department extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function activeNames(): array
    {
        if (! Schema::hasTable('departments')) {
            return [];
        }

        return static::query()
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->pluck('name')
            ->all();
    }
}
