<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeoPage;
use App\Models\SeoPageTranslate;
use App\Models\Language;
use Illuminate\Support\Str;

class SeoPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get languages
        $languages = Language::all();
        
        // SEO Pages data based on categories
        $seoPages = [
            [
                'category_id' => 5, // Oturma otağı mebeli
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Oturma Otağı Mebeli - Keyfiyyətli və Müasir Dizaynlar',
                        'slug' => 'oturma-otagi-mebeli',
                        'page_footer' => '<p>Oturma otağınız üçün ən keyfiyyətli mebelləri kəşf edin. Müasir dizaynlar və əlverişli qiymətlərlə.</p>',
                        'meta_title' => 'Oturma Otağı Mebeli | Keyfiyyətli Divan və Kreslo',
                        'meta_description' => 'Oturma otağı mebeli kolleksiyamızda müasir divanlar, kreslolar və digər mebelləri tapın. Yüksək keyfiyyət və əlverişli qiymətlər.',
                        'meta_keywords' => 'oturma otağı mebeli, divan, kreslo, oturma dəsti, müasir mebel'
                    ],
                    'en' => [
                        'title' => 'Living Room Furniture - Quality and Modern Designs',
                        'slug' => 'living-room-furniture',
                        'page_footer' => '<p>Discover the highest quality furniture for your living room. Modern designs and affordable prices.</p>',
                        'meta_title' => 'Living Room Furniture | Quality Sofas and Armchairs',
                        'meta_description' => 'Find modern sofas, armchairs and other furniture in our living room furniture collection. High quality and affordable prices.',
                        'meta_keywords' => 'living room furniture, sofa, armchair, living room set, modern furniture'
                    ],
                    'ru' => [
                        'title' => 'Мебель для Гостиной - Качественные и Современные Дизайны',
                        'slug' => 'mebel-dlya-gostinoy',
                        'page_footer' => '<p>Откройте для себя самую качественную мебель для вашей гостиной. Современные дизайны и доступные цены.</p>',
                        'meta_title' => 'Мебель для Гостиной | Качественные Диваны и Кресла',
                        'meta_description' => 'Найдите современные диваны, кресла и другую мебель в нашей коллекции мебели для гостиной. Высокое качество и доступные цены.',
                        'meta_keywords' => 'мебель для гостиной, диван, кресло, гостиный гарнитур, современная мебель'
                    ]
                ]
            ],
            [
                'category_id' => 6, // Yataq mebeli
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Yataq Mebeli - Rahatlıq və Keyfiyyət Bir Arada',
                        'slug' => 'yataq-mebeli',
                        'page_footer' => '<p>Yataq otağınız üçün ən yaxşı mebelləri seçin. Rahat yataqlar və funksional şkaflar.</p>',
                        'meta_title' => 'Yataq Mebeli | Yataq Dəsti və Şkaflar',
                        'meta_description' => 'Yataq mebeli kolleksiyamızda rahat yataqlar, şkaflar və digər yataq otağı mebellərini tapın.',
                        'meta_keywords' => 'yataq mebeli, yataq, şkaf, yataq dəsti, yataq otağı mebeli'
                    ],
                    'en' => [
                        'title' => 'Bedroom Furniture - Comfort and Quality Together',
                        'slug' => 'bedroom-furniture',
                        'page_footer' => '<p>Choose the best furniture for your bedroom. Comfortable beds and functional wardrobes.</p>',
                        'meta_title' => 'Bedroom Furniture | Bedroom Sets and Wardrobes',
                        'meta_description' => 'Find comfortable beds, wardrobes and other bedroom furniture in our bedroom furniture collection.',
                        'meta_keywords' => 'bedroom furniture, bed, wardrobe, bedroom set, bedroom furniture'
                    ],
                    'ru' => [
                        'title' => 'Мебель для Спальни - Комфорт и Качество Вместе',
                        'slug' => 'mebel-dlya-spalni',
                        'page_footer' => '<p>Выберите лучшую мебель для вашей спальни. Удобные кровати и функциональные шкафы.</p>',
                        'meta_title' => 'Мебель для Спальни | Спальные Гарнитуры и Шкафы',
                        'meta_description' => 'Найдите удобные кровати, шкафы и другую мебель для спальни в нашей коллекции мебели для спальни.',
                        'meta_keywords' => 'мебель для спальни, кровать, шкаф, спальный гарнитур, мебель для спальни'
                    ]
                ]
            ],
            [
                'category_id' => 7, // Mətbəx mebeli
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Mətbəx Mebeli - Funksional və Estetik Həllər',
                        'slug' => 'metbex-mebeli',
                        'page_footer' => '<p>Mətbəxiniz üçün ən funksional və gözəl mebelləri kəşf edin. Müasir mətbəx həlləri.</p>',
                        'meta_title' => 'Mətbəx Mebeli | Mətbəx Dəstləri və Aksesuarlar',
                        'meta_description' => 'Mətbəx mebeli kolleksiyamızda funksional mətbəx dəstləri və aksesuarları tapın.',
                        'meta_keywords' => 'mətbəx mebeli, mətbəx dəsti, mətbəx şkafı, mətbəx aksesuarları'
                    ],
                    'en' => [
                        'title' => 'Kitchen Furniture - Functional and Aesthetic Solutions',
                        'slug' => 'kitchen-furniture',
                        'page_footer' => '<p>Discover the most functional and beautiful furniture for your kitchen. Modern kitchen solutions.</p>',
                        'meta_title' => 'Kitchen Furniture | Kitchen Sets and Accessories',
                        'meta_description' => 'Find functional kitchen sets and accessories in our kitchen furniture collection.',
                        'meta_keywords' => 'kitchen furniture, kitchen set, kitchen cabinet, kitchen accessories'
                    ],
                    'ru' => [
                        'title' => 'Кухонная Мебель - Функциональные и Эстетичные Решения',
                        'slug' => 'kuhonnaya-mebel',
                        'page_footer' => '<p>Откройте для себя самую функциональную и красивую мебель для вашей кухни. Современные кухонные решения.</p>',
                        'meta_title' => 'Кухонная Мебель | Кухонные Гарнитуры и Аксессуары',
                        'meta_description' => 'Найдите функциональные кухонные гарнитуры и аксессуары в нашей коллекции кухонной мебели.',
                        'meta_keywords' => 'кухонная мебель, кухонный гарнитур, кухонный шкаф, кухонные аксессуары'
                    ]
                ]
            ],
            [
                'category_id' => 8, // Ofis mebeli
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Ofis Mebeli - Peşəkar İş Mühiti Üçün',
                        'slug' => 'ofis-mebeli',
                        'page_footer' => '<p>Ofis məkanınız üçün peşəkar mebelləri seçin. Ergonomik və funksional dizaynlar.</p>',
                        'meta_title' => 'Ofis Mebeli | Ofis Masaları və Kreslolar',
                        'meta_description' => 'Ofis mebeli kolleksiyamızda peşəkar ofis masaları, kreslolar və digər ofis mebellərini tapın.',
                        'meta_keywords' => 'ofis mebeli, ofis masası, ofis kreslosu, iş masası, ofis şkafı'
                    ],
                    'en' => [
                        'title' => 'Office Furniture - For Professional Work Environment',
                        'slug' => 'office-furniture',
                        'page_footer' => '<p>Choose professional furniture for your office space. Ergonomic and functional designs.</p>',
                        'meta_title' => 'Office Furniture | Office Desks and Chairs',
                        'meta_description' => 'Find professional office desks, chairs and other office furniture in our office furniture collection.',
                        'meta_keywords' => 'office furniture, office desk, office chair, work desk, office cabinet'
                    ],
                    'ru' => [
                        'title' => 'Офисная Мебель - Для Профессиональной Рабочей Среды',
                        'slug' => 'ofisnaya-mebel',
                        'page_footer' => '<p>Выберите профессиональную мебель для вашего офисного пространства. Эргономичные и функциональные дизайны.</p>',
                        'meta_title' => 'Офисная Мебель | Офисные Столы и Кресла',
                        'meta_description' => 'Найдите профессиональные офисные столы, кресла и другую офисную мебель в нашей коллекции офисной мебели.',
                        'meta_keywords' => 'офисная мебель, офисный стол, офисное кресло, рабочий стол, офисный шкаф'
                    ]
                ]
            ],
            [
                'category_id' => 9, // Uşaq mebeli
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Uşaq Mebeli - Təhlükəsiz və Rəngarəng Dizaynlar',
                        'slug' => 'usaq-mebeli',
                        'page_footer' => '<p>Uşaqlarınız üçün təhlükəsiz və rəngarəng mebelləri kəşf edin. Keyfiyyətli uşaq otağı mebeli.</p>',
                        'meta_title' => 'Uşaq Mebeli | Uşaq Yatağı və Oyun Mebeli',
                        'meta_description' => 'Uşaq mebeli kolleksiyamızda təhlükəsiz uşaq yataqları, oyun mebelləri və digər uşaq otağı mebellərini tapın.',
                        'meta_keywords' => 'uşaq mebeli, uşaq yatağı, uşaq masası, oyun mebeli, uşaq otağı'
                    ],
                    'en' => [
                        'title' => 'Kids Furniture - Safe and Colorful Designs',
                        'slug' => 'kids-furniture',
                        'page_footer' => '<p>Discover safe and colorful furniture for your children. Quality children\'s room furniture.</p>',
                        'meta_title' => 'Kids Furniture | Children\'s Beds and Play Furniture',
                        'meta_description' => 'Find safe children\'s beds, play furniture and other children\'s room furniture in our kids furniture collection.',
                        'meta_keywords' => 'kids furniture, children bed, kids desk, play furniture, children room'
                    ],
                    'ru' => [
                        'title' => 'Детская Мебель - Безопасные и Красочные Дизайны',
                        'slug' => 'detskaya-mebel',
                        'page_footer' => '<p>Откройте для себя безопасную и красочную мебель для ваших детей. Качественная мебель для детской комнаты.</p>',
                        'meta_title' => 'Детская Мебель | Детские Кровати и Игровая Мебель',
                        'meta_description' => 'Найдите безопасные детские кровати, игровую мебель и другую мебель для детской комнаты в нашей коллекции детской мебели.',
                        'meta_keywords' => 'детская мебель, детская кровать, детский стол, игровая мебель, детская комната'
                    ]
                ]
            ],
            [
                'category_id' => 10, // Qonaq otağı mebeli
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Qonaq Otağı Mebeli - Qonaqları Qarşılamaq Üçün',
                        'slug' => 'qonaq-otagi-mebeli',
                        'page_footer' => '<p>Qonaq otağınız üçün zərif və rahat mebelləri seçin. Qonaqları qarşılamaq üçün ideal həllər.</p>',
                        'meta_title' => 'Qonaq Otağı Mebeli | Qonaq Dəsti və Aksesuarlar',
                        'meta_description' => 'Qonaq otağı mebeli kolleksiyamızda zərif qonaq dəstləri və aksesuarları tapın.',
                        'meta_keywords' => 'qonaq otağı mebeli, qonaq dəsti, qonaq masası, qonaq kreslosu'
                    ],
                    'en' => [
                        'title' => 'Guest Room Furniture - For Welcoming Guests',
                        'slug' => 'guest-room-furniture',
                        'page_footer' => '<p>Choose elegant and comfortable furniture for your guest room. Ideal solutions for welcoming guests.</p>',
                        'meta_title' => 'Guest Room Furniture | Guest Sets and Accessories',
                        'meta_description' => 'Find elegant guest sets and accessories in our guest room furniture collection.',
                        'meta_keywords' => 'guest room furniture, guest set, guest table, guest chair'
                    ],
                    'ru' => [
                        'title' => 'Мебель для Гостевой Комнаты - Для Приема Гостей',
                        'slug' => 'mebel-dlya-gostevoy-komnaty',
                        'page_footer' => '<p>Выберите элегантную и удобную мебель для вашей гостевой комнаты. Идеальные решения для приема гостей.</p>',
                        'meta_title' => 'Мебель для Гостевой Комнаты | Гостевые Гарнитуры и Аксессуары',
                        'meta_description' => 'Найдите элегантные гостевые гарнитуры и аксессуары в нашей коллекции мебели для гостевой комнаты.',
                        'meta_keywords' => 'мебель для гостевой комнаты, гостевой гарнитур, гостевой стол, гостевое кресло'
                    ]
                ]
            ],
            [
                'category_id' => 11, // Bağça mebeli
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Bağça Mebeli - Açıq Hava Rahatlığı',
                        'slug' => 'bagca-mebeli',
                        'page_footer' => '<p>Bağçanız və terrasınız üçün hava şəraitinə davamlı mebelləri kəşf edin. Açıq hava rahatlığı.</p>',
                        'meta_title' => 'Bağça Mebeli | Terras və Bağça Dəstləri',
                        'meta_description' => 'Bağça mebeli kolleksiyamızda hava şəraitinə davamlı terras və bağça dəstlərini tapın.',
                        'meta_keywords' => 'bağça mebeli, terras mebeli, açıq hava mebeli, bağça dəsti'
                    ],
                    'en' => [
                        'title' => 'Garden Furniture - Outdoor Comfort',
                        'slug' => 'garden-furniture',
                        'page_footer' => '<p>Discover weather-resistant furniture for your garden and terrace. Outdoor comfort.</p>',
                        'meta_title' => 'Garden Furniture | Terrace and Garden Sets',
                        'meta_description' => 'Find weather-resistant terrace and garden sets in our garden furniture collection.',
                        'meta_keywords' => 'garden furniture, terrace furniture, outdoor furniture, garden set'
                    ],
                    'ru' => [
                        'title' => 'Садовая Мебель - Комфорт на Открытом Воздухе',
                        'slug' => 'sadovaya-mebel',
                        'page_footer' => '<p>Откройте для себя устойчивую к погодным условиям мебель для вашего сада и террасы. Комфорт на открытом воздухе.</p>',
                        'meta_title' => 'Садовая Мебель | Террасные и Садовые Гарнитуры',
                        'meta_description' => 'Найдите устойчивые к погодным условиям террасные и садовые гарнитуры в нашей коллекции садовой мебели.',
                        'meta_keywords' => 'садовая мебель, террасная мебель, уличная мебель, садовый гарнитур'
                    ]
                ]
            ]
        ];

        foreach ($seoPages as $seoPageData) {
            // Create SEO Page
            $seoPage = SeoPage::create([
                'category_id' => $seoPageData['category_id'],
                'status' => $seoPageData['status']
            ]);

            // Create translations for each language
            foreach ($languages as $language) {
                $langCode = $language->lang_code;
                
                if (isset($seoPageData['translations'][$langCode])) {
                    $translation = $seoPageData['translations'][$langCode];
                    
                    SeoPageTranslate::create([
                        'seo_page_id' => $seoPage->id,
                        'language_id' => $language->id,
                        'slug' => $translation['slug'],
                        'title' => $translation['title'],
                        'page_footer' => $translation['page_footer'],
                        'meta_title' => $translation['meta_title'],
                        'meta_description' => $translation['meta_description'],
                        'meta_keywords' => $translation['meta_keywords']
                    ]);
                }
            }
        }
    }
}
