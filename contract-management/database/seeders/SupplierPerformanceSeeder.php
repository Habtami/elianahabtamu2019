<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierPerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'ABC Supplies Ltd.',
                'contact_person' => 'John Smith',
                'email' => 'john@abcsupplies.com',
                'phone' => '+251911123456',
                'address' => '123 Addis Ababa, Ethiopia',
                'tin_number' => 'TIN001',
                'status' => 'active',
                'quality_rating' => 95.00,
                'delivery_rating' => 98.00,
                'cost_rating' => 4.5,
                'communication_rating' => 'Excellent',
                'overall_rating' => 95.8,
                'performance_status' => 'Top Performer',
                'last_evaluation_date' => now(),
            ],
            [
                'name' => 'XYZ Traders',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@xyztraders.com',
                'phone' => '+251922654321',
                'address' => '456 Bole, Addis Ababa',
                'tin_number' => 'TIN002',
                'status' => 'active',
                'quality_rating' => 88.00,
                'delivery_rating' => 90.00,
                'cost_rating' => 4.0,
                'communication_rating' => 'Good',
                'overall_rating' => 89.3,
                'performance_status' => 'Average',
                'last_evaluation_date' => now(),
            ],
            [
                'name' => 'Global Partners Ltd.',
                'contact_person' => 'Michael Brown',
                'email' => 'michael@globalpartners.com',
                'phone' => '+251933789012',
                'address' => '789 Kazanchis, Addis Ababa',
                'tin_number' => 'TIN003',
                'status' => 'active',
                'quality_rating' => 92.00,
                'delivery_rating' => 95.00,
                'cost_rating' => 4.2,
                'communication_rating' => 'Excellent',
                'overall_rating' => 93.5,
                'performance_status' => 'Top Performer',
                'last_evaluation_date' => now(),
            ],
            [
                'name' => 'Metro Construction Supplies',
                'contact_person' => 'Emma Davis',
                'email' => 'emma@metroconstruction.com',
                'phone' => '+251944567890',
                'address' => '321 Piazza, Addis Ababa',
                'tin_number' => 'TIN004',
                'status' => 'active',
                'quality_rating' => 85.00,
                'delivery_rating' => 87.00,
                'cost_rating' => 3.8,
                'communication_rating' => 'Good',
                'overall_rating' => 86.2,
                'performance_status' => 'Average',
                'last_evaluation_date' => now(),
            ],
            [
                'name' => 'Ethio Building Materials',
                'contact_person' => 'David Wilson',
                'email' => 'david@ethiobuilding.com',
                'phone' => '+251955345678',
                'address' => '654 Mexico, Addis Ababa',
                'tin_number' => 'TIN005',
                'status' => 'active',
                'quality_rating' => 78.00,
                'delivery_rating' => 82.00,
                'cost_rating' => 3.5,
                'communication_rating' => 'Average',
                'overall_rating' => 79.8,
                'performance_status' => 'Needs Improvement',
                'last_evaluation_date' => now(),
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }
    }
}
