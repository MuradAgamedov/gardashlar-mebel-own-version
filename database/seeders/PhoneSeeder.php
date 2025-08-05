<?php

namespace Database\Seeders;

use App\Models\Phone;
use Illuminate\Database\Seeder;

class PhoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create phones
        $phones = [
            [
                'phone_type_id' => 1, // Əsas
                'phone' => '+994 12 123 45 67',
                'order' => 1,
                'status' => true
            ],
            [
                'phone_type_id' => 1, // Əsas
                'phone' => '+994 50 123 45 67',
                'order' => 2,
                'status' => true
            ],
            [
                'phone_type_id' => 2, // Satış
                'phone' => '+994 55 123 45 67',
                'order' => 1,
                'status' => true
            ],
            [
                'phone_type_id' => 2, // Satış
                'phone' => '+994 70 123 45 67',
                'order' => 2,
                'status' => true
            ],
            [
                'phone_type_id' => 3, // Dəstək
                'phone' => '+994 51 123 45 67',
                'order' => 1,
                'status' => true
            ],
            [
                'phone_type_id' => 3, // Dəstək
                'phone' => '+994 60 123 45 67',
                'order' => 2,
                'status' => true
            ]
        ];

        foreach ($phones as $phoneData) {
            Phone::create($phoneData);
        }
    }
} 