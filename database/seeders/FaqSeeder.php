<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;
use App\Models\FaqTranslate;
use App\Models\Language;

class FaqSeeder extends Seeder
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

        // FAQ data from images in order
        $faqs = [
            [
                'order' => 1,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Özəl mebel sifarişləri hansı müddətə təhvil verilir?',
                        'answer' => 'Özəl mebel sifarişlərinə müştərinin istəklərinə və sifarişin dizayn formasna və həcminə görə istehsal müddəti təyin olunur.'
                    ],
                    'en' => [
                        'question' => 'How long does it take to deliver custom furniture orders?',
                        'answer' => 'The production time for custom furniture orders is determined according to the customer\'s wishes, the design form, and the volume of the order.'
                    ]
                ]
            ],
            [
                'order' => 2,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Mətbəx mebellərinin paqon metrəsi neçəyədir?',
                        'answer' => 'Mətbəx mebellərinin paqon metrəsi materialından və işin həcmindən asılı olaraq 400 AZN-dən başlayır.'
                    ],
                    'en' => [
                        'question' => 'How much does a linear meter of kitchen furniture cost?',
                        'answer' => 'The linear meter price of kitchen furniture starts from 400 AZN, depending on the material and the volume of work.'
                    ]
                ]
            ],
            [
                'order' => 3,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Qardaşlar mebelin satış mağazaları hansı ünvanda yerləşir?',
                        'answer' => 'Qardaşlar mebel şirkətinin hal-hazırda 3 satış mağazası mövcuddur. Onlardan biri Bakı şəh., Həsən Əliyev 12A ünvanında, digəri isə Biləcəri qəsəbəsi, Binəqədi şossesi 8F ünvanında yerləşir.'
                    ],
                    'en' => [
                        'question' => 'Where are Qardaşlar furniture sales stores located?',
                        'answer' => 'Qardaşlar furniture company currently has 3 sales stores. One of them is located at Hasan Aliyev 12A, Baku city, and the other is located at Bilajari settlement, Binagadi highway 8F.'
                    ]
                ]
            ],
            [
                'order' => 4,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Qardaşlar mebeldə planlama xidməti varmı?',
                        'answer' => 'Bəli, Siz planlama xidmətindən istifadə etməklə məkanınıza uyğun mebellərin daxili yerləşməsini, formasını, ölçülərini və digər parametrləri ilə bağlı ətraflı məlumat əldə edə bilərsiniz. Sizə bu sahədə yardımçı olmağa məmnun olardıq. Xidmətlə bağlı ətraflı məlumat almaq üçün 077-486-24-22 nömrəsi ilə əlaqə saxlaya bilərsiniz.'
                    ],
                    'en' => [
                        'question' => 'Does Qardaşlar furniture offer planning services?',
                        'answer' => 'Yes, you can get detailed information about the internal placement, form, dimensions, and other parameters of furniture suitable for your space by using the planning service. We would be happy to assist you in this area. You can contact us at 077-486-24-22 for detailed information about the service.'
                    ]
                ]
            ],
            [
                'order' => 5,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Hazırlanan mebellərin materialları hansı ölkələrdən gətirilir?',
                        'answer' => 'Qardaşlar mebel şirkətinin istehsal etdiyi mebellərin materialları Rusiya, Türkiyə və Avropa ölkələrindən gətirilir.'
                    ],
                    'en' => [
                        'question' => 'From which countries are the materials for the furniture produced brought?',
                        'answer' => 'The materials for the furniture produced by Qardaşlar furniture company are brought from Russia, Turkey, and European countries.'
                    ]
                ]
            ],
            [
                'order' => 6,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Sizdə usta xidməti varmı?',
                        'answer' => 'Qardaşlar mebel şirkəti yalnız öz müştərilərinə usta xidməti göstərməkdədir. Xidmətin dəyəri görüləcək işin həcmindən asılı olaraq dəyişir. Sizə yardımçı olmaq üçün 077-486-24-22 nömrəsi ilə əlaqə saxlaya bilərsiniz.'
                    ],
                    'en' => [
                        'question' => 'Do you have master service?',
                        'answer' => 'Qardaşlar furniture company provides master service only to its customers. The cost of the service varies depending on the volume of work to be done. You can contact us at 077-486-24-22 for assistance.'
                    ]
                ]
            ],
            [
                'order' => 7,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Sizdə sıradan çıxmış mebellərin aksesuarları varmı?',
                        'answer' => 'Bəli, Qardaşlar mebel şirkətinin müştəriləri sıradan çıxmış mebel aksesuarları ilə bağlı şirkətimizə müraciət edə bilərlər. Bunun üçün 077-486-24-22 nömrəsi xidmətinizdədir.'
                    ],
                    'en' => [
                        'question' => 'Do you have accessories for out-of-order furniture?',
                        'answer' => 'Yes, customers of Qardaşlar furniture company can contact our company regarding out-of-order furniture accessories. For this, the number 077-486-24-22 is at your service.'
                    ]
                ]
            ],
            [
                'order' => 8,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Qardaşlar mebel özəl layihələrdə iştirak edirmi?',
                        'answer' => 'Bəli, Qardaşlar mebel fəaliyyət göstərdiyi illər ərzində 400-dən çox özəl layihənin mebel təchizatında iştirak etmişdir. İştirak etdiyimiz özəl layihələrlə bağlı ətraflı məlumatı müvafiq bölmədən və ya 077-486-24-22 nömrəsinə zəng etməklə əldə edə bilərsiniz.'
                    ],
                    'en' => [
                        'question' => 'Does Qardaşlar furniture participate in special projects?',
                        'answer' => 'Yes, Qardaşlar furniture has participated in the furniture supply of more than 400 special projects during its years of operation. You can get detailed information about the special projects we have participated in from the relevant section or by calling 077-486-24-22.'
                    ]
                ]
            ],
            [
                'order' => 9,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Sizdən cehizlik dəstləri sifariş etmək olur?',
                        'answer' => 'Bəli, Siz mebellə bağlı istəklərinizi bizə bildirin, xoş gününüzə özəl qiymət və təkliflərimizlə Sizi sevindirək. Üzünüzdə təbəssüm yaratmaq üçün 077-486-24-22 nömrəsi ilə əlaqə saxlamağa tələsin.'
                    ],
                    'en' => [
                        'question' => 'Is it possible to order dowry sets from you?',
                        'answer' => 'Yes, let us know your furniture-related requests, and we will make you happy with special prices and offers for your special day. Hurry to contact us at 077-486-24-22 to put a smile on your face.'
                    ]
                ]
            ],
            [
                'order' => 10,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Mebellərin kredit və ya taksit kartları vasitəsilə satışı varmı?',
                        'answer' => 'Bəli, Qardaşlar mebel şirkəti mebellərin taksit kartları və kreditlə satışını həyata keçirir.'
                    ],
                    'en' => [
                        'question' => 'Is there a sale of furniture via credit or installment cards?',
                        'answer' => 'Yes, Gardashlar furniture company carries out the sale of furniture with installment cards and credit.'
                    ]
                ]
            ],
            [
                'order' => 11,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Mebellərə zəmanət verilirmi?',
                        'answer' => 'Bəli, qardaşlar mebel şirkəti mebellərinə zəmanət verir. Zəmanət şərtləri ilə bağlı məlumatlarla müvafiq bölmədən tanış ola bilərsiniz.'
                    ],
                    'en' => [
                        'question' => 'Is there a warranty for furniture?',
                        'answer' => 'Yes, Gardashlar furniture company provides a warranty for its furniture. You can get acquainted with information regarding warranty conditions in the relevant section.'
                    ]
                ]
            ],
            [
                'order' => 12,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Mebellərin dəyərini onlayn olaraq ödəyə bilərəm?',
                        'answer' => 'Bəli, müştərilərimiz əldə etdikləri mebelin dəyərini həm nağd, həm də bank kartları vasitəsilə ödəyə bilərlər.'
                    ],
                    'en' => [
                        'question' => 'Can I pay for the furniture online?',
                        'answer' => 'Yes, our customers can pay for the furniture they purchase both in cash and via bank cards.'
                    ]
                ]
            ],
            [
                'order' => 13,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Məhsulun qaytarılması prosesi necə baş verir?',
                        'answer' => 'Qardaşlar mebel məhsullarının keyfiyyətinə tam zəmanət verir. Malların müştərilərə təhvil verilməsi prosesi şirkətin satış nümayəndəsinin nəzarəti altında olur və akt əsasında təslim edilir. Şirkət nümayəndəsinin məsuliyyətsizliyi və ya texniki səhv nəticəsində yaranan problemlərə görə şirkət məsuliyyət daşıyır, lakin istehlakçının ehtiyatsızlığı səbəbindən yaranan narahatçılıqlara görə şirkət cavabdeh deyil və problem müştərinin hesabına həll edilir. Xüsusi qeyd: istehlakçının fərdi sifarişi əsasında hazırlatdığı hər hansı bir malın qaytarılması qəbul olunmur.'
                    ],
                    'en' => [
                        'question' => 'How does the product return process work?',
                        'answer' => 'Qardaşlar furniture provides a full warranty for the quality of its products. The process of delivering goods to customers is under the supervision of the company\'s sales representative and is delivered on the basis of an act. The company is responsible for problems arising from the negligence or technical error of the company representative, but the company is not responsible for inconveniences caused by consumer carelessness and the problem is solved at the customer\'s expense. Special note: the return of any product prepared on the basis of the consumer\'s individual order is not accepted.'
                    ]
                ]
            ],
            [
                'order' => 14,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Korporativ əməkdaşlıq üçün necə müraciət edə bilərəm?',
                        'answer' => 'Qardaşlar mebel şirkəti ilə korporativ səviyyədə əməkdaşlıq etmək üçün 077-486-24-22 nömrəsi ilə əlaqə saxlaya bilərsiniz.'
                    ],
                    'en' => [
                        'question' => 'How can I apply for corporate cooperation?',
                        'answer' => 'You can contact 077-486-24-22 to cooperate at the corporate level with Qardaşlar furniture company.'
                    ]
                ]
            ],
            [
                'order' => 15,
                'status' => true,
                'translations' => [
                    'az' => [
                        'question' => 'Televizor altligi divarda quraşdırıldıqda kabelləri necə səliqəli saxlamaq olar?',
                        'answer' => 'Divara quraşdırılan bir çox televizor altlığı modelində kabelləri səliqəli saxlamaq üçün xüsusi kabel idarəetmə sistemləri mövcuddur. Bu sistemlər kabellərin görünməz şəkildə saxlanmasını təmin edir.'
                    ],
                    'en' => [
                        'question' => 'How to neatly store cables when a TV stand is mounted on the wall?',
                        'answer' => 'Many wall-mounted TV stand models have special cable management systems for neatly storing cables. These systems ensure that cables are stored invisibly.'
                    ]
                ]
            ]
        ];

        foreach ($faqs as $faqData) {
            // Create FAQ
            $faq = Faq::create([
                'order' => $faqData['order'],
                'status' => $faqData['status']
            ]);

            // Create translations
            foreach ($faqData['translations'] as $langCode => $translation) {
                $language = $langCode === 'az' ? $azLanguage : $enLanguage;
                
                FaqTranslate::create([
                    'faq_id' => $faq->id,
                    'language_id' => $language->id,
                    'question' => $translation['question'],
                    'answer' => $translation['answer']
                ]);
            }
        }

        $this->command->info('FAQ seeded successfully!');
    }
} 