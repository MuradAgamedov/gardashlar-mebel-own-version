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
                        'language_id' => 1, // Azərbaycan
                        'name' => 'Əsas'
                    ],
                    [
                        'language_id' => 2, // English
                        'name' => 'Main'
                    ],
                    [
                        'language_id' => 3, // Русский
                        'name' => 'Основной'
                    ]
                ]
            ],
            [
                'order' => 2,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 1, // Azərbaycan
                        'name' => 'Satış'
                    ],
                    [
                        'language_id' => 2, // English
                        'name' => 'Sales'
                    ],
                    [
                        'language_id' => 3, // Русский
                        'name' => 'Продажи'
                    ]
                ]
            ],
            [
                'order' => 3,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 1, // Azərbaycan
                        'name' => 'Dəstək'
                    ],
                    [
                        'language_id' => 2, // English
                        'name' => 'Support'
                    ],
                    [
                        'language_id' => 3, // Русский
                        'name' => 'Поддержка'
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
                    'name' => $translation['name']
                ]);
            }
        }
    }
} 