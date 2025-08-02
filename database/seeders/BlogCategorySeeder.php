<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslate;
use App\Models\Language;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::active()->orderBy('order')->get();
        
        $categories = [
            [
                'order' => 1,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Mebel Dizaynı',
                        'seo_title' => 'Mebel Dizaynı Blog Kategoriyası',
                        'seo_keywords' => 'mebel dizaynı, interyer, ev mebeli, ofis mebeli',
                        'seo_description' => 'Mebel dizaynı sahəsində ən son trendlər və ideyalar'
                    ],
                    'en' => [
                        'title' => 'Furniture Design',
                        'seo_title' => 'Furniture Design Blog Category',
                        'seo_keywords' => 'furniture design, interior, home furniture, office furniture',
                        'seo_description' => 'Latest trends and ideas in furniture design'
                    ],
                    'ru' => [
                        'title' => 'Дизайн Мебели',
                        'seo_title' => 'Категория блога Дизайн Мебели',
                        'seo_keywords' => 'дизайн мебели, интерьер, домашняя мебель, офисная мебель',
                        'seo_description' => 'Последние тренды и идеи в дизайне мебели'
                    ]
                ]
            ],
            [
                'order' => 2,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Mebel Materialları',
                        'seo_title' => 'Mebel Materialları Blog Kategoriyası',
                        'seo_keywords' => 'mebel materialları, ağac, taxta, metal, dəri',
                        'seo_description' => 'Mebel istehsalında istifadə olunan materiallar haqqında məlumat'
                    ],
                    'en' => [
                        'title' => 'Furniture Materials',
                        'seo_title' => 'Furniture Materials Blog Category',
                        'seo_keywords' => 'furniture materials, wood, timber, metal, leather',
                        'seo_description' => 'Information about materials used in furniture production'
                    ],
                    'ru' => [
                        'title' => 'Материалы для Мебели',
                        'seo_title' => 'Категория блога Материалы для Мебели',
                        'seo_keywords' => 'материалы для мебели, дерево, металл, кожа',
                        'seo_description' => 'Информация о материалах, используемых в производстве мебели'
                    ]
                ]
            ],
            [
                'order' => 3,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Mebel Qayğısı',
                        'seo_title' => 'Mebel Qayğısı Blog Kategoriyası',
                        'seo_keywords' => 'mebel qayğısı, təmizlik, baxım, təmir',
                        'seo_description' => 'Mebel qayğısı və baxımı haqqında məsləhətlər'
                    ],
                    'en' => [
                        'title' => 'Furniture Care',
                        'seo_title' => 'Furniture Care Blog Category',
                        'seo_keywords' => 'furniture care, cleaning, maintenance, repair',
                        'seo_description' => 'Tips on furniture care and maintenance'
                    ],
                    'ru' => [
                        'title' => 'Уход за Мебелью',
                        'seo_title' => 'Категория блога Уход за Мебелью',
                        'seo_keywords' => 'уход за мебелью, чистка, обслуживание, ремонт',
                        'seo_description' => 'Советы по уходу и обслуживанию мебели'
                    ]
                ]
            ],
            [
                'order' => 4,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Mebel İstehsalı',
                        'seo_title' => 'Mebel İstehsalı Blog Kategoriyası',
                        'seo_keywords' => 'mebel istehsalı, fabrik, emalatxana, texnologiya',
                        'seo_description' => 'Mebel istehsal prosesləri və texnologiyaları'
                    ],
                    'en' => [
                        'title' => 'Furniture Manufacturing',
                        'seo_title' => 'Furniture Manufacturing Blog Category',
                        'seo_keywords' => 'furniture manufacturing, factory, workshop, technology',
                        'seo_description' => 'Furniture production processes and technologies'
                    ],
                    'ru' => [
                        'title' => 'Производство Мебели',
                        'seo_title' => 'Категория блога Производство Мебели',
                        'seo_keywords' => 'производство мебели, фабрика, мастерская, технология',
                        'seo_description' => 'Процессы и технологии производства мебели'
                    ]
                ]
            ],
            [
                'order' => 5,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Mebel Trendləri',
                        'seo_title' => 'Mebel Trendləri Blog Kategoriyası',
                        'seo_keywords' => 'mebel trendləri, yeni moda, stil, dizayn',
                        'seo_description' => 'Mebel sahəsində ən son trendlər və moda'
                    ],
                    'en' => [
                        'title' => 'Furniture Trends',
                        'seo_title' => 'Furniture Trends Blog Category',
                        'seo_keywords' => 'furniture trends, new fashion, style, design',
                        'seo_description' => 'Latest trends and fashion in furniture'
                    ],
                    'ru' => [
                        'title' => 'Тренды Мебели',
                        'seo_title' => 'Категория блога Тренды Мебели',
                        'seo_keywords' => 'тренды мебели, новая мода, стиль, дизайн',
                        'seo_description' => 'Последние тренды и мода в мебели'
                    ]
                ]
            ]
        ];

        foreach ($categories as $categoryData) {
            // Create blog category
            $category = BlogCategory::create([
                'order' => $categoryData['order'],
                'status' => $categoryData['status'],
            ]);

            // Create translations for each language
            foreach ($languages as $language) {
                $langCode = $language->lang_code;
                
                if (isset($categoryData['translations'][$langCode])) {
                    $translation = $categoryData['translations'][$langCode];
                    
                    BlogCategoryTranslate::create([
                        'blog_category_id' => $category->id,
                        'language_id' => $language->id,
                        'title' => $translation['title'],
                        'seo_title' => $translation['seo_title'],
                        'seo_keywords' => $translation['seo_keywords'],
                        'seo_description' => $translation['seo_description'],
                    ]);
                }
            }
        }

        $this->command->info('Blog categories seeded successfully!');
    }
}
