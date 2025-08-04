<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceStaticData;
use App\Models\ServiceStaticDataTranslate;
use App\Models\Language;

class ServiceStaticDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get languages
        $azLanguage = Language::where('lang_code', 'az')->first();
        $enLanguage = Language::where('lang_code', 'en')->first();

        if (!$azLanguage || !$enLanguage) {
            $this->command->error('Languages not found. Please run LanguageSeeder first.');
            return;
        }

        // Create or get the first service static data record
        $serviceStaticData = ServiceStaticData::getFirstOrCreate();

        // Update with sample images
        $serviceStaticData->update([
            'card_1_image' => 'service-static/card-1.jpg',
            'card_2_image' => 'service-static/card-2.jpg',
            'card_3_image' => 'service-static/card-3.jpg',
        ]);

        // Sample data for translations
        $translations = [
            'az' => [
                'title' => 'Xidmətlərimiz',
                'description' => 'Peşəkar mebel xidmətlərimizlə evinizi gözəlləşdirin',
                'card_1_image_title' => 'Mebel İstehsalı',
                'card_1_image_alt_text' => 'Yüksək keyfiyyətli mebel istehsalı',
                'card_2_image_title' => 'Mebel Satışı',
                'card_2_image_alt_text' => 'Geniş mebel çeşidi',
                'card_3_image_title' => 'Mebel Dizaynı',
                'card_3_image_alt_text' => 'Fərdi mebel dizaynı',
            ],
            'en' => [
                'title' => 'Our Services',
                'description' => 'Transform your home with our professional furniture services',
                'card_1_image_title' => 'Furniture Manufacturing',
                'card_1_image_alt_text' => 'High quality furniture manufacturing',
                'card_2_image_title' => 'Furniture Sales',
                'card_2_image_alt_text' => 'Wide range of furniture',
                'card_3_image_title' => 'Furniture Design',
                'card_3_image_alt_text' => 'Custom furniture design',
            ]
        ];

        // Create translations
        foreach ($translations as $langCode => $translation) {
            $language = Language::where('lang_code', $langCode)->first();
            
            if ($language) {
                // Delete existing translation if exists
                ServiceStaticDataTranslate::where('service_static_data_id', $serviceStaticData->id)
                    ->where('language_id', $language->id)
                    ->delete();

                // Create new translation
                ServiceStaticDataTranslate::create([
                    'service_static_data_id' => $serviceStaticData->id,
                    'language_id' => $language->id,
                    'title' => $translation['title'],
                    'description' => $translation['description'],
                    'card_1_image_title' => $translation['card_1_image_title'],
                    'card_1_image_alt_text' => $translation['card_1_image_alt_text'],
                    'card_2_image_title' => $translation['card_2_image_title'],
                    'card_2_image_alt_text' => $translation['card_2_image_alt_text'],
                    'card_3_image_title' => $translation['card_3_image_title'],
                    'card_3_image_alt_text' => $translation['card_3_image_alt_text'],
                ]);
            }
        }

        $this->command->info('Service Static Data seeded successfully!');
        $this->command->info('Note: Please add the actual images to the storage/app/public/service-static/ directory.');
    }
} 