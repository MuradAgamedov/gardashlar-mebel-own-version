<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedDictionaries();
        $this->seedTranslations();
    }

    private function seedDictionaries(): void
    {
        $keywords = [
            'name_required', 'name_max', 'email_required', 'email_email', 'email_max',
            'mobile_required', 'mobile_max', 'mobile_invalid', 'area_required', 'area_max',
            'address_required', 'address_max', 'floor_count_required', 'floor_count_max',
            'is_repaired_required', 'is_repaired_boolean', 'has_warehouse_required',
            'has_warehouse_boolean', 'has_parking_required', 'has_parking_boolean',
            'details_required', 'details_max', 'file_invalid', 'file_mimes', 'file_max',
            'file_upload_failed', 'form_submitted_success', 'validation_failed',
            'form_submit_error', 'vacancy_id_required', 'vacancy_not_found',
            'vacancy_not_available', 'message_required', 'message_max', 'service_id_required',
            'service_not_found', 'service_not_available', 'city_required', 'city_max',
            'contact_form_success', 'contact_form_error'
        ];

        $existingKeywords = DB::table('dictionaries')->pluck('keyword')->toArray();
        $newKeywords = array_diff($keywords, $existingKeywords);

        if (!empty($newKeywords)) {
            $now = now();
            $insertData = array_map(function ($keyword) use ($now) {
                return [
                    'keyword' => $keyword,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $newKeywords);

            DB::table('dictionaries')->insert($insertData);
            $this->command->info('Added ' . count($newKeywords) . ' new dictionary entries.');
        } else {
            $this->command->info('All dictionary entries already exist.');
        }
    }

    private function seedTranslations(): void
    {
        $translations = [
            // Azerbaijani (3)
            3 => [
                'name_required' => 'Ad tələb olunur',
                'name_max' => 'Ad 255 simvoldan çox ola bilməz',
                'email_required' => 'Email tələb olunur',
                'email_email' => 'Email düzgün formatda deyil',
                'email_max' => 'Email 255 simvoldan çox ola bilməz',
                'mobile_required' => 'Mobil nömrə tələb olunur',
                'mobile_max' => 'Mobil nömrə 20 simvoldan çox ola bilməz',
                'mobile_invalid' => 'Mobil nömrə formatı yanlışdır',
                'area_required' => 'Sahə tələb olunur',
                'area_max' => 'Sahə 50 simvoldan çox ola bilməz',
                'address_required' => 'Ünvan tələb olunur',
                'address_max' => 'Ünvan 500 simvoldan çox ola bilməz',
                'floor_count_required' => 'Mərtəbə sayı tələb olunur',
                'floor_count_max' => 'Mərtəbə sayı 50 simvoldan çox ola bilməz',
                'is_repaired_required' => 'Təmir vəziyyəti tələb olunur',
                'is_repaired_boolean' => 'Təmir vəziyyəti doğru və ya yanlış olmalıdır',
                'has_warehouse_required' => 'Anbar vəziyyəti tələb olunur',
                'has_warehouse_boolean' => 'Anbar vəziyyəti doğru və ya yanlış olmalıdır',
                'has_parking_required' => 'Parkinq vəziyyəti tələb olunur',
                'has_parking_boolean' => 'Parkinq vəziyyəti doğru və ya yanlış olmalıdır',
                'details_required' => 'Ətraflı məlumat tələb olunur',
                'details_max' => 'Ətraflı məlumat 2000 simvoldan çox ola bilməz',
                'file_invalid' => 'Fayl düzgün formatda deyil',
                'file_mimes' => 'Fayl növü icazə verilmir',
                'file_max' => 'Fayl ölçüsü 10MB-dan çox ola bilməz',
                'file_upload_failed' => 'Fayl yüklənərkən xəta baş verdi',
                'form_submitted_success' => 'Forma uğurla göndərildi',
                'validation_failed' => 'Yoxlama uğursuz oldu',
                'form_submit_error' => 'Form göndərilərkən xəta baş verdi',
                'vacancy_id_required' => 'Vakansiya ID-si tələb olunur',
                'vacancy_not_found' => 'Vakansiya tapılmadı',
                'vacancy_not_available' => 'Vakansiya artıq aktiv deyil',
                'message_required' => 'Mesaj tələb olunur',
                'message_max' => 'Mesaj çox uzundur',
                'service_id_required' => 'Xidmət ID-si tələb olunur',
                'service_not_found' => 'Xidmət tapılmadı',
                'service_not_available' => 'Xidmət aktiv deyil',
                'city_required' => 'Şəhər tələb olunur',
                'city_max' => 'Şəhər 255 simvoldan çox ola bilməz',
                'contact_form_success' => 'Mesajınız uğurla göndərildi',
                'contact_form_error' => 'Mesaj göndərilərkən xəta baş verdi',
            ],
            // English (1)
            1 => [
                'name_required' => 'Name is required',
                'name_max' => 'Name may not be greater than 255 characters',
                'email_required' => 'Email is required',
                'email_email' => 'Email must be a valid email address',
                'email_max' => 'Email may not be greater than 255 characters',
                'mobile_required' => 'Mobile number is required',
                'mobile_max' => 'Mobile number may not be greater than 20 characters',
                'mobile_invalid' => 'Invalid mobile number format',
                'area_required' => 'Area is required',
                'area_max' => 'Area may not be greater than 50 characters',
                'address_required' => 'Address is required',
                'address_max' => 'Address may not be greater than 500 characters',
                'floor_count_required' => 'Floor count is required',
                'floor_count_max' => 'Floor count may not be greater than 50 characters',
                'is_repaired_required' => 'Repair status is required',
                'is_repaired_boolean' => 'Repair status must be true or false',
                'has_warehouse_required' => 'Warehouse status is required',
                'has_warehouse_boolean' => 'Warehouse status must be true or false',
                'has_parking_required' => 'Parking status is required',
                'has_parking_boolean' => 'Parking status must be true or false',
                'details_required' => 'Details are required',
                'details_max' => 'Details may not be greater than 2000 characters',
                'file_invalid' => 'File must be a valid file',
                'file_mimes' => 'Invalid file type',
                'file_max' => 'File may not be greater than 10MB',
                'file_upload_failed' => 'Failed to upload file',
                'form_submitted_success' => 'Form submitted successfully',
                'validation_failed' => 'Validation failed',
                'form_submit_error' => 'Error submitting form',
                'vacancy_id_required' => 'Vacancy ID is required',
                'vacancy_not_found' => 'Vacancy not found',
                'vacancy_not_available' => 'Vacancy not available for applications',
                'message_required' => 'Message is required',
                'message_max' => 'Message may not be greater than 1000 characters',
                'service_id_required' => 'Service ID is required',
                'service_not_found' => 'Service not found',
                'service_not_available' => 'Service not available',
                'city_required' => 'City is required',
                'city_max' => 'City may not be greater than 255 characters',
                'contact_form_success' => 'Your message was sent successfully',
                'contact_form_error' => 'An error occurred while sending your message',
            ],
            // Russian (4)
            4 => [
                'name_required' => 'Имя обязательно для заполнения',
                'name_max' => 'Имя не может превышать 255 символов',
                'email_required' => 'Электронная почта обязательна',
                'email_email' => 'Введите действительный адрес электронной почты',
                'email_max' => 'Адрес электронной почты не может превышать 255 символов',
                'mobile_required' => 'Номер телефона обязателен',
                'mobile_max' => 'Номер телефона не может превышать 20 символов',
                'mobile_invalid' => 'Неверный формат номера телефона',
                'area_required' => 'Площадь обязательна для заполнения',
                'area_max' => 'Площадь не может превышать 50 символов',
                'address_required' => 'Адрес обязателен',
                'address_max' => 'Адрес не может превышать 500 символов',
                'floor_count_required' => 'Количество этажей обязательно',
                'floor_count_max' => 'Количество этажей не может превышать 50 символов',
                'is_repaired_required' => 'Необходимо указать состояние ремонта',
                'is_repaired_boolean' => 'Состояние ремонта должно быть истина или ложь',
                'has_warehouse_required' => 'Необходимо указать наличие склада',
                'has_warehouse_boolean' => 'Значение склада должно быть истина или ложь',
                'has_parking_required' => 'Необходимо указать наличие парковки',
                'has_parking_boolean' => 'Значение парковки должно быть истина или ложь',
                'details_required' => 'Необходимо указать детали',
                'details_max' => 'Детали не могут превышать 2000 символов',
                'file_invalid' => 'Файл имеет недопустимый формат',
                'file_mimes' => 'Недопустимый тип файла',
                'file_max' => 'Размер файла не может превышать 10 МБ',
                'file_upload_failed' => 'Ошибка при загрузке файла',
                'form_submitted_success' => 'Форма успешно отправлена',
                'validation_failed' => 'Ошибка проверки данных',
                'form_submit_error' => 'Произошла ошибка при отправке формы',
                'vacancy_id_required' => 'ID вакансии обязательно',
                'vacancy_not_found' => 'Вакансия не найдена',
                'vacancy_not_available' => 'Вакансия недоступна для подачи заявки',
                'message_required' => 'Сообщение обязательно для заполнения',
                'message_max' => 'Сообщение слишком длинное',
                'service_id_required' => 'ID услуги обязательно',
                'service_not_found' => 'Услуга не найдена',
                'service_not_available' => 'Услуга недоступна',
                'city_required' => 'Город обязателен для заполнения',
                'city_max' => 'Название города не может превышать 255 символов',
                'contact_form_success' => 'Ваше сообщение успешно отправлено',
                'contact_form_error' => 'Произошла ошибка при отправке сообщения',
            ],
        ];

        // Get all dictionaries
        $dictionaries = DB::table('dictionaries')->pluck('id', 'keyword');
        
        // Get existing translations
        $existing = DB::table('dictionary_translates')
            ->select(DB::raw('CONCAT(dictionary_id, "-", language_id) as unique_key'))
            ->pluck('unique_key')
            ->toArray();

        $toInsert = [];
        $now = now();
        $count = 0;

        foreach ($translations as $languageId => $langTranslations) {
            foreach ($langTranslations as $keyword => $value) {
                if (!isset($dictionaries[$keyword])) {
                    $this->command->warn("Warning: Dictionary key '$keyword' not found. Skipping...");
                    continue;
                }

                $uniqueKey = $dictionaries[$keyword] . '-' . $languageId;
                
                if (!in_array($uniqueKey, $existing)) {
                    $toInsert[] = [
                        'dictionary_id' => $dictionaries[$keyword],
                        'language_id' => $languageId,
                        'value' => $value,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $count++;
                }
            }
        }

        if (!empty($toInsert)) {
            // Insert in chunks to avoid hitting max_allowed_packet
            foreach (array_chunk($toInsert, 50) as $chunk) {
                DB::table('dictionary_translates')->insert($chunk);
            }
            $this->command->info("Added $count new translations.");
        } else {
            $this->command->info('All translations already exist.');
        }
    }
}
