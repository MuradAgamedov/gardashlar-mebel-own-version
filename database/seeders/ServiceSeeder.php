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
                'slug' => 'web-development',
                'status' => true,
                'order' => 1,
                'translations' => [
                    'az' => [
                        'title' => 'Veb İnkişaf',
                        'card_description' => 'Müasir və funksional veb saytlar yaradırıq',
                        'main_description' => 'Biz müasir texnologiyalardan istifadə edərək yüksək keyfiyyətli veb saytlar yaradırıq. Responsive dizayn, SEO optimizasiyası və istifadəçi dostu interfeys təmin edirik.',
                        'seo_title' => 'Veb İnkişaf Xidmətləri',
                        'seo_keywords' => 'veb inkişaf, sayt yaratma, web development',
                        'seo_description' => 'Professional veb inkişaf xidmətləri'
                    ],
                    'en' => [
                        'title' => 'Web Development',
                        'card_description' => 'We create modern and functional websites',
                        'main_description' => 'We create high-quality websites using modern technologies. We provide responsive design, SEO optimization and user-friendly interface.',
                        'seo_title' => 'Web Development Services',
                        'seo_keywords' => 'web development, website creation',
                        'seo_description' => 'Professional web development services'
                    ],
                    'ru' => [
                        'title' => 'Веб-разработка',
                        'card_description' => 'Создаем современные и функциональные веб-сайты',
                        'main_description' => 'Мы создаем качественные веб-сайты, используя современные технологии. Обеспечиваем адаптивный дизайн, SEO-оптимизацию и удобный интерфейс.',
                        'seo_title' => 'Услуги веб-разработки',
                        'seo_keywords' => 'веб-разработка, создание сайтов',
                        'seo_description' => 'Профессиональные услуги веб-разработки'
                    ]
                ],
                'advantages' => [
                    [
                        'icon' => 'services/icons/responsive-design.svg',
                        'status' => true,
                        'order' => 1,
                        'translations' => [
                            'az' => [
                                'title' => 'Responsive Dizayn',
                                'icon_alt_text' => 'Responsive dizayn ikonu',
                                'description' => 'Bütün cihazlarda mükəmməl görünən saytlar'
                            ],
                            'en' => [
                                'title' => 'Responsive Design',
                                'icon_alt_text' => 'Responsive design icon',
                                'description' => 'Websites that look perfect on all devices'
                            ],
                            'ru' => [
                                'title' => 'Адаптивный дизайн',
                                'icon_alt_text' => 'Иконка адаптивного дизайна',
                                'description' => 'Сайты, которые отлично выглядят на всех устройствах'
                            ]
                        ]
                    ],
                    [
                        'icon' => 'services/icons/seo-optimization.svg',
                        'status' => true,
                        'order' => 2,
                        'translations' => [
                            'az' => [
                                'title' => 'SEO Optimizasiyası',
                                'icon_alt_text' => 'SEO optimizasiyası ikonu',
                                'description' => 'Axtarış motorlarında yüksək sıralama'
                            ],
                            'en' => [
                                'title' => 'SEO Optimization',
                                'icon_alt_text' => 'SEO optimization icon',
                                'description' => 'High ranking in search engines'
                            ],
                            'ru' => [
                                'title' => 'SEO-оптимизация',
                                'icon_alt_text' => 'Иконка SEO-оптимизации',
                                'description' => 'Высокий рейтинг в поисковых системах'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'slug' => 'mobile-app-development',
                'status' => true,
                'order' => 2,
                'translations' => [
                    'az' => [
                        'title' => 'Mobil Tətbiq İnkişafı',
                        'card_description' => 'iOS və Android üçün mobil tətbiqlər',
                        'main_description' => 'iOS və Android platformaları üçün yüksək keyfiyyətli mobil tətbiqlər yaradırıq. Native və cross-platform həllər təklif edirik.',
                        'seo_title' => 'Mobil Tətbiq İnkişafı',
                        'seo_keywords' => 'mobil tətbiq, iOS, Android, app development',
                        'seo_description' => 'Professional mobil tətbiq inkişafı xidmətləri'
                    ],
                    'en' => [
                        'title' => 'Mobile App Development',
                        'card_description' => 'Mobile applications for iOS and Android',
                        'main_description' => 'We create high-quality mobile applications for iOS and Android platforms. We offer native and cross-platform solutions.',
                        'seo_title' => 'Mobile App Development',
                        'seo_keywords' => 'mobile app, iOS, Android, app development',
                        'seo_description' => 'Professional mobile app development services'
                    ],
                    'ru' => [
                        'title' => 'Разработка мобильных приложений',
                        'card_description' => 'Мобильные приложения для iOS и Android',
                        'main_description' => 'Мы создаем качественные мобильные приложения для платформ iOS и Android. Предлагаем нативные и кроссплатформенные решения.',
                        'seo_title' => 'Разработка мобильных приложений',
                        'seo_keywords' => 'мобильное приложение, iOS, Android, разработка приложений',
                        'seo_description' => 'Профессиональные услуги разработки мобильных приложений'
                    ]
                ],
                'advantages' => [
                    [
                        'icon' => 'services/icons/native-development.svg',
                        'status' => true,
                        'order' => 1,
                        'translations' => [
                            'az' => [
                                'title' => 'Native İnkişaf',
                                'icon_alt_text' => 'Native inkişaf ikonu',
                                'description' => 'Platforma xüsusi funksionallıq'
                            ],
                            'en' => [
                                'title' => 'Native Development',
                                'icon_alt_text' => 'Native development icon',
                                'description' => 'Platform-specific functionality'
                            ],
                            'ru' => [
                                'title' => 'Нативная разработка',
                                'icon_alt_text' => 'Иконка нативной разработки',
                                'description' => 'Платформо-специфичная функциональность'
                            ]
                        ]
                    ],
                    [
                        'icon' => 'services/icons/cross-platform.svg',
                        'status' => true,
                        'order' => 2,
                        'translations' => [
                            'az' => [
                                'title' => 'Cross-Platform',
                                'icon_alt_text' => 'Cross-platform ikonu',
                                'description' => 'Bir kod ilə çoxlu platformalar'
                            ],
                            'en' => [
                                'title' => 'Cross-Platform',
                                'icon_alt_text' => 'Cross-platform icon',
                                'description' => 'Multiple platforms with one code'
                            ],
                            'ru' => [
                                'title' => 'Кроссплатформенность',
                                'icon_alt_text' => 'Иконка кроссплатформенности',
                                'description' => 'Множество платформ с одним кодом'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'slug' => 'ui-ux-design',
                'status' => true,
                'order' => 3,
                'translations' => [
                    'az' => [
                        'title' => 'UI/UX Dizayn',
                        'card_description' => 'İstifadəçi təcrübəsi və interfeys dizaynı',
                        'main_description' => 'İstifadəçi təcrübəsini əsas götürərək gözəl və funksional interfeyslər yaradırıq. İstifadəçi araşdırması və prototipləşdirmə xidmətləri təmin edirik.',
                        'seo_title' => 'UI/UX Dizayn Xidmətləri',
                        'seo_keywords' => 'UI dizayn, UX dizayn, istifadəçi təcrübəsi',
                        'seo_description' => 'Professional UI/UX dizayn xidmətləri'
                    ],
                    'en' => [
                        'title' => 'UI/UX Design',
                        'card_description' => 'User experience and interface design',
                        'main_description' => 'We create beautiful and functional interfaces focusing on user experience. We provide user research and prototyping services.',
                        'seo_title' => 'UI/UX Design Services',
                        'seo_keywords' => 'UI design, UX design, user experience',
                        'seo_description' => 'Professional UI/UX design services'
                    ],
                    'ru' => [
                        'title' => 'UI/UX дизайн',
                        'card_description' => 'Дизайн пользовательского опыта и интерфейса',
                        'main_description' => 'Мы создаем красивые и функциональные интерфейсы, ориентируясь на пользовательский опыт. Предоставляем услуги пользовательских исследований и прототипирования.',
                        'seo_title' => 'Услуги UI/UX дизайна',
                        'seo_keywords' => 'UI дизайн, UX дизайн, пользовательский опыт',
                        'seo_description' => 'Профессиональные услуги UI/UX дизайна'
                    ]
                ],
                'advantages' => [
                    [
                        'icon' => 'services/icons/user-research.svg',
                        'status' => true,
                        'order' => 1,
                        'translations' => [
                            'az' => [
                                'title' => 'İstifadəçi Araşdırması',
                                'icon_alt_text' => 'İstifadəçi araşdırması ikonu',
                                'description' => 'Hədəf auditoriyanı anlamaq'
                            ],
                            'en' => [
                                'title' => 'User Research',
                                'icon_alt_text' => 'User research icon',
                                'description' => 'Understanding target audience'
                            ],
                            'ru' => [
                                'title' => 'Пользовательские исследования',
                                'icon_alt_text' => 'Иконка пользовательских исследований',
                                'description' => 'Понимание целевой аудитории'
                            ]
                        ]
                    ],
                    [
                        'icon' => 'services/icons/prototyping.svg',
                        'status' => true,
                        'order' => 2,
                        'translations' => [
                            'az' => [
                                'title' => 'Prototipləşdirmə',
                                'icon_alt_text' => 'Prototipləşdirmə ikonu',
                                'description' => 'İnteraktiv prototiplər yaratmaq'
                            ],
                            'en' => [
                                'title' => 'Prototyping',
                                'icon_alt_text' => 'Prototyping icon',
                                'description' => 'Creating interactive prototypes'
                            ],
                            'ru' => [
                                'title' => 'Прототипирование',
                                'icon_alt_text' => 'Иконка прототипирования',
                                'description' => 'Создание интерактивных прототипов'
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