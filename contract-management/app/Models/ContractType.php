<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ContractType extends Model
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
        return ['ICB', 'NCB'];
    }

    public static function activeNames(): array
    {
        $names = self::defaultNames();
        $known = array_map('mb_strtolower', $names);

        if (! Schema::hasTable('contract_types')) {
            return $names;
        }

        $registered = static::query()
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->pluck('name')
            ->all();

        foreach ($registered as $name) {
            if (! in_array(mb_strtolower($name), $known, true)) {
                $names[] = $name;
                $known[] = mb_strtolower($name);
            }
        }

        return $names;
    }
}
