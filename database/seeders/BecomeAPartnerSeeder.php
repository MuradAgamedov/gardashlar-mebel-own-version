<?php

namespace Database\Seeders;

use App\Models\BecomeAPartner;
use App\Models\BecomeAPartnerAdvantage;
use App\Models\BecomeAPartnerTranslate;
use App\Models\BecomeAPartnerAdvantageTranslate;
use Illuminate\Database\Seeder;

class BecomeAPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Become A Partner record
        $becomeAPartner = BecomeAPartner::create([
            'content' => 'Default content'
        ]);

        // Create translations for Become A Partner
        $translations = [
            [
                'language_id' => 3, // Azerbaijani
                'content' => 'Bizim Tərəfdaşımız Olun və Uğura Çatın! Gardashlar Mebel ilə tərəfdaşlıq edərək biznesinizi böyüdün. Keyfiyyətli məhsullar, peşəkar dəstək və sürətli çatdırılma xidmətlərimizlə sizə uğur qazandırırıq.'
            ],
            [
                'language_id' => 1, // English
                'content' => 'Become Our Partner and Achieve Success! Grow your business by partnering with Gardashlar Mebel. We help you succeed with quality products, professional support, and fast delivery services.'
            ]
        ];

        foreach ($translations as $translation) {
            BecomeAPartnerTranslate::create([
                'become_a_partner_id' => $becomeAPartner->id,
                'language_id' => $translation['language_id'],
                'content' => $translation['content']
            ]);
        }

        // Create Become A Partner Advantages
        $advantages = [
            [
                'order' => 1,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 3, // Azerbaijani
                        'title' => 'Keyfiyyətli Məhsullar',
                        'description' => 'Ən yüksək keyfiyyət standartlarında istehsal edilən məhsullarımızla müştərilərinizi məmnun edin.',
                        'icon_alt_text' => 'Keyfiyyətli məhsullar ikonu'
                    ],
                    [
                        'language_id' => 1, // English
                        'title' => 'Quality Products',
                        'description' => 'Satisfy your customers with our products manufactured to the highest quality standards.',
                        'icon_alt_text' => 'Quality products icon'
                    ]
                ]
            ],
            [
                'order' => 2,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 3, // Azerbaijani
                        'title' => '24/7 Dəstək',
                        'description' => 'Həftənin 7 günü, 24 saat peşəkar dəstək xidmətimizlə sizə kömək edirik.',
                        'icon_alt_text' => '24/7 dəstək ikonu'
                    ],
                    [
                        'language_id' => 1, // English
                        'title' => '24/7 Support',
                        'description' => 'We help you with our professional support service 24 hours a day, 7 days a week.',
                        'icon_alt_text' => '24/7 support icon'
                    ]
                ]
            ],
            [
                'order' => 3,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 3, // Azerbaijani
                        'title' => 'Sürətli Çatdırılma',
                        'description' => 'Sifarişlərinizi ən qısa müddətdə və etibarlı şəkildə çatdırırıq.',
                        'icon_alt_text' => 'Sürətli çatdırılma ikonu'
                    ],
                    [
                        'language_id' => 1, // English
                        'title' => 'Fast Delivery',
                        'description' => 'We deliver your orders in the shortest time and reliably.',
                        'icon_alt_text' => 'Fast delivery icon'
                    ]
                ]
            ],
            [
                'order' => 4,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 3, // Azerbaijani
                        'title' => 'Rəqabətli Qiymətlər',
                        'description' => 'Ən yaxşı keyfiyyəti ən sərfəli qiymətlərlə təqdim edirik.',
                        'icon_alt_text' => 'Rəqabətli qiymətlər ikonu'
                    ],
                    [
                        'language_id' => 1, // English
                        'title' => 'Competitive Prices',
                        'description' => 'We offer the best quality at the most affordable prices.',
                        'icon_alt_text' => 'Competitive prices icon'
                    ]
                ]
            ],
            [
                'order' => 5,
                'status' => true,
                'translations' => [
                    [
                        'language_id' => 3, // Azerbaijani
                        'title' => 'Peşəkar Komanda',
                        'description' => 'Təcrübəli və peşəkar komandamızla sizin uğurunuz üçün çalışırıq.',
                        'icon_alt_text' => 'Peşəkar komanda ikonu'
                    ],
                    [
                        'language_id' => 1, // English
                        'title' => 'Professional Team',
                        'description' => 'We work for your success with our experienced and professional team.',
                        'icon_alt_text' => 'Professional team icon'
                    ]
                ]
            ]
        ];

        foreach ($advantages as $advantageData) {
            $translations = $advantageData['translations'];
            unset($advantageData['translations']);
            
            $advantage = BecomeAPartnerAdvantage::create($advantageData);

            foreach ($translations as $translation) {
                BecomeAPartnerAdvantageTranslate::create([
                    'become_a_partner_advantage_id' => $advantage->id,
                    'language_id' => $translation['language_id'],
                    'title' => $translation['title'],
                    'description' => $translation['description'],
                    'icon_alt_text' => $translation['icon_alt_text']
                ]);
            }
        }

        $this->command->info('Become A Partner and Advantages seeded successfully!');
    }
}
