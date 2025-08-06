<?php

namespace Database\Seeders;

use App\Models\PrivacyPolicy;
use App\Models\PrivacyPolicyTranslate;
use App\Models\CustomerRights;
use App\Models\CustomerRightsTranslate;
use App\Models\DeliveryPayment;
use App\Models\DeliveryPaymentTranslate;
use App\Models\WarrantyPolicy;
use App\Models\WarrantyPolicyTranslate;
use App\Models\RefundPolicy;
use App\Models\RefundPolicyTranslate;
use App\Models\CompanyManagement;
use App\Models\CompanyManagementTranslate;
use App\Models\SiteUsageRules;
use App\Models\SiteUsageRulesTranslate;
use App\Models\Language;
use Illuminate\Database\Seeder;

class PolicyPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::all();

        // Privacy Policy
        $privacyPolicy = PrivacyPolicy::firstOrCreate(['status' => true]);
        foreach ($languages as $language) {
            PrivacyPolicyTranslate::firstOrCreate(
                [
                    'privacy_policy_id' => $privacyPolicy->id,
                    'language_id' => $language->id
                ],
                [
                    'content' => $this->getDefaultContent('privacy_policy', $language->lang_code)
                ]
            );
        }

        // Customer Rights
        $customerRights = CustomerRights::firstOrCreate(['status' => true]);
        foreach ($languages as $language) {
            CustomerRightsTranslate::firstOrCreate(
                [
                    'customer_rights_id' => $customerRights->id,
                    'language_id' => $language->id
                ],
                [
                    'content' => $this->getDefaultContent('customer_rights', $language->lang_code)
                ]
            );
        }

        // Delivery Payment
        $deliveryPayment = DeliveryPayment::firstOrCreate(['status' => true]);
        foreach ($languages as $language) {
            DeliveryPaymentTranslate::firstOrCreate(
                [
                    'delivery_payment_id' => $deliveryPayment->id,
                    'language_id' => $language->id
                ],
                [
                    'content' => $this->getDefaultContent('delivery_payment', $language->lang_code)
                ]
            );
        }

        // Warranty Policy
        $warrantyPolicy = WarrantyPolicy::firstOrCreate(['status' => true]);
        foreach ($languages as $language) {
            WarrantyPolicyTranslate::firstOrCreate(
                [
                    'warranty_policy_id' => $warrantyPolicy->id,
                    'language_id' => $language->id
                ],
                [
                    'content' => $this->getDefaultContent('warranty_policy', $language->lang_code)
                ]
            );
        }

        // Refund Policy
        $refundPolicy = RefundPolicy::firstOrCreate(['status' => true]);
        foreach ($languages as $language) {
            RefundPolicyTranslate::firstOrCreate(
                [
                    'refund_policy_id' => $refundPolicy->id,
                    'language_id' => $language->id
                ],
                [
                    'content' => $this->getDefaultContent('refund_policy', $language->lang_code)
                ]
            );
        }

        // Company Management
        $companyManagement = CompanyManagement::firstOrCreate(['status' => true]);
        foreach ($languages as $language) {
            CompanyManagementTranslate::firstOrCreate(
                [
                    'company_management_id' => $companyManagement->id,
                    'language_id' => $language->id
                ],
                [
                    'content' => $this->getDefaultContent('company_management', $language->lang_code)
                ]
            );
        }

        // Site Usage Rules
        $siteUsageRules = SiteUsageRules::firstOrCreate(['status' => true]);
        foreach ($languages as $language) {
            SiteUsageRulesTranslate::firstOrCreate(
                [
                    'site_usage_rules_id' => $siteUsageRules->id,
                    'language_id' => $language->id
                ],
                [
                    'content' => $this->getDefaultContent('site_usage_rules', $language->lang_code)
                ]
            );
        }

        $this->command->info('Policy pages seeded successfully!');
    }

    private function getDefaultContent($type, $langCode)
    {
        $content = [
            'az' => [
                'privacy_policy' => '<p>Gizlilik siyasəti məzmunu burada olacaq...</p>',
                'customer_rights' => '<p>Müştəri hüquqları məzmunu burada olacaq...</p>',
                'delivery_payment' => '<p>Çatdırılma və ödəniş məzmunu burada olacaq...</p>',
                'warranty_policy' => '<p>Zəmanət siyasəti məzmunu burada olacaq...</p>',
                'refund_policy' => '<p>Geri qaytarma siyasəti məzmunu burada olacaq...</p>',
                'company_management' => '<p>Şirkət idarəetməsi məzmunu burada olacaq...</p>',
                'site_usage_rules' => '<p>Sayt istifadə qaydaları məzmunu burada olacaq...</p>'
            ],
            'en' => [
                'privacy_policy' => '<p>Privacy policy content will be here...</p>',
                'customer_rights' => '<p>Customer rights content will be here...</p>',
                'delivery_payment' => '<p>Delivery and payment content will be here...</p>',
                'warranty_policy' => '<p>Warranty policy content will be here...</p>',
                'refund_policy' => '<p>Refund policy content will be here...</p>',
                'company_management' => '<p>Company management content will be here...</p>',
                'site_usage_rules' => '<p>Site usage rules content will be here...</p>'
            ],
            'ru' => [
                'privacy_policy' => '<p>Содержание политики конфиденциальности будет здесь...</p>',
                'customer_rights' => '<p>Содержание прав клиентов будет здесь...</p>',
                'delivery_payment' => '<p>Содержание доставки и оплаты будет здесь...</p>',
                'warranty_policy' => '<p>Содержание гарантийной политики будет здесь...</p>',
                'refund_policy' => '<p>Содержание политики возврата будет здесь...</p>',
                'company_management' => '<p>Содержание управления компанией будет здесь...</p>',
                'site_usage_rules' => '<p>Содержание правил использования сайта будет здесь...</p>'
            ]
        ];

        return $content[$langCode][$type] ?? $content['az'][$type];
    }
} 