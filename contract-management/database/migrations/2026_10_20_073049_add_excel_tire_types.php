<?php

use App\Models\TireType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tireTypes = [
            ['name' => 'Tyre Forklift 650/10 12PR Innertube&Flap', 'code' => 'FLT650', 'category' => 'Forklift', 'is_active' => true, 'sort_order' => 100],
            ['name' => 'TYRE 4.10 -18', 'code' => 'TYR410', 'category' => 'Industrial', 'is_active' => true, 'sort_order' => 101],
            ['name' => 'Tyre 12x20 Pr22', 'code' => 'TYR1220', 'category' => 'Industrial', 'is_active' => true, 'sort_order' => 102],
            ['name' => 'Tyre 7.00/15 12PR With Inner Tube', 'code' => 'TYR715', 'category' => 'Truck', 'is_active' => true, 'sort_order' => 103],
            ['name' => 'Tyre 175*70*14', 'code' => 'TYR175', 'category' => 'Car', 'is_active' => true, 'sort_order' => 104],
            ['name' => 'Tyre 245/70 16Pr', 'code' => 'TYR24570', 'category' => 'Truck', 'is_active' => true, 'sort_order' => 105],
            ['name' => 'Tyre 245/75 17Pr', 'code' => 'TYR24575', 'category' => 'Truck', 'is_active' => true, 'sort_order' => 106],
            ['name' => 'Tyre 215/80 15Pr', 'code' => 'TYR21580', 'category' => 'Truck', 'is_active' => true, 'sort_order' => 107],
            ['name' => 'Tyre 600/9 12Pr', 'code' => 'TYR6009', 'category' => 'Industrial', 'is_active' => true, 'sort_order' => 108],
            ['name' => 'Tyre 6.50/16 10Pr', 'code' => 'TYR65016', 'category' => 'Truck', 'is_active' => true, 'sort_order' => 109],
            ['name' => 'Tyre 7.00/12 12Pr', 'code' => 'TYR70012', 'category' => 'Truck', 'is_active' => true, 'sort_order' => 110],
            ['name' => 'Tyre 7.50/16 10Pr', 'code' => 'TYR75016', 'category' => 'Truck', 'is_active' => true, 'sort_order' => 111],
            ['name' => 'Tyre 7.00/14 8 Pr', 'code' => 'TYR7148', 'category' => 'Truck', 'is_active' => true, 'sort_order' => 112],
            ['name' => 'Tyre 560/13 14Pr', 'code' => 'TYR56013', 'category' => 'Industrial', 'is_active' => true, 'sort_order' => 113],
            ['name' => 'Tyre 2.75/21 4PR', 'code' => 'TYR27521', 'category' => 'Industrial', 'is_active' => true, 'sort_order' => 114],
            ['name' => 'Tyre 7.00x16 12 Pr', 'code' => 'TYR71612', 'category' => 'Truck', 'is_active' => true, 'sort_order' => 115],
            ['name' => 'Tyre 420/70 24 PR', 'code' => 'TYR42070', 'category' => 'Industrial', 'is_active' => true, 'sort_order' => 116],
            ['name' => 'Tyre 750/16 8Pr', 'code' => 'TYR75016-8', 'category' => 'Industrial', 'is_active' => true, 'sort_order' => 117],
            ['name' => 'Tyre 8.15x15 14PR', 'code' => 'TYR81515', 'category' => 'Truck', 'is_active' => true, 'sort_order' => 118],
            ['name' => 'Tyre 265/65 R17 Trayngle', 'code' => 'TYR26565R17T', 'category' => 'SUV', 'is_active' => true, 'sort_order' => 119],
            ['name' => 'Tyre 12/20 20Pr', 'code' => 'TYR1220-20', 'category' => 'Industrial', 'is_active' => true, 'sort_order' => 120],
            ['name' => 'Tyre 750/16 16Pr', 'code' => 'TYR75016-16', 'category' => 'Industrial', 'is_active' => true, 'sort_order' => 121],
            ['name' => 'Tyre 8.25/16 16Pr', 'code' => 'TYR82516', 'category' => 'Industrial', 'is_active' => true, 'sort_order' => 122],
            ['name' => 'Tyre 7.00/12 14Pr', 'code' => 'TYR70012-14', 'category' => 'Truck', 'is_active' => true, 'sort_order' => 123],
            ['name' => 'Tyre 11/20 18Pr', 'code' => 'TYR1120', 'category' => 'Industrial', 'is_active' => true, 'sort_order' => 124],
            ['name' => 'Tyre 265/65 R17', 'code' => 'TYR26565R17', 'category' => 'SUV', 'is_active' => true, 'sort_order' => 125],
            ['name' => 'Tyre 7.50/16 14Pr', 'code' => 'TYR75016-14', 'category' => 'Truck', 'is_active' => true, 'sort_order' => 126],
            ['name' => 'Tyre 10/20 16Pr', 'code' => 'TYR1020', 'category' => 'Industrial', 'is_active' => true, 'sort_order' => 127],
            ['name' => 'Tyre 6.50/14 8Pr', 'code' => 'TYR65014', 'category' => 'Truck', 'is_active' => true, 'sort_order' => 128],
            ['name' => 'Tyre 9/20 16Pr', 'code' => 'TYR920', 'category' => 'Industrial', 'is_active' => true, 'sort_order' => 129],
            ['name' => 'Tyre 265/70 R19.5 18Pr', 'code' => 'TYR26570R195', 'category' => 'SUV', 'is_active' => true, 'sort_order' => 130],
        ];

        foreach ($tireTypes as $type) {
            // Only insert if not already exists
            if (!TireType::where('name', $type['name'])->exists()) {
                TireType::create($type);
            }
        }
    }

    public function down(): void
    {
        $names = [
            'Tyre Forklift 650/10 12PR Innertube&Flap',
            'TYRE 4.10 -18',
            'Tyre 12x20 Pr22',
            'Tyre 7.00/15 12PR With Inner Tube',
            'Tyre 175*70*14',
            'Tyre 245/70 16Pr',
            'Tyre 245/75 17Pr',
            'Tyre 215/80 15Pr',
            'Tyre 600/9 12Pr',
            'Tyre 6.50/16 10Pr',
            'Tyre 7.00/12 12Pr',
            'Tyre 7.50/16 10Pr',
            'Tyre 7.00/14 8 Pr',
            'Tyre 560/13 14Pr',
            'Tyre 2.75/21 4PR',
            'Tyre 7.00x16 12 Pr',
            'Tyre 420/70 24 PR',
            'Tyre 750/16 8Pr',
            'Tyre 8.15x15 14PR',
            'Tyre 265/65 R17 Trayngle',
            'Tyre 12/20 20Pr',
            'Tyre 750/16 16Pr',
            'Tyre 8.25/16 16Pr',
            'Tyre 7.00/12 14Pr',
            'Tyre 11/20 18Pr',
            'Tyre 265/65 R17',
            'Tyre 7.50/16 14Pr',
            'Tyre 10/20 16Pr',
            'Tyre 6.50/14 8Pr',
            'Tyre 9/20 16Pr',
            'Tyre 265/70 R19.5 18Pr',
        ];
        
        TireType::whereIn('name', $names)->delete();
    }
};