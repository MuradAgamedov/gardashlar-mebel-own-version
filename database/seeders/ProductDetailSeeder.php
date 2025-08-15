<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductDetailTranslate;
use App\Models\Language;

class ProductDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get languages
        $languages = Language::all();
        if ($languages->isEmpty()) {
            $this->command->error('No languages found. Please run LanguageSeeder first.');
            return;
        }

        // Get products
        $products = Product::take(5)->get();
        if ($products->isEmpty()) {
            $this->command->error('No products found. Please run ProductSeeder first.');
            return;
        }

        $this->command->info('Seeding ProductDetails and ProductDetailTranslates...');

        foreach ($products as $product) {
            // Create product details
            $this->createProductDetails($product, $languages);
        }

        $this->command->info('ProductDetails and ProductDetailTranslates seeded successfully!');
    }

    /**
     * Create product details for a specific product
     */
    private function createProductDetails(Product $product, $languages): void
    {
        // Sample detail types
        $detailTypes = [
            'dimensions' => [
                'az' => 'Ölçülər',
                'en' => 'Dimensions',
                'ru' => 'Размеры'
            ],
            'materials' => [
                'az' => 'Materiallar',
                'en' => 'Materials',
                'ru' => 'Материалы'
            ],
            'colors' => [
                'az' => 'Rənglər',
                'en' => 'Colors',
                'ru' => 'Цвета'
            ],
            'weight' => [
                'az' => 'Çəki',
                'en' => 'Weight',
                'ru' => 'Вес'
            ],
            'warranty' => [
                'az' => 'Zəmanət',
                'en' => 'Warranty',
                'ru' => 'Гарантия'
            ],
            'features' => [
                'az' => 'Xüsusiyyətlər',
                'en' => 'Features',
                'ru' => 'Особенности'
            ]
        ];

        foreach ($detailTypes as $keyword => $translations) {
            // Create product detail
            $productDetail = ProductDetail::create([
                'product_id' => $product->id,
                'order' => ProductDetail::where('product_id', $product->id)->max('order') + 1
            ]);

            // Create translations for each language
            foreach ($languages as $language) {
                $langCode = $language->lang_code;
                
                if (isset($translations[$langCode])) {
                    $value = $this->getSampleValue($keyword, $langCode);
                    
                    ProductDetailTranslate::create([
                        'product_detail_id' => $productDetail->id,
                        'language_id' => $language->id,
                        'keyword' => $keyword,
                        'value' => $value
                    ]);
                }
            }
        }
    }

    /**
     * Get sample values for different detail types
     */
    private function getSampleValue(string $keyword, string $langCode): array
    {
        $values = [
            'dimensions' => [
                'az' => ['uzunluq' => '120 cm', 'eni' => '80 cm', 'hündürlük' => '45 cm'],
                'en' => ['length' => '120 cm', 'width' => '80 cm', 'height' => '45 cm'],
                'ru' => ['длина' => '120 см', 'ширина' => '80 см', 'высота' => '45 см']
            ],
            'materials' => [
                'az' => ['əsas_material' => 'Ağac', 'üzlük' => 'MDF', 'rəng' => 'Ağ'],
                'en' => ['main_material' => 'Wood', 'veneer' => 'MDF', 'finish' => 'White'],
                'ru' => ['основной_материал' => 'Дерево', 'шпон' => 'МДФ', 'отделка' => 'Белый']
            ],
            'colors' => [
                'az' => ['mövcud_rənglər' => ['Ağ', 'Qara', 'Qəhvəyi'], 'standart' => 'Ağ'],
                'en' => ['available_colors' => ['White', 'Black', 'Brown'], 'standard' => 'White'],
                'ru' => ['доступные_цвета' => ['Белый', 'Черный', 'Коричневый'], 'стандарт' => 'Белый']
            ],
            'weight' => [
                'az' => ['net_çəki' => '25 kg', 'qablaşdırma_çəkisi' => '28 kg'],
                'en' => ['net_weight' => '25 kg', 'packaging_weight' => '28 kg'],
                'ru' => ['чистый_вес' => '25 кг', 'вес_упаковки' => '28 кг']
            ],
            'warranty' => [
                'az' => ['müddət' => '2 il', 'şərtlər' => 'İstehsal qüsurlarına görə'],
                'en' => ['duration' => '2 years', 'terms' => 'Manufacturing defects'],
                'ru' => ['срок' => '2 года', 'условия' => 'Производственные дефекты']
            ],
            'features' => [
                'az' => ['xüsusiyyətlər' => ['Çoxfunksiyalı', 'Modern dizayn', 'Yüksək keyfiyyət'], 'qeyd' => 'Məişət üçün ideal'],
                'en' => ['features' => ['Multifunctional', 'Modern design', 'High quality'], 'note' => 'Perfect for home use'],
                'ru' => ['особенности' => ['Многофункциональный', 'Современный дизайн', 'Высокое качество'], 'примечание' => 'Идеально для дома']
            ]
        ];

        return $values[$keyword][$langCode] ?? [];
    }
}
