<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Battery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date_of_manufacture',
        'serial_number',
        'material_code',
        'battery_type',
        'brand',
        'plate_number',
        'driver_name',
        'initial_kilometer',
        'return_kilometer',
        'status',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'date_of_manufacture' => 'date',
        'initial_kilometer' => 'integer',
        'return_kilometer' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function defaultBatteryTypes(): array
    {
        return [
            ['material_code' => '1000000502', 'name' => 'Car Battery 12V 90A'],
            ['material_code' => '1000000501', 'name' => 'Car Battery 12V 150A'],
            ['material_code' => '1000000500', 'name' => 'Car Battery 12V 120A'],
            ['material_code' => '1000000499', 'name' => 'Car Battery 12V 200A'],
            ['material_code' => '1000000498', 'name' => 'Car Battery 12V 170A'],
            ['material_code' => '1000000497', 'name' => 'Car Battery 12V 50A'],
            ['material_code' => '1000000496', 'name' => 'Car Battery 12V 100A'],
            ['material_code' => '1000000495', 'name' => 'Car Battery 12V 70A'],
            ['material_code' => '1000000494', 'name' => 'Car Battery 12V 60A'],
            ['material_code' => null, 'name' => 'Car Battery 12V 45A'],
            ['material_code' => null, 'name' => 'Car Battery 12V 75A'],
        ];
    }

    public static function getBatteryTypes(): array
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('battery_types')) {
                $types = BatteryType::where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();

                if ($types->isNotEmpty()) {
                    return $types->map(function ($type) {
                        return [
                            'material_code' => $type->material_code,
                            'name' => $type->name,
                        ];
                    })->toArray();
                }
            }
        } catch (\Throwable $e) {
            // Fall through to the built-in list
        }

        return self::defaultBatteryTypes();
    }

    public static function getMaterialCodeForType(string $typeName): ?string
    {
        foreach (self::getBatteryTypes() as $type) {
            if ($type['name'] === $typeName) {
                return $type['material_code'];
            }
        }

        return null;
    }

    public static function defaultBrands(): array
    {
        return [
            'Varta' => 'Varta',
            'Bosch' => 'Bosch',
            'Exide' => 'Exide',
            'Optima' => 'Optima',
            'ACDelco' => 'ACDelco',
        ];
    }

    public static function getBrands(): array
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('battery_brands')) {
                $names = BatteryBrand::where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->pluck('name')
                    ->toArray();

                if (!empty($names)) {
                    return array_combine($names, $names);
                }
            }
        } catch (\Throwable $e) {
            // Fall through to the built-in list
        }

        return self::defaultBrands();
    }

    const STATUS_IN_SERVICE = 'in_service';
    const STATUS_RETURNED = 'returned';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_IN_SERVICE => 'In Service',
            self::STATUS_RETURNED => 'Returned',
        ];
    }

    public function getStatusDisplayName(): string
    {
        return self::getStatuses()[$this->status] ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public function isReturned(): bool
    {
        return $this->status === self::STATUS_RETURNED;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function histories()
    {
        return $this->hasMany(BatteryHistory::class);
    }

    public function scopeReturned($query)
    {
        return $query->where('status', self::STATUS_RETURNED);
    }

    public function scopeInService($query)
    {
        return $query->where('status', self::STATUS_IN_SERVICE);
    }
}
