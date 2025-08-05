<?php

namespace Database\Seeders;

use App\Models\BecomeAInvestor;
use App\Models\BecomeAInvestorAdvantage;
use App\Models\BecomeAInvestorTranslate;
use App\Models\BecomeAInvestorAdvantageTranslate;
use Illuminate\Database\Seeder;

class BecomeAInvestorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Become A Investor record
        $becomeAInvestor = BecomeAInvestor::create([]);

        // Create translations for Become A Investor
        $translations = [
            [
                'language_id' => 3, // Azerbaijani
                'content' => 'Bizim İnvestorumuz Olun və Gəlirinizi Artırın! Gardashlar Mebel-ə investisiya edərək gəlirinizi artırın. Yüksək gəlir, etibarlı investisiya və peşəkar idarəetmə ilə sizin uğurunuz üçün çalışırıq.'
            ],
            [
                'language_id' => 1, // English
                'content' => 'Become Our Investor and Increase Your Income! Increase your income by investing in Gardashlar Mebel. We work for your success with high returns, reliable investment, and professional management.'
            ]
        ];

        foreach ($translations as $translation) {
            BecomeAInvestorTranslate::create([
                'become_a_investor_id' => $becomeAInvestor->id,
                'language_id' => $translation['language_id'],
                'content' => $translation['content']
            ]);
        }

        // Create Become A Investor Advantages
        $advantages = [
            [
                'order' => 1,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 3, // Azerbaijani
                        'title' => 'Yüksək Gəlir',
                        'description' => 'İnvestisiyanızdan yüksək və dayanıqlı gəlir əldə edin.',
                        'icon_alt_text' => 'Yüksək gəlir ikonu'
                    ],
                    [
                        'language_id' => 1, // English
                        'title' => 'High Returns',
                        'description' => 'Get high and sustainable returns from your investment.',
                        'icon_alt_text' => 'High returns icon'
                    ]
                ]
            ],
            [
                'order' => 2,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 3, // Azerbaijani
                        'title' => 'Etibarlı İnvestisiya',
                        'description' => 'Uzun illik təcrübə və etibarlı biznes modeli ilə təminatlı olun.',
                        'icon_alt_text' => 'Etibarlı investisiya ikonu'
                    ],
                    [
                        'language_id' => 1, // English
                        'title' => 'Reliable Investment',
                        'description' => 'Be confident with years of experience and a reliable business model.',
                        'icon_alt_text' => 'Reliable investment icon'
                    ]
                ]
            ],
            [
                'order' => 3,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 3, // Azerbaijani
                        'title' => 'Peşəkar İdarəetmə',
                        'description' => 'Təcrübəli komandamızla investisiyanızı peşəkar şəkildə idarə edirik.',
                        'icon_alt_text' => 'Peşəkar idarəetmə ikonu'
                    ],
                    [
                        'language_id' => 1, // English
                        'title' => 'Professional Management',
                        'description' => 'We professionally manage your investment with our experienced team.',
                        'icon_alt_text' => 'Professional management icon'
                    ]
                ]
            ],
            [
                'order' => 4,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 3, // Azerbaijani
                        'title' => 'Şəffaf Hesabat',
                        'description' => 'İnvestisiyanızın vəziyyəti haqqında müntəzəm və şəffaf hesabatlar alın.',
                        'icon_alt_text' => 'Şəffaf hesabat ikonu'
                    ],
                    [
                        'language_id' => 1, // English
                        'title' => 'Transparent Reports',
                        'description' => 'Receive regular and transparent reports about your investment status.',
                        'icon_alt_text' => 'Transparent reports icon'
                    ]
                ]
            ],
            [
                'order' => 5,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 3, // Azerbaijani
                        'title' => 'Sürətli Ödəniş',
                        'description' => 'Gəlirlərinizi vaxtında və sürətli şəkildə alın.',
                        'icon_alt_text' => 'Sürətli ödəniş ikonu'
                    ],
                    [
                        'language_id' => 1, // English
                        'title' => 'Fast Payment',
                        'description' => 'Receive your returns on time and quickly.',
                        'icon_alt_text' => 'Fast payment icon'
                    ]
                ]
            ]
        ];

        foreach ($advantages as $advantageData) {
            $translations = $advantageData['translations'];
            unset($advantageData['translations']);
            
            $advantage = BecomeAInvestorAdvantage::create($advantageData);

            foreach ($translations as $translation) {
                BecomeAInvestorAdvantageTranslate::create([
                    'become_a_investor_advantage_id' => $advantage->id,
                    'language_id' => $translation['language_id'],
                    'title' => $translation['title'],
                    'description' => $translation['description'],
                    'icon_alt_text' => $translation['icon_alt_text']
                ]);
            }
        }

        $this->command->info('Become A Investor and Advantages seeded successfully!');
    }
}
