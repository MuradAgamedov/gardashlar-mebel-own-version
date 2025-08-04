<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IndividualProjectOrder;
use App\Models\IndividualProjectOrderTranslate;
use App\Models\Language;

class IndividualProjectOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get languages
        $azLanguage = Language::where('lang_code', 'az')->first();
        $enLanguage = Language::where('lang_code', 'en')->first();

        if (!$azLanguage || !$enLanguage) {
            $this->command->error('Languages not found. Please run LanguageSeeder first.');
            return;
        }

        // Sample data for individual project orders
        $orders = [
            [
                'order' => 1,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Layihə Təklifi Göndərin',
                        'description' => 'Öz layihənizi təsvir edin və biz sizinlə əlaqə saxlayacağıq'
                    ],
                    'en' => [
                        'title' => 'Submit Project Proposal',
                        'description' => 'Describe your project and we will contact you'
                    ]
                ]
            ],
            [
                'order' => 2,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Layihə Analizi',
                        'description' => 'Layihənizi analiz edirik və texniki təklif hazırlayırıq'
                    ],
                    'en' => [
                        'title' => 'Project Analysis',
                        'description' => 'We analyze your project and prepare technical proposal'
                    ]
                ]
            ],
            [
                'order' => 3,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Qiymət Təklifi',
                        'description' => 'Detallı qiymət təklifi və iş planı təqdim edirik'
                    ],
                    'en' => [
                        'title' => 'Price Quote',
                        'description' => 'We present detailed price quote and work plan'
                    ]
                ]
            ],
            [
                'order' => 4,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Müqavilə İmzalama',
                        'description' => 'Razılıq əldə edildikdən sonra müqavilə imzalanır'
                    ],
                    'en' => [
                        'title' => 'Contract Signing',
                        'description' => 'Contract is signed after agreement is reached'
                    ]
                ]
            ],
            [
                'order' => 5,
                'status' => true,
                'translations' => [
                    'az' => [
                        'title' => 'Layihə İcra',
                        'description' => 'Layihə planına uyğun olaraq işlər başlayır'
                    ],
                    'en' => [
                        'title' => 'Project Execution',
                        'description' => 'Work begins according to project plan'
                    ]
                ]
            ]
        ];

        foreach ($orders as $orderData) {
            // Create individual project order
            $order = IndividualProjectOrder::create([
                'order' => $orderData['order'],
                'status' => $orderData['status']
            ]);

            // Create translations
            foreach ($orderData['translations'] as $langCode => $translation) {
                $language = $langCode === 'az' ? $azLanguage : $enLanguage;
                
                IndividualProjectOrderTranslate::create([
                    'individual_project_order_id' => $order->id,
                    'language_id' => $language->id,
                    'title' => $translation['title'],
                    'description' => $translation['description']
                ]);
            }
        }

        $this->command->info('Individual Project Orders seeded successfully!');
    }
} 