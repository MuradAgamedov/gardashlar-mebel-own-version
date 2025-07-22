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
        // English - Main Language
        Language::create([
            'title' => 'English',
            'lang_code' => 'en',
            'lang_name' => 'English',
            'order' => 1,
            'status' => true,
            'is_main_language' => true
        ]);
    }
}
