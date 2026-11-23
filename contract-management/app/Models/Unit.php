<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Unit extends Model
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
            'Pcs',
            'Kg',
            'Liter',
            'Box',
            'Pack',
            'Roll',
            'Set',
            'Pair',
            'Meter',
            'Square Meter',
            'Carton',
            'Dozen',
            'Bottle',
            'Bag',
            'Ream',
            'Tube',
            'Can',
            'Bundle',
            'Drum',
        ];
    }

    public static function activeNames(): array
    {
        if (! Schema::hasTable('units')) {
            return self::defaultNames();
        }

        $names = static::query()
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->pluck('name')
            ->all();

        return $names ?: self::defaultNames();
    }
}
