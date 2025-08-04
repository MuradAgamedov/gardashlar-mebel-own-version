<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Certificate;
use App\Models\CertificateTranslate;
use App\Models\Language;

class CertificateSeeder extends Seeder
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

        $certificates = [
            [
                'order' => 1,
                'status' => true,
                'date' => '2022-01-15',
                'image' => 'certificates/iso-14001-2015-certificate.jpg',
                'translations' => [
                    'az' => [
                        'image_alt_text' => 'ISO 14001:2015 Sertifikatı - Qardaşlar Mebel QSC',
                        'title' => 'ISO 14001:2015 Sertifikatı'
                    ],
                    'ru' => [
                        'image_alt_text' => 'Сертификат ISO 14001:2015 - Qardaşlar Mebel QSC',
                        'title' => 'Сертификат ISO 14001:2015'
                    ],
                    'en' => [
                        'image_alt_text' => 'ISO 14001:2015 Certificate - Qardaşlar Mebel QSC',
                        'title' => 'ISO 14001:2015 Certificate'
                    ]
                ]
            ],
            [
                'order' => 2,
                'status' => true,
                'date' => '2022-03-20',
                'image' => 'certificates/iso-9001-2015-certificate.jpg',
                'translations' => [
                    'az' => [
                        'image_alt_text' => 'ISO 9001:2015 Sertifikatı - Qardaşlar Mebel QSC',
                        'title' => 'ISO 9001:2015 Sertifikatı'
                    ],
                    'ru' => [
                        'image_alt_text' => 'Сертификат ISO 9001:2015 - Qardaşlar Mebel QSC',
                        'title' => 'Сертификат ISO 9001:2015'
                    ],
                    'en' => [
                        'image_alt_text' => 'ISO 9001:2015 Certificate - Qardaşlar Mebel QSC',
                        'title' => 'ISO 9001:2015 Certificate'
                    ]
                ]
            ],
            [
                'order' => 3,
                'status' => true,
                'date' => '2022-06-10',
                'image' => 'certificates/iso-45001-2018-certificate.jpg',
                'translations' => [
                    'az' => [
                        'image_alt_text' => 'ISO 45001:2018 Sertifikatı - Qardaşlar Mebel QSC',
                        'title' => 'ISO 45001:2018 Sertifikatı'
                    ],
                    'ru' => [
                        'image_alt_text' => 'Сертификат ISO 45001:2018 - Qardaşlar Mebel QSC',
                        'title' => 'Сертификат ISO 45001:2018'
                    ],
                    'en' => [
                        'image_alt_text' => 'ISO 45001:2018 Certificate - Qardaşlar Mebel QSC',
                        'title' => 'ISO 45001:2018 Certificate'
                    ]
                ]
            ]
        ];

        foreach ($certificates as $certificateData) {
            // Create certificate
            $certificate = Certificate::create([
                'order' => $certificateData['order'],
                'status' => $certificateData['status'],
                'date' => $certificateData['date'],
                'image' => $certificateData['image']
            ]);

            // Create translations
            foreach ($certificateData['translations'] as $langCode => $translation) {
                $language = Language::where('lang_code', $langCode)->first();
                
                if ($language) {
                    CertificateTranslate::create([
                        'certificate_id' => $certificate->id,
                        'language_id' => $language->id,
                        'image_alt_text' => $translation['image_alt_text'],
                        'title' => $translation['title']
                    ]);
                }
            }
        }

        $this->command->info('Certificates seeded successfully!');
        $this->command->info('Created ' . count($certificates) . ' certificates with translations.');
        $this->command->info('Note: Please add the actual certificate images to the storage/app/public/certificates/ directory.');
    }
} 