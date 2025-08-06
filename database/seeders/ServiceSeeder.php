<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceTranslate;
use App\Models\ServiceAdvantage;
use App\Models\ServiceAdvantageTranslate;
use App\Models\Language;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::all();

        // Sample services data
        $services = [
            [
                'slug' => 'construction-services',
                'status' => true,
                'order' => 1,
                'translations' => [
                    'az' => [
                        'title' => 'Tikinti Xidmətləri',
                        'card_description' => 'Keyfiyyətli tikinti və inşaat işləri',
                        'main_description' => 'Biz müasir texnologiyalardan istifadə edərək yüksək keyfiyyətli tikinti xidmətləri təmin edirik. Yaşayış və ticarət binaları, infrastruktur layihələri və s.',
                        'seo_title' => 'Tikinti Xidmətləri',
                        'seo_keywords' => 'tikinti, inşaat, bina, layihə',
                        'seo_description' => 'Professional tikinti xidmətləri'
                    ],
                    'en' => [
                        'title' => 'Construction Services',
                        'card_description' => 'Quality construction and building services',
                        'main_description' => 'We provide high-quality construction services using modern technologies. Residential and commercial buildings, infrastructure projects and more.',
                        'seo_title' => 'Construction Services',
                        'seo_keywords' => 'construction, building, project',
                        'seo_description' => 'Professional construction services'
                    ],
                    'ru' => [
                        'title' => 'Строительные услуги',
                        'card_description' => 'Качественные строительные услуги',
                        'main_description' => 'Мы предоставляем качественные строительные услуги, используя современные технологии. Жилые и коммерческие здания, инфраструктурные проекты и многое другое.',
                        'seo_title' => 'Строительные услуги',
                        'seo_keywords' => 'строительство, здание, проект',
                        'seo_description' => 'Профессиональные строительные услуги'
                    ]
                ],
                'advantages' => [
                    [
                        'icon' => 'services/icons/quality-assurance.svg',
                        'status' => true,
                        'order' => 1,
                        'translations' => [
                            'az' => [
                                'title' => 'Keyfiyyət Təminatı',
                                'icon_alt_text' => 'Keyfiyyət təminatı ikonu',
                                'description' => 'Yüksək keyfiyyət standartları'
                            ],
                            'en' => [
                                'title' => 'Quality Assurance',
                                'icon_alt_text' => 'Quality assurance icon',
                                'description' => 'High quality standards'
                            ],
                            'ru' => [
                                'title' => 'Контроль качества',
                                'icon_alt_text' => 'Иконка контроля качества',
                                'description' => 'Высокие стандарты качества'
                            ]
                        ]
                    ],
                    [
                        'icon' => 'services/icons/modern-technology.svg',
                        'status' => true,
                        'order' => 2,
                        'translations' => [
                            'az' => [
                                'title' => 'Müasir Texnologiya',
                                'icon_alt_text' => 'Müasir texnologiya ikonu',
                                'description' => 'Ən son texnologiyalardan istifadə'
                            ],
                            'en' => [
                                'title' => 'Modern Technology',
                                'icon_alt_text' => 'Modern technology icon',
                                'description' => 'Using latest technologies'
                            ],
                            'ru' => [
                                'title' => 'Современные технологии',
                                'icon_alt_text' => 'Иконка современных технологий',
                                'description' => 'Использование новейших технологий'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'slug' => 'renovation-services',
                'status' => true,
                'order' => 2,
                'translations' => [
                    'az' => [
                        'title' => 'Təmir və Yeniləmə',
                        'card_description' => 'Binaların təmiri və yenilənməsi',
                        'main_description' => 'Mövcud binaların təmiri, yenilənməsi və modernləşdirilməsi xidmətləri. Daxili və xarici təmir işləri.',
                        'seo_title' => 'Təmir və Yeniləmə Xidmətləri',
                        'seo_keywords' => 'təmir, yeniləmə, bina təmiri',
                        'seo_description' => 'Professional təmir və yeniləmə xidmətləri'
                    ],
                    'en' => [
                        'title' => 'Renovation Services',
                        'card_description' => 'Building repair and renovation',
                        'main_description' => 'Repair, renovation and modernization services for existing buildings. Interior and exterior renovation works.',
                        'seo_title' => 'Renovation Services',
                        'seo_keywords' => 'renovation, repair, building renovation',
                        'seo_description' => 'Professional renovation services'
                    ],
                    'ru' => [
                        'title' => 'Услуги по ремонту и реконструкции',
                        'card_description' => 'Ремонт и реконструкция зданий',
                        'main_description' => 'Услуги по ремонту, реконструкции и модернизации существующих зданий. Внутренние и внешние ремонтные работы.',
                        'seo_title' => 'Услуги по ремонту и реконструкции',
                        'seo_keywords' => 'ремонт, реконструкция, ремонт зданий',
                        'seo_description' => 'Профессиональные услуги по ремонту и реконструкции'
                    ]
                ],
                'advantages' => [
                    [
                        'icon' => 'services/icons/interior-design.svg',
                        'status' => true,
                        'order' => 1,
                        'translations' => [
                            'az' => [
                                'title' => 'Daxili Dizayn',
                                'icon_alt_text' => 'Daxili dizayn ikonu',
                                'description' => 'Müasir daxili dizayn həlləri'
                            ],
                            'en' => [
                                'title' => 'Interior Design',
                                'icon_alt_text' => 'Interior design icon',
                                'description' => 'Modern interior design solutions'
                            ],
                            'ru' => [
                                'title' => 'Дизайн интерьера',
                                'icon_alt_text' => 'Иконка дизайна интерьера',
                                'description' => 'Современные решения дизайна интерьера'
                            ]
                        ]
                    ],
                    [
                        'icon' => 'services/icons/expert-team.svg',
                        'status' => true,
                        'order' => 2,
                        'translations' => [
                            'az' => [
                                'title' => 'Ekspert Komanda',
                                'icon_alt_text' => 'Ekspert komanda ikonu',
                                'description' => 'Təcrübəli mütəxəssislər'
                            ],
                            'en' => [
                                'title' => 'Expert Team',
                                'icon_alt_text' => 'Expert team icon',
                                'description' => 'Experienced specialists'
                            ],
                            'ru' => [
                                'title' => 'Команда экспертов',
                                'icon_alt_text' => 'Иконка команды экспертов',
                                'description' => 'Опытные специалисты'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'slug' => 'project-management',
                'status' => true,
                'order' => 3,
                'translations' => [
                    'az' => [
                        'title' => 'Layihə İdarəetməsi',
                        'card_description' => 'Tikinti layihələrinin idarə edilməsi',
                        'main_description' => 'Tikinti layihələrinin tam idarə edilməsi xidmətləri. Planlaşdırma, koordinasiya və nəzarət.',
                        'seo_title' => 'Layihə İdarəetməsi Xidmətləri',
                        'seo_keywords' => 'layihə idarəetməsi, tikinti layihəsi',
                        'seo_description' => 'Professional layihə idarəetməsi xidmətləri'
                    ],
                    'en' => [
                        'title' => 'Project Management',
                        'card_description' => 'Construction project management',
                        'main_description' => 'Complete construction project management services. Planning, coordination and supervision.',
                        'seo_title' => 'Project Management Services',
                        'seo_keywords' => 'project management, construction project',
                        'seo_description' => 'Professional project management services'
                    ],
                    'ru' => [
                        'title' => 'Управление проектами',
                        'card_description' => 'Управление строительными проектами',
                        'main_description' => 'Полные услуги по управлению строительными проектами. Планирование, координация и надзор.',
                        'seo_title' => 'Услуги управления проектами',
                        'seo_keywords' => 'управление проектами, строительный проект',
                        'seo_description' => 'Профессиональные услуги управления проектами'
                    ]
                ],
                'advantages' => [
                    [
                        'icon' => 'services/icons/planning.svg',
                        'status' => true,
                        'order' => 1,
                        'translations' => [
                            'az' => [
                                'title' => 'Planlaşdırma',
                                'icon_alt_text' => 'Planlaşdırma ikonu',
                                'description' => 'Detallı planlaşdırma və analiz'
                            ],
                            'en' => [
                                'title' => 'Planning',
                                'icon_alt_text' => 'Planning icon',
                                'description' => 'Detailed planning and analysis'
                            ],
                            'ru' => [
                                'title' => 'Планирование',
                                'icon_alt_text' => 'Иконка планирования',
                                'description' => 'Детальное планирование и анализ'
                            ]
                        ]
                    ],
                    [
                        'icon' => 'services/icons/supervision.svg',
                        'status' => true,
                        'order' => 2,
                        'translations' => [
                            'az' => [
                                'title' => 'Nəzarət',
                                'icon_alt_text' => 'Nəzarət ikonu',
                                'description' => 'Davamlı nəzarət və hesabat'
                            ],
                            'en' => [
                                'title' => 'Supervision',
                                'icon_alt_text' => 'Supervision icon',
                                'description' => 'Continuous monitoring and reporting'
                            ],
                            'ru' => [
                                'title' => 'Надзор',
                                'icon_alt_text' => 'Иконка надзора',
                                'description' => 'Постоянный мониторинг и отчетность'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        foreach ($services as $serviceData) {
            // Create service
            $service = Service::create([
                'slug' => $serviceData['slug'],
                'status' => $serviceData['status'],
                'order' => $serviceData['order']
            ]);

            // Create translations for each language
            foreach ($languages as $language) {
                $translationData = $serviceData['translations'][$language->lang_code] ?? [];
                
                ServiceTranslate::create([
                    'service_id' => $service->id,
                    'language_id' => $language->id,
                    'title' => $translationData['title'] ?? 'Default Title',
                    'slug' => $serviceData['slug'],
                    'card_description' => $translationData['card_description'] ?? 'Default card description',
                    'main_description' => $translationData['main_description'] ?? 'Default main description',
                    'seo_title' => $translationData['seo_title'] ?? 'Default SEO title',
                    'seo_keywords' => $translationData['seo_keywords'] ?? 'Default SEO keywords',
                    'seo_description' => $translationData['seo_description'] ?? 'Default SEO description'
                ]);
            }

            // Create advantages for this service
            if (isset($serviceData['advantages'])) {
                foreach ($serviceData['advantages'] as $advantageData) {
                    $advantage = ServiceAdvantage::create([
                        'service_id' => $service->id,
                        'icon' => $advantageData['icon'],
                        'status' => $advantageData['status'],
                        'order' => $advantageData['order']
                    ]);

                    // Create advantage translations for each language
                    foreach ($languages as $language) {
                        $advantageTranslationData = $advantageData['translations'][$language->lang_code] ?? [];
                        
                        ServiceAdvantageTranslate::create([
                            'service_advantage_id' => $advantage->id,
                            'language_id' => $language->id,
                            'title' => $advantageTranslationData['title'] ?? 'Default Advantage Title',
                            'icon_alt_text' => $advantageTranslationData['icon_alt_text'] ?? 'Default icon alt text',
                            'description' => $advantageTranslationData['description'] ?? 'Default advantage description'
                        ]);
                    }
                }
            }
        }

        $this->command->info('Services and advantages seeded successfully!');
    }
} 