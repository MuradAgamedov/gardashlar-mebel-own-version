<?php

namespace Database\Seeders;

use App\Models\Phone;
use App\Models\PhoneType;
use App\Models\PhoneTypeTranslate;
use Illuminate\Database\Seeder;

class PhoneAndPhoneTypeSeeder extends Seeder
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

        $createdPhoneTypes = [];

        foreach ($phoneTypes as $phoneTypeData) {
            $translations = $phoneTypeData['translations'];
            unset($phoneTypeData['translations']);

            $phoneType = PhoneType::create($phoneTypeData);
            $createdPhoneTypes[] = $phoneType;

            // Create translations for each language
            foreach ($translations as $translation) {
                PhoneTypeTranslate::create([
                    'phone_type_id' => $phoneType->id,
                    'language_id' => $translation['language_id'],
                    'title' => $translation['name']
                ]);
            }
        }

        // Create phones for each phone type
        $phones = [
            [
                'phone_type_id' => $createdPhoneTypes[0]->id, // Əsas
                'phone' => '+994 12 123 45 67',
                'order' => 1,
                'status' => true
            ],
            [
                'phone_type_id' => $createdPhoneTypes[0]->id, // Əsas
                'phone' => '+994 50 123 45 67',
                'order' => 2,
                'status' => true
            ],
            [
                'phone_type_id' => $createdPhoneTypes[1]->id, // Satış
                'phone' => '+994 55 123 45 67',
                'order' => 1,
                'status' => true
            ],
            [
                'phone_type_id' => $createdPhoneTypes[1]->id, // Satış
                'phone' => '+994 70 123 45 67',
                'order' => 2,
                'status' => true
            ],
            [
                'phone_type_id' => $createdPhoneTypes[2]->id, // Dəstək
                'phone' => '+994 51 123 45 67',
                'order' => 1,
                'status' => true
            ],
            [
                'phone_type_id' => $createdPhoneTypes[2]->id, // Dəstək
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