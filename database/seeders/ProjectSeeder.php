<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ProjectTranslate;
use App\Models\ProjectImage;
use App\Models\ProjectCategory;
use App\Models\Language;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::all();
        $projectCategories = ProjectCategory::all();

        if ($projectCategories->isEmpty()) {
            $this->command->warn('No project categories found. Please run ProjectCategorySeeder first.');
            return;
        }

        $projectData = [
            // Yumşaq mebellər kateqoriyası üçün layihələr
            [
                'category_id' => 1,
                'projects' => [
                    [
                        'az' => [
                            'title' => 'Modern Yumşaq Mebel Dizaynı',
                            'company_name' => 'Comfort Home',
                            'slug' => 'modern-yumsaq-mebel-dizayni',
                            'description' => 'Bu layihədə müasir yumşaq mebel dizaynı ilə rahat və funksional yaşayış məkanı yaradılmışdır. Yüksək keyfiyyətli materiallar və professional dizayn həlləri istifadə edilmişdir.'
                        ],
                        'en' => [
                            'title' => 'Modern Soft Furniture Design',
                            'company_name' => 'Comfort Home',
                            'slug' => 'modern-soft-furniture-design',
                            'description' => 'This project features modern soft furniture design creating a comfortable and functional living space. High-quality materials and professional design solutions were used.'
                        ],
                        'ru' => [
                            'title' => 'Современный дизайн мягкой мебели',
                            'company_name' => 'Comfort Home',
                            'slug' => 'sovremennyy-dizayn-myagkoy-mebeli',
                            'description' => 'В этом проекте представлен современный дизайн мягкой мебели, создающий комфортное и функциональное жилое пространство. Использованы высококачественные материалы и профессиональные дизайнерские решения.'
                        ]
                    ],
                    [
                        'az' => [
                            'title' => 'Lüks Divan Kolleksiyası',
                            'company_name' => 'Elite Furniture',
                            'slug' => 'luks-divan-kolleksiyasi',
                            'description' => 'Lüks divan kolleksiyası ilə yaşayış otağınızı transformasiya edin. Eksklüziv materiallar və unikal dizayn elementləri.'
                        ],
                        'en' => [
                            'title' => 'Luxury Sofa Collection',
                            'company_name' => 'Elite Furniture',
                            'slug' => 'luxury-sofa-collection',
                            'description' => 'Transform your living room with our luxury sofa collection. Exclusive materials and unique design elements.'
                        ],
                        'ru' => [
                            'title' => 'Коллекция люксовых диванов',
                            'company_name' => 'Elite Furniture',
                            'slug' => 'kollektsiya-lyuksovykh-divanov',
                            'description' => 'Преобразуйте свою гостиную с нашей коллекцией люксовых диванов. Эксклюзивные материалы и уникальные дизайнерские элементы.'
                        ]
                    ]
                ]
            ],
            // Masalar kateqoriyası üçün layihələr
            [
                'category_id' => 2,
                'projects' => [
                    [
                        'az' => [
                            'title' => 'Modern Yemək Masası Dizaynı',
                            'company_name' => 'Table Masters',
                            'slug' => 'modern-yemek-masasi-dizayni',
                            'description' => 'Modern yemək masası dizaynı ilə ailə yeməklərinizi daha xüsusi edin. Funksional və estetik həllər.'
                        ],
                        'en' => [
                            'title' => 'Modern Dining Table Design',
                            'company_name' => 'Table Masters',
                            'slug' => 'modern-dining-table-design',
                            'description' => 'Make your family meals more special with modern dining table design. Functional and aesthetic solutions.'
                        ],
                        'ru' => [
                            'title' => 'Современный дизайн обеденного стола',
                            'company_name' => 'Table Masters',
                            'slug' => 'sovremennyy-dizayn-obedennogo-stola',
                            'description' => 'Сделайте ваши семейные обеды более особенными с современным дизайном обеденного стола. Функциональные и эстетичные решения.'
                        ]
                    ],
                    [
                        'az' => [
                            'title' => 'Ofis Masaları Kolleksiyası',
                            'company_name' => 'Office Solutions',
                            'slug' => 'ofis-masalari-kolleksiyasi',
                            'description' => 'Professional ofis mühiti üçün funksional və rahat masalar. Məhsuldarlığı artırır.'
                        ],
                        'en' => [
                            'title' => 'Office Tables Collection',
                            'company_name' => 'Office Solutions',
                            'slug' => 'office-tables-collection',
                            'description' => 'Functional and comfortable tables for professional office environment. Increases productivity.'
                        ],
                        'ru' => [
                            'title' => 'Коллекция офисных столов',
                            'company_name' => 'Office Solutions',
                            'slug' => 'kollektsiya-ofisnykh-stolov',
                            'description' => 'Функциональные и удобные столы для профессиональной офисной среды. Повышает продуктивность.'
                        ]
                    ]
                ]
            ],
            // Mətbəx mebelləri kateqoriyası üçün layihələr
            [
                'category_id' => 3,
                'projects' => [
                    [
                        'az' => [
                            'title' => 'Modern Mətbəx Dizaynı',
                            'company_name' => 'Kitchen Pro',
                            'slug' => 'modern-mtbx-dizayni',
                            'description' => 'Modern mətbəx mebelləri ilə funksional və gözəl mətbəx yaradın. Hər detal düşünülmüşdür.'
                        ],
                        'en' => [
                            'title' => 'Modern Kitchen Design',
                            'company_name' => 'Kitchen Pro',
                            'slug' => 'modern-kitchen-design',
                            'description' => 'Create a functional and beautiful kitchen with modern kitchen furniture. Every detail is thought out.'
                        ],
                        'ru' => [
                            'title' => 'Современный дизайн кухни',
                            'company_name' => 'Kitchen Pro',
                            'slug' => 'sovremennyy-dizayn-kukhni',
                            'description' => 'Создайте функциональную и красивую кухню с современной кухонной мебелью. Каждая деталь продумана.'
                        ]
                    ],
                    [
                        'az' => [
                            'title' => 'Lüks Mətbəx Kolleksiyası',
                            'company_name' => 'Luxury Kitchens',
                            'slug' => 'luks-mtbx-kolleksiyasi',
                            'description' => 'Lüks mətbəx mebelləri ilə evinizin ən mühüm hissəsini transformasiya edin.'
                        ],
                        'en' => [
                            'title' => 'Luxury Kitchen Collection',
                            'company_name' => 'Luxury Kitchens',
                            'slug' => 'luxury-kitchen-collection',
                            'description' => 'Transform the most important part of your home with luxury kitchen furniture.'
                        ],
                        'ru' => [
                            'title' => 'Коллекция люксовых кухонь',
                            'company_name' => 'Luxury Kitchens',
                            'slug' => 'kollektsiya-lyuksovykh-kukhon',
                            'description' => 'Преобразуйте самую важную часть вашего дома с люксовой кухонной мебелью.'
                        ]
                    ]
                ]
            ],
            // Ofis mebelleri kateqoriyası üçün layihələr
            [
                'category_id' => 4,
                'projects' => [
                    [
                        'az' => [
                            'title' => 'Modern Ofis Dizaynı',
                            'company_name' => 'Office Design Pro',
                            'slug' => 'modern-ofis-dizayni',
                            'description' => 'Modern ofis mebelləri ilə professional iş mühiti yaradın. Məhsuldarlıq və rahatlıq birlikdə.'
                        ],
                        'en' => [
                            'title' => 'Modern Office Design',
                            'company_name' => 'Office Design Pro',
                            'slug' => 'modern-office-design',
                            'description' => 'Create a professional work environment with modern office furniture. Productivity and comfort together.'
                        ],
                        'ru' => [
                            'title' => 'Современный дизайн офиса',
                            'company_name' => 'Office Design Pro',
                            'slug' => 'sovremennyy-dizayn-ofisa',
                            'description' => 'Создайте профессиональную рабочую среду с современной офисной мебелью. Продуктивность и комфорт вместе.'
                        ]
                    ],
                    [
                        'az' => [
                            'title' => 'Korporativ Ofis Həlləri',
                            'company_name' => 'Corporate Solutions',
                            'slug' => 'korporativ-ofis-hllri',
                            'description' => 'Böyük şirkətlər üçün korporativ ofis mebelləri. Funksional və professional dizayn.'
                        ],
                        'en' => [
                            'title' => 'Corporate Office Solutions',
                            'company_name' => 'Corporate Solutions',
                            'slug' => 'corporate-office-solutions',
                            'description' => 'Corporate office furniture for large companies. Functional and professional design.'
                        ],
                        'ru' => [
                            'title' => 'Корпоративные офисные решения',
                            'company_name' => 'Corporate Solutions',
                            'slug' => 'korporativnye-ofisnye-resheniya',
                            'description' => 'Корпоративная офисная мебель для крупных компаний. Функциональный и профессиональный дизайн.'
                        ]
                    ]
                ]
            ]
        ];

        foreach ($projectData as $categoryData) {
            $category = $projectCategories->find($categoryData['category_id']);
            
            if (!$category) {
                $this->command->warn("Category with ID '{$categoryData['category_id']}' not found. Skipping...");
                continue;
            }

            foreach ($categoryData['projects'] as $projectTranslations) {
                // Create project
                $project = Project::create([
                    'project_category_id' => $category->id,
                    'status' => true,
                    'order' => Project::max('order') + 1
                ]);

                // Create translations
                foreach ($languages as $language) {
                    $langCode = $language->code;
                    if (isset($projectTranslations[$langCode])) {
                        ProjectTranslate::create([
                            'project_id' => $project->id,
                            'language_id' => $language->id,
                            'title' => $projectTranslations[$langCode]['title'],
                            'company_name' => $projectTranslations[$langCode]['company_name'],
                            'slug' => $projectTranslations[$langCode]['slug'],
                            'description' => $projectTranslations[$langCode]['description']
                        ]);
                    }
                }

                // Create images with Lorem Picsum
                $imageCount = rand(3, 6); // 3-6 şəkil
                for ($i = 0; $i < $imageCount; $i++) {
                    $width = rand(800, 1200);
                    $height = rand(600, 900);
                    $imageUrl = "https://picsum.photos/{$width}/{$height}?random=" . rand(1, 1000);
                    
                    ProjectImage::create([
                        'project_id' => $project->id,
                        'image' => $imageUrl,
                        'is_main_image' => $i === 0, // İlk şəkil əsas şəkil olsun
                        'order' => $i + 1,
                        'status' => true
                    ]);
                }

                $this->command->info("Created project: {$projectTranslations['az']['title']} for category ID: {$categoryData['category_id']}");
            }
        }

        $this->command->info('Project seeder completed successfully!');
    }
}
