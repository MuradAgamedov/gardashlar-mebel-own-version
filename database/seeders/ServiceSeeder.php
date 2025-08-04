<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceTranslate;
use App\Models\Language;

class ServiceSeeder extends Seeder
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

        $services = [
            [
                'order' => 1,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Mebel İstehsalı',
                        'card_description' => 'Yüksək keyfiyyətli mebel istehsalı',
                        'main_description' => 'Qardaşlar Mebel QSC olaraq, ən yüksək keyfiyyət standartlarında mebel istehsalı təklif edirik. Müasir texnologiya və təcrübəli ustalarımızla müştərilərimizin gözləntilərini həmişə aşırıq.',
                        'seo_title' => 'Mebel İstehsalı - Qardaşlar Mebel QSC',
                        'seo_keywords' => 'mebel istehsalı, yüksək keyfiyyət, qardaşlar mebel',
                        'seo_description' => 'Yüksək keyfiyyətli mebel istehsalı xidməti. Qardaşlar Mebel QSC ilə keyfiyyətli və dayanıqlı mebellər.'
                    ],
                    
                    'en' => [
                        'title' => 'Furniture Manufacturing',
                        'card_description' => 'High-quality furniture manufacturing',
                        'main_description' => 'As Qardaşlar Mebel QSC, we offer furniture manufacturing at the highest quality standards. With modern technology and experienced craftsmen, we always exceed our customers\' expectations.',
                        'seo_title' => 'Furniture Manufacturing - Qardaşlar Mebel QSC',
                        'seo_keywords' => 'furniture manufacturing, high quality, qardaşlar mebel',
                        'seo_description' => 'High-quality furniture manufacturing services. Quality and durable furniture with Qardaşlar Mebel QSC.'
                    ]
                ]
            ],
            [
                'order' => 2,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Mebel Satışı',
                        'card_description' => 'Geniş mebel çeşidi və satış xidməti',
                        'main_description' => 'Müştərilərimizə ən geniş mebel çeşidini təqdim edirik. Ev, ofis və ticarət obyektləri üçün bütün növ mebellər. Peşəkar satış komandamız sizə ən uyğun seçimi etməkdə kömək edəcək.',
                        'seo_title' => 'Mebel Satışı - Qardaşlar Mebel QSC',
                        'seo_keywords' => 'mebel satışı, geniş çeşid, ev mebelleri, ofis mebelleri',
                        'seo_description' => 'Geniş mebel çeşidi və peşəkar satış xidməti. Ev və ofis üçün keyfiyyətli mebellər.'
                    ],
                    
                    'en' => [
                        'title' => 'Furniture Sales',
                        'card_description' => 'Wide furniture range and sales service',
                        'main_description' => 'We offer our customers the widest range of furniture. All types of furniture for home, office and commercial premises. Our professional sales team will help you make the best choice.',
                        'seo_title' => 'Furniture Sales - Qardaşlar Mebel QSC',
                        'seo_keywords' => 'furniture sales, wide range, home furniture, office furniture',
                        'seo_description' => 'Wide furniture range and professional sales services. Quality furniture for home and office.'
                    ]
                ]
            ],
            [
                'order' => 3,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Mebel Dizaynı',
                        'card_description' => 'Peşəkar mebel dizayn xidməti',
                        'main_description' => 'Müasir və funksional mebel dizaynı xidməti təklif edirik. Dizayn komandamız sizin fərdi ehtiyaclarınıza uyğun həllər yaradır. Həm funksionallıq, həm də estetik gözəllik.',
                        'seo_title' => 'Mebel Dizaynı - Qardaşlar Mebel QSC',
                        'seo_keywords' => 'mebel dizaynı, peşəkar dizayn, fərdi həllər',
                        'seo_description' => 'Peşəkar mebel dizayn xidməti. Fərdi ehtiyaclara uyğun funksional və gözəl həllər.'
                    ],
                    
                    'en' => [
                        'title' => 'Furniture Design',
                        'card_description' => 'Professional furniture design service',
                        'main_description' => 'We offer modern and functional furniture design services. Our design team creates solutions tailored to your individual needs. Both functionality and aesthetic beauty.',
                        'seo_title' => 'Furniture Design - Qardaşlar Mebel QSC',
                        'seo_keywords' => 'furniture design, professional design, custom solutions',
                        'seo_description' => 'Professional furniture design services. Functional and beautiful solutions for individual needs.'
                    ]
                ]
            ]
        ];

        foreach ($services as $serviceData) {
            // Create service
            $service = Service::create([
                'order' => $serviceData['order'],
                'status' => $serviceData['status']
            ]);

            // Create translations
            foreach ($serviceData['translations'] as $langCode => $translation) {
                $language = Language::where('lang_code', $langCode)->first();
                
                if ($language) {
                    ServiceTranslate::create([
                        'service_id' => $service->id,
                        'language_id' => $language->id,
                        'title' => $translation['title'],
                        'card_description' => $translation['card_description'],
                        'main_description' => $translation['main_description'],
                        'seo_title' => $translation['seo_title'],
                        'seo_keywords' => $translation['seo_keywords'],
                        'seo_description' => $translation['seo_description']
                    ]);
                }
            }
        }

        $this->command->info('Services seeded successfully!');
        $this->command->info('Created ' . count($services) . ' services with translations.');
    }
} 