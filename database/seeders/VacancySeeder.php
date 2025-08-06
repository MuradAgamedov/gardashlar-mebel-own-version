<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vacancy;
use App\Models\VacancyTranslate;
use App\Models\VacancyRequirement;
use App\Models\VacancyRequirementTranslate;
use App\Models\Language;
use Carbon\Carbon;

class VacancySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get languages
        $languages = Language::all();
        
        if ($languages->isEmpty()) {
            $this->command->error('No languages found. Please run LanguageSeeder first.');
            return;
        }

        // Sample vacancy data
        $vacancies = [
            [
                'email' => 'hr@gardashlar.az',
                'address' => 'Bakı şəhəri, Nərimanov rayonu, Atatürk prospekti 123',
                'last_apply_date' => Carbon::now()->addMonths(2),
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Mühasib vəzifəsi',
                        'slug' => 'muhasib-vezifesi',
                        'position' => 'Mühasib',
                        'experience' => '3-5 il təcrübə',
                        'work_schedule' => 'Bazar ertəsi - Cümə, 09:00-18:00',
                        'seo_title' => 'Mühasib vəzifəsi - Gardashlar',
                        'seo_keywords' => 'mühasib, iş, vəzifə, bakı, gardashlar',
                        'seo_description' => 'Təcrübəli mühasib axtarılır. 3-5 il təcrübə tələb olunur.'
                    ],
                    'en' => [
                        'title' => 'Accountant Position',
                        'slug' => 'accountant-position',
                        'position' => 'Accountant',
                        'experience' => '3-5 years experience',
                        'work_schedule' => 'Monday - Friday, 09:00-18:00',
                        'seo_title' => 'Accountant Position - Gardashlar',
                        'seo_keywords' => 'accountant, job, position, baku, gardashlar',
                        'seo_description' => 'Experienced accountant needed. 3-5 years experience required.'
                    ],
                    'ru' => [
                        'title' => 'Должность бухгалтера',
                        'slug' => 'dolzhnost-bukhgaltera',
                        'position' => 'Бухгалтер',
                        'experience' => '3-5 лет опыта',
                        'work_schedule' => 'Понедельник - Пятница, 09:00-18:00',
                        'seo_title' => 'Должность бухгалтера - Gardashlar',
                        'seo_keywords' => 'бухгалтер, работа, должность, баку, gardashlar',
                        'seo_description' => 'Требуется опытный бухгалтер. Требуется 3-5 лет опыта.'
                    ]
                ],
                'requirements' => [
                    'az' => [
                        'title' => 'Təhsil tələbləri',
                        'description' => 'Ali təhsil (iqtisadiyyat, maliyyə və ya mühasibatlıq sahəsində)'
                    ],
                    'en' => [
                        'title' => 'Education Requirements',
                        'description' => 'Higher education (in economics, finance or accounting)'
                    ],
                    'ru' => [
                        'title' => 'Требования к образованию',
                        'description' => 'Высшее образование (в области экономики, финансов или бухгалтерского учета)'
                    ]
                ]
            ],
            [
                'email' => 'hr@gardashlar.az',
                'address' => 'Bakı şəhəri, Yasamal rayonu, Təbriz küçəsi 45',
                'last_apply_date' => Carbon::now()->addMonths(1),
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Marketinq meneceri',
                        'slug' => 'marketinq-meneceri',
                        'position' => 'Marketinq meneceri',
                        'experience' => '2-4 il təcrübə',
                        'work_schedule' => 'Bazar ertəsi - Cümə, 10:00-19:00',
                        'seo_title' => 'Marketinq meneceri - Gardashlar',
                        'seo_keywords' => 'marketinq, menecer, iş, bakı, gardashlar',
                        'seo_description' => 'Yaradıcı marketinq meneceri axtarılır. 2-4 il təcrübə tələb olunur.'
                    ],
                    'en' => [
                        'title' => 'Marketing Manager',
                        'slug' => 'marketing-manager',
                        'position' => 'Marketing Manager',
                        'experience' => '2-4 years experience',
                        'work_schedule' => 'Monday - Friday, 10:00-19:00',
                        'seo_title' => 'Marketing Manager - Gardashlar',
                        'seo_keywords' => 'marketing, manager, job, baku, gardashlar',
                        'seo_description' => 'Creative marketing manager needed. 2-4 years experience required.'
                    ],
                    'ru' => [
                        'title' => 'Менеджер по маркетингу',
                        'slug' => 'menedzher-po-marketingu',
                        'position' => 'Менеджер по маркетингу',
                        'experience' => '2-4 года опыта',
                        'work_schedule' => 'Понедельник - Пятница, 10:00-19:00',
                        'seo_title' => 'Менеджер по маркетингу - Gardashlar',
                        'seo_keywords' => 'маркетинг, менеджер, работа, баку, gardashlar',
                        'seo_description' => 'Требуется креативный менеджер по маркетингу. Требуется 2-4 года опыта.'
                    ]
                ],
                'requirements' => [
                    'az' => [
                        'title' => 'Təcrübə tələbləri',
                        'description' => 'Digital marketinq sahəsində təcrübə, sosial media idarəetməsi'
                    ],
                    'en' => [
                        'title' => 'Experience Requirements',
                        'description' => 'Experience in digital marketing, social media management'
                    ],
                    'ru' => [
                        'title' => 'Требования к опыту',
                        'description' => 'Опыт в цифровом маркетинге, управление социальными сетями'
                    ]
                ]
            ],
            [
                'email' => 'hr@gardashlar.az',
                'address' => 'Bakı şəhəri, Binəqədi rayonu, Əhməd Rəcəbli küçəsi 78',
                'last_apply_date' => Carbon::now()->addWeeks(3),
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Proqram tərtibatçısı',
                        'slug' => 'proqram-tertibatcisi',
                        'position' => 'Proqram tərtibatçısı',
                        'experience' => '1-3 il təcrübə',
                        'work_schedule' => 'Bazar ertəsi - Cümə, 09:00-18:00',
                        'seo_title' => 'Proqram tərtibatçısı - Gardashlar',
                        'seo_keywords' => 'proqram, tərtibatçı, developer, iş, bakı, gardashlar',
                        'seo_description' => 'Gənc və enerjili proqram tərtibatçısı axtarılır. 1-3 il təcrübə tələb olunur.'
                    ],
                    'en' => [
                        'title' => 'Software Developer',
                        'slug' => 'software-developer',
                        'position' => 'Software Developer',
                        'experience' => '1-3 years experience',
                        'work_schedule' => 'Monday - Friday, 09:00-18:00',
                        'seo_title' => 'Software Developer - Gardashlar',
                        'seo_keywords' => 'software, developer, programming, job, baku, gardashlar',
                        'seo_description' => 'Young and energetic software developer needed. 1-3 years experience required.'
                    ],
                    'ru' => [
                        'title' => 'Разработчик программного обеспечения',
                        'slug' => 'razrabotchik-programmnogo-obespecheniya',
                        'position' => 'Разработчик ПО',
                        'experience' => '1-3 года опыта',
                        'work_schedule' => 'Понедельник - Пятница, 09:00-18:00',
                        'seo_title' => 'Разработчик ПО - Gardashlar',
                        'seo_keywords' => 'программист, разработчик, программирование, работа, баку, gardashlar',
                        'seo_description' => 'Требуется молодой и энергичный разработчик ПО. Требуется 1-3 года опыта.'
                    ]
                ],
                'requirements' => [
                    'az' => [
                        'title' => 'Texniki tələblər',
                        'description' => 'PHP, Laravel, JavaScript, MySQL bilikləri tələb olunur'
                    ],
                    'en' => [
                        'title' => 'Technical Requirements',
                        'description' => 'Knowledge of PHP, Laravel, JavaScript, MySQL required'
                    ],
                    'ru' => [
                        'title' => 'Технические требования',
                        'description' => 'Требуются знания PHP, Laravel, JavaScript, MySQL'
                    ]
                ]
            ]
        ];

        foreach ($vacancies as $vacancyData) {
            // Create vacancy
            $vacancy = Vacancy::create([
                'email' => $vacancyData['email'],
                'address' => $vacancyData['address'],
                'last_apply_date' => $vacancyData['last_apply_date'],
                'status' => $vacancyData['status'],
            ]);

            // Create translations for each language
            foreach ($languages as $language) {
                $langCode = $language->lang_code;
                
                if (isset($vacancyData['translations'][$langCode])) {
                    $translationData = $vacancyData['translations'][$langCode];
                    
                    VacancyTranslate::create([
                        'vacancy_id' => $vacancy->id,
                        'language_id' => $language->id,
                        'title' => $translationData['title'],
                        'slug' => $translationData['slug'],
                        'position' => $translationData['position'],
                        'experience' => $translationData['experience'],
                        'work_schedule' => $translationData['work_schedule'],
                        'seo_title' => $translationData['seo_title'],
                        'seo_keywords' => $translationData['seo_keywords'],
                        'seo_description' => $translationData['seo_description'],
                    ]);
                }
            }

            // Create requirements if they exist
            if (isset($vacancyData['requirements'])) {
                $requirement = VacancyRequirement::create([
                    'vacancy_id' => $vacancy->id,
                    'status' => true,
                ]);

                // Create requirement translations
                foreach ($languages as $language) {
                    $langCode = $language->lang_code;
                    
                    if (isset($vacancyData['requirements'][$langCode])) {
                        $reqData = $vacancyData['requirements'][$langCode];
                        
                        VacancyRequirementTranslate::create([
                            'vacancy_requirement_id' => $requirement->id,
                            'language_id' => $language->id,
                            'title' => $reqData['title'],
                            'description' => $reqData['description'],
                        ]);
                    }
                }
            }
        }

        $this->command->info('Vacancy seeder completed successfully!');
        $this->command->info('Created ' . count($vacancies) . ' vacancies with translations and requirements.');
    }
}
