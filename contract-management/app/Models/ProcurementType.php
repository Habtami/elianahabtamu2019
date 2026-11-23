<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ProcurementType extends Model
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

    public static function defaultNames(): array
    {
        return [
            'Purchase',
            'Tender',
            'Direct',
        ];
    }

    public static function activeNames(): array
    {
        if (! Schema::hasTable('procurement_types')) {
            return self::defaultNames();
        }

        $names = static::query()
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->pluck('name')
            ->all();

        return $names ?: self::defaultNames();
    }
}
