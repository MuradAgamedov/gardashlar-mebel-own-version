<?php

namespace Database\Seeders;

use App\Models\PhoneType;
use App\Models\PhoneTypeTranslate;
use Illuminate\Database\Seeder;

class PhoneTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create phone types
        $phoneTypes = [
            [
                'order' => 1,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 3, // Azərbaycan
                        'name' => 'Əsas'
                    ],
                    [
                        'language_id' => 1, // English
                        'name' => 'Main'
                    ]
                ]
            ],
            [
                'order' => 2,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 3, // Azərbaycan
                        'name' => 'Satış'
                    ],
                    [
                        'language_id' => 1, // English
                        'name' => 'Sales'
                    ]
                ]
            ],
            [
                'order' => 3,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 3, // Azərbaycan
                        'name' => 'Dəstək'
                    ],
                    [
                        'language_id' => 1, // English
                        'name' => 'Support'
                    ]
                ]
            ]
        ];

        foreach ($phoneTypes as $phoneTypeData) {
            $translations = $phoneTypeData['translations'];
            unset($phoneTypeData['translations']);

            $phoneType = PhoneType::create($phoneTypeData);

            // Create translations for each language
            foreach ($translations as $translation) {
                PhoneTypeTranslate::create([
                    'phone_type_id' => $phoneType->id,
                    'language_id' => $translation['language_id'],
                    'title' => $translation['name']
                ]);
            }
        }
    }
} 