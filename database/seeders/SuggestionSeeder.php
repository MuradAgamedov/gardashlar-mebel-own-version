<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Suggestion;
use App\Models\SuggestionTranslate;
use App\Models\Language;

class SuggestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::all();

        // Sample suggestions data
        $suggestions = [
            [
                'image' => 'suggestions/sample-suggestion-1.jpg',
                'pdf' => 'suggestions/pdfs/sample-pdf-1.pdf',
                'status' => true,
                'order' => 1,
                'translations' => [
                    'az' => [
                        'title' => 'Məhsul Tövsiyyəsi 1',
                        'image_alt_text' => 'Məhsul tövsiyyəsi şəkli'
                    ],
                    'en' => [
                        'title' => 'Product Suggestion 1',
                        'image_alt_text' => 'Product suggestion image'
                    ],
                    'ru' => [
                        'title' => 'Рекомендация продукта 1',
                        'image_alt_text' => 'Изображение рекомендации продукта'
                    ]
                ]
            ],
            [
                'image' => 'suggestions/sample-suggestion-2.jpg',
                'pdf' => 'suggestions/pdfs/sample-pdf-2.pdf',
                'status' => true,
                'order' => 2,
                'translations' => [
                    'az' => [
                        'title' => 'Xidmət Tövsiyyəsi 2',
                        'image_alt_text' => 'Xidmət tövsiyyəsi şəkli'
                    ],
                    'en' => [
                        'title' => 'Service Suggestion 2',
                        'image_alt_text' => 'Service suggestion image'
                    ],
                    'ru' => [
                        'title' => 'Рекомендация услуги 2',
                        'image_alt_text' => 'Изображение рекомендации услуги'
                    ]
                ]
            ],
            [
                'image' => 'suggestions/sample-suggestion-3.jpg',
                'pdf' => null,
                'status' => true,
                'order' => 3,
                'translations' => [
                    'az' => [
                        'title' => 'Texnologiya Tövsiyyəsi 3',
                        'image_alt_text' => 'Texnologiya tövsiyyəsi şəkli'
                    ],
                    'en' => [
                        'title' => 'Technology Suggestion 3',
                        'image_alt_text' => 'Technology suggestion image'
                    ],
                    'ru' => [
                        'title' => 'Технологическая рекомендация 3',
                        'image_alt_text' => 'Изображение технологической рекомендации'
                    ]
                ]
            ],
            [
                'image' => 'suggestions/sample-suggestion-4.jpg',
                'pdf' => 'suggestions/pdfs/sample-pdf-4.pdf',
                'status' => false,
                'order' => 4,
                'translations' => [
                    'az' => [
                        'title' => 'İnnovasiya Tövsiyyəsi 4',
                        'image_alt_text' => 'İnnovasiya tövsiyyəsi şəkli'
                    ],
                    'en' => [
                        'title' => 'Innovation Suggestion 4',
                        'image_alt_text' => 'Innovation suggestion image'
                    ],
                    'ru' => [
                        'title' => 'Инновационная рекомендация 4',
                        'image_alt_text' => 'Изображение инновационной рекомендации'
                    ]
                ]
            ],
            [
                'image' => 'suggestions/sample-suggestion-5.jpg',
                'pdf' => null,
                'status' => true,
                'order' => 5,
                'translations' => [
                    'az' => [
                        'title' => 'Dizayn Tövsiyyəsi 5',
                        'image_alt_text' => 'Dizayn tövsiyyəsi şəkli'
                    ],
                    'en' => [
                        'title' => 'Design Suggestion 5',
                        'image_alt_text' => 'Design suggestion image'
                    ],
                    'ru' => [
                        'title' => 'Рекомендация дизайна 5',
                        'image_alt_text' => 'Изображение рекомендации дизайна'
                    ]
                ]
            ]
        ];

        foreach ($suggestions as $suggestionData) {
            // Create suggestion
            $suggestion = Suggestion::create([
                'image' => $suggestionData['image'],
                'pdf' => $suggestionData['pdf'],
                'status' => $suggestionData['status'],
                'order' => $suggestionData['order']
            ]);

            // Create translations for each language
            foreach ($languages as $language) {
                $translationData = $suggestionData['translations'][$language->lang_code] ?? [];
                
                SuggestionTranslate::create([
                    'suggestion_id' => $suggestion->id,
                    'language_id' => $language->id,
                    'title' => $translationData['title'] ?? 'Default Title',
                    'image_alt_text' => $translationData['image_alt_text'] ?? 'Default Alt Text'
                ]);
            }
        }

        $this->command->info('Suggestions seeded successfully!');
    }
} 