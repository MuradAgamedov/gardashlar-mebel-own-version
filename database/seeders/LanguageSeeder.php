<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Azerbaijani - Main Language
        Language::create([
            'title' => 'Azerbaijani',
            'lang_code' => 'az',
            'lang_name' => 'Azərbaycan dili',
            'order' => 1,
            'status' => true,
            'is_main_language' => true
        ]);

        // English
        Language::create([
            'title' => 'English',
            'lang_code' => 'en',
            'lang_name' => 'English',
            'order' => 2,
            'status' => true,
            'is_main_language' => false
        ]);
    }
}
