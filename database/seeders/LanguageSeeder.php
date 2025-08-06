<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing languages
        Language::truncate();

        // Create languages with correct IDs and codes
        $languages = [
            [
                'id' => 1,
                'title' => 'Azərbaycan dili',
                'lang_code' => 'az',
                'lang_name' => 'Azerbaijani',
                'order' => 1,
                'status' => true,
                'is_main_language' => true,
            ],
            [
                'id' => 2,
                'title' => 'English',
                'lang_code' => 'en',
                'lang_name' => 'English',
                'order' => 2,
                'status' => true,
                'is_main_language' => false,
            ],
            [
                'id' => 3,
                'title' => 'Русский',
                'lang_code' => 'ru',
                'lang_name' => 'Russian',
                'order' => 3,
                'status' => true,
                'is_main_language' => false,
            ],
        ];

        foreach ($languages as $language) {
            Language::create($language);
        }

        $this->command->info('Languages seeded successfully!');
    }
} 