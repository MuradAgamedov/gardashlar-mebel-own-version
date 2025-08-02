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
                        'title' => 'Texnologiya',
                        'seo_title' => 'Texnologiya Blog Kategoriyası',
                        'seo_keywords' => 'texnologiya, blog, yeni texnologiyalar, IT',
                        'seo_description' => 'Texnologiya sahəsində ən son yeniliklər və xəbərlər'
                    ],
                    'en' => [
                        'title' => 'Technology',
                        'seo_title' => 'Technology Blog Category',
                        'seo_keywords' => 'technology, blog, new technologies, IT',
                        'seo_description' => 'Latest news and updates in technology field'
                    ],
                    'ru' => [
                        'title' => 'Технологии',
                        'seo_title' => 'Категория блога Технологии',
                        'seo_keywords' => 'технологии, блог, новые технологии, IT',
                        'seo_description' => 'Последние новости и обновления в области технологий'
                    ]
                ]
            ],
            [
                'order' => 2,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Biznes',
                        'seo_title' => 'Biznes Blog Kategoriyası',
                        'seo_keywords' => 'biznes, iqtisadiyyat, investisiya, sahibkarlıq',
                        'seo_description' => 'Biznes və iqtisadiyyat sahəsində faydalı məlumatlar'
                    ],
                    'en' => [
                        'title' => 'Business',
                        'seo_title' => 'Business Blog Category',
                        'seo_keywords' => 'business, economy, investment, entrepreneurship',
                        'seo_description' => 'Useful information in business and economy field'
                    ],
                    'ru' => [
                        'title' => 'Бизнес',
                        'seo_title' => 'Категория блога Бизнес',
                        'seo_keywords' => 'бизнес, экономика, инвестиции, предпринимательство',
                        'seo_description' => 'Полезная информация в области бизнеса и экономики'
                    ]
                ]
            ],
            [
                'order' => 3,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Sağlamlıq',
                        'seo_title' => 'Sağlamlıq Blog Kategoriyası',
                        'seo_keywords' => 'sağlamlıq, tibb, həkim, müalicə',
                        'seo_description' => 'Sağlamlıq və tibb sahəsində məsləhətlər'
                    ],
                    'en' => [
                        'title' => 'Health',
                        'seo_title' => 'Health Blog Category',
                        'seo_keywords' => 'health, medicine, doctor, treatment',
                        'seo_description' => 'Advice in health and medicine field'
                    ],
                    'ru' => [
                        'title' => 'Здоровье',
                        'seo_title' => 'Категория блога Здоровье',
                        'seo_keywords' => 'здоровье, медицина, врач, лечение',
                        'seo_description' => 'Советы в области здоровья и медицины'
                    ]
                ]
            ],
            [
                'order' => 4,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'İdman',
                        'seo_title' => 'İdman Blog Kategoriyası',
                        'seo_keywords' => 'idman, futbol, basketbol, yarış',
                        'seo_description' => 'İdman sahəsində ən son xəbərlər və nəticələr'
                    ],
                    'en' => [
                        'title' => 'Sports',
                        'seo_title' => 'Sports Blog Category',
                        'seo_keywords' => 'sports, football, basketball, competition',
                        'seo_description' => 'Latest news and results in sports field'
                    ],
                    'ru' => [
                        'title' => 'Спорт',
                        'seo_title' => 'Категория блога Спорт',
                        'seo_keywords' => 'спорт, футбол, баскетбол, соревнования',
                        'seo_description' => 'Последние новости и результаты в области спорта'
                    ]
                ]
            ],
            [
                'order' => 5,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Mədəniyyət',
                        'seo_title' => 'Mədəniyyət Blog Kategoriyası',
                        'seo_keywords' => 'mədəniyyət, sənət, musiqi, kino',
                        'seo_description' => 'Mədəniyyət və sənət sahəsində yeniliklər'
                    ],
                    'en' => [
                        'title' => 'Culture',
                        'seo_title' => 'Culture Blog Category',
                        'seo_keywords' => 'culture, art, music, cinema',
                        'seo_description' => 'Updates in culture and art field'
                    ],
                    'ru' => [
                        'title' => 'Культура',
                        'seo_title' => 'Категория блога Культура',
                        'seo_keywords' => 'культура, искусство, музыка, кино',
                        'seo_description' => 'Обновления в области культуры и искусства'
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
