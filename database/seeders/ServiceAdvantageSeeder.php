<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceAdvantage;
use App\Models\ServiceAdvantageTranslate;
use App\Models\Language;

class ServiceAdvantageSeeder extends Seeder
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

        // Get services
        $services = Service::all();
        
        if ($services->isEmpty()) {
            $this->command->error('Services not found. Please run ServiceSeeder first.');
            return;
        }

        $advantages = [
            // Mebel İstehsalı (Furniture Manufacturing) advantages
            [
                'service_title' => 'Mebel İstehsalı',
                'advantages' => [
                    [
                        'order' => 1,
                        'status' => true,
                        'icon' => 'service-advantages/manufacturing-quality.png',
                        'translations' => [
                            'az' => [
                                'title' => 'Yüksək Keyfiyyət',
                                'icon_alt_text' => 'Yüksək keyfiyyət ikonu',
                                'description' => 'Ən yüksək keyfiyyət standartlarında mebel istehsalı'
                            ],
                            'en' => [
                                'title' => 'High Quality',
                                'icon_alt_text' => 'High quality icon',
                                'description' => 'Furniture manufacturing at the highest quality standards'
                            ]
                        ]
                    ],
                    [
                        'order' => 2,
                        'status' => true,
                        'icon' => 'service-advantages/manufacturing-technology.png',
                        'translations' => [
                            'az' => [
                                'title' => 'Müasir Texnologiya',
                                'icon_alt_text' => 'Müasir texnologiya ikonu',
                                'description' => 'Ən son texnologiya ilə təchiz edilmiş istehsal'
                            ],
                            'en' => [
                                'title' => 'Modern Technology',
                                'icon_alt_text' => 'Modern technology icon',
                                'description' => 'Production equipped with the latest technology'
                            ]
                        ]
                    ],
                    [
                        'order' => 3,
                        'status' => true,
                        'icon' => 'service-advantages/manufacturing-experience.png',
                        'translations' => [
                            'az' => [
                                'title' => 'Təcrübəli Ustalar',
                                'icon_alt_text' => 'Təcrübəli ustalar ikonu',
                                'description' => 'Uzun illik təcrübəyə malik peşəkar ustalar'
                            ],
                            'en' => [
                                'title' => 'Experienced Craftsmen',
                                'icon_alt_text' => 'Experienced craftsmen icon',
                                'description' => 'Professional craftsmen with years of experience'
                            ]
                        ]
                    ]
                ]
            ],
            // Mebel Satışı (Furniture Sales) advantages
            [
                'service_title' => 'Mebel Satışı',
                'advantages' => [
                    [
                        'order' => 1,
                        'status' => true,
                        'icon' => 'service-advantages/sales-variety.png',
                        'translations' => [
                            'az' => [
                                'title' => 'Geniş Çeşid',
                                'icon_alt_text' => 'Geniş çeşid ikonu',
                                'description' => 'Hər zövqə uyğun geniş mebel çeşidi'
                            ],
                            'en' => [
                                'title' => 'Wide Range',
                                'icon_alt_text' => 'Wide range icon',
                                'description' => 'Wide range of furniture for every taste'
                            ]
                        ]
                    ],
                    [
                        'order' => 2,
                        'status' => true,
                        'icon' => 'service-advantages/sales-service.png',
                        'translations' => [
                            'az' => [
                                'title' => 'Peşəkar Xidmət',
                                'icon_alt_text' => 'Peşəkar xidmət ikonu',
                                'description' => 'Müştəri məmnuniyyəti üçün peşəkar xidmət'
                            ],
                            'en' => [
                                'title' => 'Professional Service',
                                'icon_alt_text' => 'Professional service icon',
                                'description' => 'Professional service for customer satisfaction'
                            ]
                        ]
                    ],
                    [
                        'order' => 3,
                        'status' => true,
                        'icon' => 'service-advantages/sales-delivery.png',
                        'translations' => [
                            'az' => [
                                'title' => 'Çatdırılma',
                                'icon_alt_text' => 'Çatdırılma ikonu',
                                'description' => 'Sürətli və təhlükəsiz çatdırılma xidməti'
                            ],
                            'en' => [
                                'title' => 'Delivery',
                                'icon_alt_text' => 'Delivery icon',
                                'description' => 'Fast and safe delivery service'
                            ]
                        ]
                    ]
                ]
            ],
            // Mebel Dizaynı (Furniture Design) advantages
            [
                'service_title' => 'Mebel Dizaynı',
                'advantages' => [
                    [
                        'order' => 1,
                        'status' => true,
                        'icon' => 'service-advantages/design-custom.png',
                        'translations' => [
                            'az' => [
                                'title' => 'Fərdi Dizayn',
                                'icon_alt_text' => 'Fərdi dizayn ikonu',
                                'description' => 'Sizin ehtiyaclarınıza uyğun fərdi dizayn'
                            ],
                            'en' => [
                                'title' => 'Custom Design',
                                'icon_alt_text' => 'Custom design icon',
                                'description' => 'Custom design tailored to your needs'
                            ]
                        ]
                    ],
                    [
                        'order' => 2,
                        'status' => true,
                        'icon' => 'service-advantages/design-functional.png',
                        'translations' => [
                            'az' => [
                                'title' => 'Funksional Həllər',
                                'icon_alt_text' => 'Funksional həllər ikonu',
                                'description' => 'Həm funksional, həm də estetik həllər'
                            ],
                            'en' => [
                                'title' => 'Functional Solutions',
                                'icon_alt_text' => 'Functional solutions icon',
                                'description' => 'Both functional and aesthetic solutions'
                            ]
                        ]
                    ],
                    [
                        'order' => 3,
                        'status' => true,
                        'icon' => 'service-advantages/design-consultation.png',
                        'translations' => [
                            'az' => [
                                'title' => 'Pulsuz Məsləhət',
                                'icon_alt_text' => 'Pulsuz məsləhət ikonu',
                                'description' => 'Peşəkar dizayn məsləhəti pulsuz'
                            ],
                            'en' => [
                                'title' => 'Free Consultation',
                                'icon_alt_text' => 'Free consultation icon',
                                'description' => 'Free professional design consultation'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        foreach ($advantages as $serviceAdvantageData) {
            // Find the service by title
            $service = $services->first(function($service) use ($serviceAdvantageData) {
                $translation = $service->translate($azLanguage->id);
                return $translation && $translation->title === $serviceAdvantageData['service_title'];
            });

            if (!$service) {
                $this->command->warn("Service '{$serviceAdvantageData['service_title']}' not found, skipping advantages.");
                continue;
            }

            foreach ($serviceAdvantageData['advantages'] as $advantageData) {
                // Create advantage
                $advantage = ServiceAdvantage::create([
                    'service_id' => $service->id,
                    'order' => $advantageData['order'],
                    'status' => $advantageData['status'],
                    'icon' => $advantageData['icon']
                ]);

                // Create translations
                foreach ($advantageData['translations'] as $langCode => $translation) {
                    $language = Language::where('lang_code', $langCode)->first();
                    
                    if ($language) {
                        ServiceAdvantageTranslate::create([
                            'service_advantage_id' => $advantage->id,
                            'language_id' => $language->id,
                            'title' => $translation['title'],
                            'icon_alt_text' => $translation['icon_alt_text'],
                            'description' => $translation['description']
                        ]);
                    }
                }
            }
        }

        $this->command->info('Service Advantages seeded successfully!');
        $this->command->info('Note: Please add the actual advantage icons to the storage/app/public/service-advantages/ directory.');
    }
} 