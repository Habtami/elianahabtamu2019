<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class MaterialGroup extends Model
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
            'Stationery',
            'Sanitary',
            'Service',
            'Spare Parts',
            'Electronics',
            'Uniform',
            'Printing',
            'Other Services',
            'Work',
            'Maintenance',
            'Machinery',
            'Furniture',
        ];
    }

    public static function activeNames(): array
    {
        if (! Schema::hasTable('material_groups')) {
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
