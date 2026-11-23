<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TireType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'category',
        'width',
        'aspect_ratio',
        'rim_diameter',
        'load_index',
        'season',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'width' => 'decimal:1',
        'aspect_ratio' => 'decimal:1',
        'rim_diameter' => 'decimal:1',
        'load_index' => 'integer',
    ];

    public function tires()
    {
        return $this->hasMany(Tire::class, 'tire_type', 'name');
    }

    public static function getActiveTypes()
    {
        return cache()->remember('tire_types_active', 3600, function () {
            return self::where('is_active', true)->orderBy('sort_order')->get();
        });
    }

    public static function getList()
    {
        return cache()->remember('tire_types_list', 3600, function () {
            return self::where('is_active', true)
                ->orderBy('sort_order')
                ->pluck('name', 'name')
                ->toArray();
        });
    }

    public function clearCache()
    {
        cache()->forget('tire_types_active');
        cache()->forget('tire_types_list');
    }

    protected static function booted()
    {
        static::created(function ($model) {
            $model->clearCache();
        });
        static::updated(function ($model) {
            $model->clearCache();
        });
        static::deleted(function ($model) {
            $model->clearCache();
        });
    }

    public function formattedSpec(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->width ? "{$this->width}/{$this->aspect_ratio}R{$this->rim_diameter}" : null,
        );
    }
}