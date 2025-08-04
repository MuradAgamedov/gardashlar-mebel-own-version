<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IndividualProject;
use App\Models\IndividualProjectTranslate;
use App\Models\Language;
use App\Models\ProjectCategory;

class IndividualProjectSeeder extends Seeder
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

        // Get project categories
        $categories = ProjectCategory::all();
        if ($categories->isEmpty()) {
            $this->command->error('Project categories not found. Please run ProjectCategorySeeder first.');
            return;
        }

        // Sample data for individual projects
        $projects = [
            [
                'order' => 1,
                'status' => true,
                'image' => 'individual-projects/project1.jpg',
                'translations' => [
                    'az' => [
                        'title' => 'Müasir Ev Layihəsi',
                        'description' => 'Bu layihə müasir memarlıq üslubunda tikilmiş evdir. Minimalist dizayn və funksionallıq birləşdirilib.',
                        'seo_title' => 'Müasir Ev Layihəsi - Gardashlar',
                        'seo_keywords' => 'ev layihəsi, müasir ev, memarlıq',
                        'seo_description' => 'Müasir ev layihəsi - minimalist dizayn və funksionallıq'
                    ],
                    'en' => [
                        'title' => 'Modern House Project',
                        'description' => 'This project is a house built in modern architectural style. Minimalist design and functionality are combined.',
                        'seo_title' => 'Modern House Project - Gardashlar',
                        'seo_keywords' => 'house project, modern house, architecture',
                        'seo_description' => 'Modern house project - minimalist design and functionality'
                    ]
                ]
            ],
            [
                'order' => 2,
                'status' => true,
                'image' => 'individual-projects/project2.jpg',
                'translations' => [
                    'az' => [
                        'title' => 'Villa Layihəsi',
                        'description' => 'Lüks villa layihəsi - geniş sahə və yüksək keyfiyyətli materiallarla tikilmiş.',
                        'seo_title' => 'Villa Layihəsi - Gardashlar',
                        'seo_keywords' => 'villa layihəsi, lüks ev, villa',
                        'seo_description' => 'Lüks villa layihəsi - geniş sahə və yüksək keyfiyyət'
                    ],
                    'en' => [
                        'title' => 'Villa Project',
                        'description' => 'Luxury villa project - built with large area and high-quality materials.',
                        'seo_title' => 'Villa Project - Gardashlar',
                        'seo_keywords' => 'villa project, luxury house, villa',
                        'seo_description' => 'Luxury villa project - large area and high quality'
                    ]
                ]
            ],
            [
                'order' => 3,
                'status' => true,
                'image' => 'individual-projects/project3.jpg',
                'translations' => [
                    'az' => [
                        'title' => 'Ofis Binası Layihəsi',
                        'description' => 'Müasir ofis binası layihəsi - işçilər üçün rahat mühit yaradılıb.',
                        'seo_title' => 'Ofis Binası Layihəsi - Gardashlar',
                        'seo_keywords' => 'ofis binası, iş yeri, ofis',
                        'seo_description' => 'Müasir ofis binası layihəsi - rahat iş mühiti'
                    ],
                    'en' => [
                        'title' => 'Office Building Project',
                        'description' => 'Modern office building project - comfortable environment created for employees.',
                        'seo_title' => 'Office Building Project - Gardashlar',
                        'seo_keywords' => 'office building, workplace, office',
                        'seo_description' => 'Modern office building project - comfortable work environment'
                    ]
                ]
            ]
        ];

        foreach ($projects as $index => $projectData) {
            // Get a category (cycle through available categories)
            $category = $categories[$index % $categories->count()];
            
            // Create individual project
            $project = IndividualProject::create([
                'project_category_id' => $category->id,
                'order' => $projectData['order'],
                'status' => $projectData['status'],
                'image' => $projectData['image']
            ]);

            // Create translations
            foreach ($projectData['translations'] as $langCode => $translation) {
                $language = $langCode === 'az' ? $azLanguage : $enLanguage;
                
                IndividualProjectTranslate::create([
                    'individual_project_id' => $project->id,
                    'language_id' => $language->id,
                    'title' => $translation['title'],
                    'description' => $translation['description'],
                    'seo_title' => $translation['seo_title'],
                    'seo_keywords' => $translation['seo_keywords'],
                    'seo_description' => $translation['seo_description']
                ]);
            }
        }

        $this->command->info('Individual Projects seeded successfully!');
    }
} 