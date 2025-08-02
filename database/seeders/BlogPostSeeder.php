<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlogPost;
use App\Models\BlogPostTranslate;
use App\Models\BlogCategory;
use App\Models\Language;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $languages = Language::active()->orderBy('order')->get();
        $categories = BlogCategory::active()->ordered()->get();
        
        if ($categories->isEmpty()) {
            $this->command->warn('No blog categories found. Please run BlogCategorySeeder first.');
            return;
        }

        // Mebel Dizaynı kateqoriyası üçün 5 blog post
        $designCategory = $categories->first();
        if ($designCategory) {
            $designPosts = [
                [
                    'title' => [
                        'az' => 'Modern Ev Dizaynında Mebel Seçimi',
                        'en' => 'Furniture Selection in Modern Home Design',
                        'ru' => 'Выбор мебели в современном дизайне дома'
                    ],
                    'description' => [
                        'az' => '<h2>Modern Ev Dizaynında Mebel Seçimi</h2><p>Modern ev dizaynında mebel seçimi çox vacibdir. Düzgün mebel seçimi evinizin görünüşünü tamamilə dəyişə bilər.</p>',
                        'en' => '<h2>Furniture Selection in Modern Home Design</h2><p>Furniture selection is crucial in modern home design. The right furniture choice can completely transform the look of your home.</p>',
                        'ru' => '<h2>Выбор мебели в современном дизайне дома</h2><p>Выбор мебели имеет решающее значение в современном дизайне дома. Правильный выбор мебели может полностью изменить внешний вид вашего дома.</p>'
                    ]
                ],
                [
                    'title' => [
                        'az' => 'Minimalist Mebel Dizaynının Sirləri',
                        'en' => 'Secrets of Minimalist Furniture Design',
                        'ru' => 'Секреты минималистичного дизайна мебели'
                    ],
                    'description' => [
                        'az' => '<h2>Minimalist Mebel Dizaynının Sirləri</h2><p>Minimalist mebel dizaynı sadəlik və funksionallığı birləşdirir. Bu stil müasir evlər üçün ideal seçimdir.</p>',
                        'en' => '<h2>Secrets of Minimalist Furniture Design</h2><p>Minimalist furniture design combines simplicity and functionality. This style is an ideal choice for modern homes.</p>',
                        'ru' => '<h2>Секреты минималистичного дизайна мебели</h2><p>Минималистичный дизайн мебели сочетает простоту и функциональность. Этот стиль является идеальным выбором для современных домов.</p>'
                    ]
                ],
                [
                    'title' => [
                        'az' => 'Klassik və Müasir Mebel Birləşməsi',
                        'en' => 'Combining Classic and Modern Furniture',
                        'ru' => 'Сочетание классической и современной мебели'
                    ],
                    'description' => [
                        'az' => '<h2>Klassik və Müasir Mebel Birləşməsi</h2><p>Klassik və müasir mebellərin birləşməsi unikal və cəlbedici interyer yarada bilər.</p>',
                        'en' => '<h2>Combining Classic and Modern Furniture</h2><p>The combination of classic and modern furniture can create a unique and attractive interior.</p>',
                        'ru' => '<h2>Сочетание классической и современной мебели</h2><p>Сочетание классической и современной мебели может создать уникальный и привлекательный интерьер.</p>'
                    ]
                ],
                [
                    'title' => [
                        'az' => 'Kiçik Məkanlar üçün Mebel Dizaynı',
                        'en' => 'Furniture Design for Small Spaces',
                        'ru' => 'Дизайн мебели для маленьких помещений'
                    ],
                    'description' => [
                        'az' => '<h2>Kiçik Məkanlar üçün Mebel Dizaynı</h2><p>Kiçik məkanlar üçün mebel seçimi xüsusi diqqət tələb edir. Bu yazıda optimal həllər haqqında məlumat verəcəyik.</p>',
                        'en' => '<h2>Furniture Design for Small Spaces</h2><p>Furniture selection for small spaces requires special attention. In this article, we will provide information about optimal solutions.</p>',
                        'ru' => '<h2>Дизайн мебели для маленьких помещений</h2><p>Выбор мебели для маленьких помещений требует особого внимания. В этой статье мы предоставим информацию об оптимальных решениях.</p>'
                    ]
                ],
                [
                    'title' => [
                        'az' => 'Rəng Teorisi və Mebel Dizaynı',
                        'en' => 'Color Theory and Furniture Design',
                        'ru' => 'Теория цвета и дизайн мебели'
                    ],
                    'description' => [
                        'az' => '<h2>Rəng Teorisi və Mebel Dizaynı</h2><p>Rəng seçimi mebel dizaynında ən vacib elementlərdən biridir. Bu yazıda rəng nəzəriyyəsi haqqında məlumat verəcəyik.</p>',
                        'en' => '<h2>Color Theory and Furniture Design</h2><p>Color selection is one of the most important elements in furniture design. In this article, we will provide information about color theory.</p>',
                        'ru' => '<h2>Теория цвета и дизайн мебели</h2><p>Выбор цвета является одним из самых важных элементов в дизайне мебели. В этой статье мы предоставим информацию о теории цвета.</p>'
                    ]
                ]
            ];

            foreach ($designPosts as $postData) {
                $this->createBlogPost($designCategory, $postData, $languages);
            }
        }

        // Mebel Materialları kateqoriyası üçün 5 blog post
        $materialCategory = $categories->skip(1)->first();
        if ($materialCategory) {
            $materialPosts = [
                [
                    'title' => [
                        'az' => 'Təbii Ağac Materialları',
                        'en' => 'Natural Wood Materials',
                        'ru' => 'Натуральные древесные материалы'
                    ],
                    'description' => [
                        'az' => '<h2>Təbii Ağac Materialları</h2><p>Təbii ağac materialları mebel istehsalında ən populyar seçimdir. Bu materiallar keyfiyyətli və uzunömürlü mebel yaratmağa imkan verir.</p>',
                        'en' => '<h2>Natural Wood Materials</h2><p>Natural wood materials are the most popular choice in furniture manufacturing. These materials allow creating quality and durable furniture.</p>',
                        'ru' => '<h2>Натуральные древесные материалы</h2><p>Натуральные древесные материалы являются самым популярным выбором в производстве мебели. Эти материалы позволяют создавать качественную и долговечную мебель.</p>'
                    ]
                ],
                [
                    'title' => [
                        'az' => 'MDF və Plywood Materialları',
                        'en' => 'MDF and Plywood Materials',
                        'ru' => 'Материалы МДФ и фанера'
                    ],
                    'description' => [
                        'az' => '<h2>MDF və Plywood Materialları</h2><p>MDF və plywood müasir mebel istehsalında geniş istifadə olunan materiallardır. Bu materiallar keyfiyyətli və əlverişli qiymətə mebel yaratmağa imkan verir.</p>',
                        'en' => '<h2>MDF and Plywood Materials</h2><p>MDF and plywood are widely used materials in modern furniture manufacturing. These materials allow creating quality furniture at affordable prices.</p>',
                        'ru' => '<h2>Материалы МДФ и фанера</h2><p>МДФ и фанера являются широко используемыми материалами в современном производстве мебели. Эти материалы позволяют создавать качественную мебель по доступным ценам.</p>'
                    ]
                ],
                [
                    'title' => [
                        'az' => 'Metal və Şüşə Materialları',
                        'en' => 'Metal and Glass Materials',
                        'ru' => 'Металлические и стеклянные материалы'
                    ],
                    'description' => [
                        'az' => '<h2>Metal və Şüşə Materialları</h2><p>Metal və şüşə materialları müasir mebel dizaynında geniş istifadə olunur. Bu materiallar cəlbedici və funksional mebel yaratmağa imkan verir.</p>',
                        'en' => '<h2>Metal and Glass Materials</h2><p>Metal and glass materials are widely used in modern furniture design. These materials allow creating attractive and functional furniture.</p>',
                        'ru' => '<h2>Металлические и стеклянные материалы</h2><p>Металлические и стеклянные материалы широко используются в современном дизайне мебели. Эти материалы позволяют создавать привлекательную и функциональную мебель.</p>'
                    ]
                ],
                [
                    'title' => [
                        'az' => 'Dəri və Parça Materialları',
                        'en' => 'Leather and Fabric Materials',
                        'ru' => 'Кожаные и тканевые материалы'
                    ],
                    'description' => [
                        'az' => '<h2>Dəri və Parça Materialları</h2><p>Dəri və parça materialları mebel üzlüyü üçün ən populyar seçimlərdir. Bu materiallar rahatlıq və estetik görünüş təmin edir.</p>',
                        'en' => '<h2>Leather and Fabric Materials</h2><p>Leather and fabric materials are the most popular choices for furniture upholstery. These materials provide comfort and aesthetic appearance.</p>',
                        'ru' => '<h2>Кожаные и тканевые материалы</h2><p>Кожаные и тканевые материалы являются самыми популярными выборами для обивки мебели. Эти материалы обеспечивают комфорт и эстетический вид.</p>'
                    ]
                ],
                [
                    'title' => [
                        'az' => 'Ekoloji Mebel Materialları',
                        'en' => 'Eco-Friendly Furniture Materials',
                        'ru' => 'Экологичные материалы мебели'
                    ],
                    'description' => [
                        'az' => '<h2>Ekoloji Mebel Materialları</h2><p>Ekoloji mebel materialları ətraf mühitə qayğı göstərmək istəyənlər üçün ideal seçimdir. Bu materiallar təbii və təhlükəsizdir.</p>',
                        'en' => '<h2>Eco-Friendly Furniture Materials</h2><p>Eco-friendly furniture materials are an ideal choice for those who want to care for the environment. These materials are natural and safe.</p>',
                        'ru' => '<h2>Экологичные материалы мебели</h2><p>Экологичные материалы мебели являются идеальным выбором для тех, кто хочет заботиться об окружающей среде. Эти материалы натуральные и безопасные.</p>'
                    ]
                ]
            ];

            foreach ($materialPosts as $postData) {
                $this->createBlogPost($materialCategory, $postData, $languages);
            }
        }

        $this->command->info('Blog posts seeded successfully!');
    }

    private function createBlogPost($category, $postData, $languages)
    {
        $blogPost = BlogPost::create([
            'blog_category_id' => $category->id,
            'card_image' => null,
            'inner_banner_image' => null,
            'published_at' => now(),
            'status' => true,
        ]);

        foreach ($languages as $language) {
            $langCode = $language->lang_code;
            if (isset($postData['title'][$langCode])) {
                BlogPostTranslate::create([
                    'blog_post_id' => $blogPost->id,
                    'language_id' => $language->id,
                    'title' => $postData['title'][$langCode],
                    'description' => $postData['description'][$langCode],
                    'card_image_alt_text' => $postData['title'][$langCode],
                    'card_description' => 'Mebel sahəsində ətraflı məlumat və tövsiyələr.',
                    'inner_banner_alt_text' => $postData['title'][$langCode],
                    'seo_title' => $postData['title'][$langCode] . ' - Gardashlar',
                    'seo_keywords' => 'mebel, dizayn, materiallar, interyer',
                    'seo_description' => 'Mebel sahəsində ətraflı məlumat və tövsiyələr.',
                ]);
            }
        }
    }
} 