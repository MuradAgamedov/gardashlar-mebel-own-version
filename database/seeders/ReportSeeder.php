<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportTranslate;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reports = [
            [
                'order' => 1,
                'status' => true,
                'image' => 'reports/financial-report-icon.png', // Green G icon
                'pdf' => 'reports/2021-financial-report.pdf',
                'translations' => [
                    [
                        'language_id' => 3, // Azerbaijani
                        'title' => '2021-ci il Maliyə hesabatı',
                        'description' => 'is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s stan'
                    ],
                    [
                        'language_id' => 1, // English
                        'title' => '2021 Financial Report',
                        'description' => 'is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s stan'
                    ]
                ]
            ],
            [
                'order' => 2,
                'status' => true,
                'image' => 'reports/financial-report-icon.png', // Green G icon
                'pdf' => 'reports/2023-annual-financial-report.pdf',
                'translations' => [
                    [
                        'language_id' => 3, // Azerbaijani
                        'title' => '2023-cü il üzrə illik Maliyə hesabatı',
                        'description' => 'is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s stan'
                    ],
                    [
                        'language_id' => 1, // English
                        'title' => '2023 Annual Financial Report',
                        'description' => 'is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s stan'
                    ]
                ]
            ],
            [
                'order' => 3,
                'status' => true,
                'image' => 'reports/financial-report-icon.png', // Green G icon
                'pdf' => 'reports/2022-financial-report.pdf',
                'translations' => [
                    [
                        'language_id' => 3, // Azerbaijani
                        'title' => '2022-ci il Maliyə hesabatı',
                        'description' => 'is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s stan'
                    ],
                    [
                        'language_id' => 1, // English
                        'title' => '2022 Financial Report',
                        'description' => 'is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s stan'
                    ]
                ]
            ],
            [
                'order' => 4,
                'status' => true,
                'image' => 'reports/financial-report-icon.png', // Green G icon
                'pdf' => 'reports/2023-annual-financial-report-2.pdf',
                'translations' => [
                    [
                        'language_id' => 3, // Azerbaijani
                        'title' => '2023-cü il üzrə illik Maliyə hesabatı',
                        'description' => 'is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s stan'
                    ],
                    [
                        'language_id' => 1, // English
                        'title' => '2023 Annual Financial Report',
                        'description' => 'is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s stan'
                    ]
                ]
            ]
        ];

        foreach ($reports as $reportData) {
            $translations = $reportData['translations'];
            unset($reportData['translations']);
            
            $report = Report::create($reportData);
            
            foreach ($translations as $translation) {
                ReportTranslate::create([
                    'report_id' => $report->id,
                    'language_id' => $translation['language_id'],
                    'title' => $translation['title'],
                    'description' => $translation['description']
                ]);
            }
        }
    }
}
