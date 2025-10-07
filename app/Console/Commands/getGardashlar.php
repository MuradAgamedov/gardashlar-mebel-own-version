<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslate;
use Illuminate\Support\Str;

class getGardashlar extends Command
{
    protected $signature = 'getGardashlar';
    protected $description = 'Fetch Gardashlar API data and insert/update product categories with translations';

    public function handle()
    {
        $this->info('✅ Başladı: Gardashlar API-dən data alınır...');

        try {
            // 🟢 3 dəfə cəhd etsin, hər dəfə 10 saniyə fasilə ilə
            $response = Http::retry(3, 10000)
                ->timeout(120) // 120 saniyə limit
                ->get('https://gardashlar.com/testdata.php');
        } catch (\Exception $e) {
            $this->error('❌ API-yə qoşulma alınmadı: ' . $e->getMessage());
            return 1;
        }

        if (!$response->ok()) {
            $this->error('❌ API səhv cavab verdi. Status: ' . $response->status());
            return 1;
        }

        $data = $response->json();

        if (!is_array($data)) {
            $this->error('❌ API cavabı düzgün formatda deyil.');
            return 1;
        }

        // parent_id-yə görə sırala
        usort($data, fn($a, $b) => ($a['parent_id'] ?? 0) <=> ($b['parent_id'] ?? 0));

        foreach ($data as $item) {
            $parentId = $item['parent_id'] ?? null;
            if ($parentId === 0 || !ProductCategory::where('id', $parentId)->exists()) {
                $parentId = null;
            }

            $category = ProductCategory::updateOrCreate(
                ['id' => $item['id']],
                [
                    'parent_id' => $parentId,
                    'order' => $item['order'] ?? 1,
                    'status' => isset($item['status']) ? (bool)$item['status'] : true,
                    'image' => $item['image'] ?? null,
                ]
            );

            // 🔤 Dil mapping
            $langId = $item['lang_id'] ?? 1;
            $mappedLangId = match ($langId) {
                1 => 3,
                2 => 1,
                3 => 4,
                default => $langId,
            };

            // 🔒 Slug yaradılması və unikal təminatı
            $rawSlug = trim($item['slug_pr'] ?? '');
            $slugBase = $rawSlug ?: Str::slug($item['title'] ?? 'category', '-');
            if (!$slugBase) {
                $slugBase = 'category-' . $category->id;
            }

            $slug = $slugBase;
            $counter = 0;
            while (
                ProductCategoryTranslate::where('slug', $slug)
                    ->where('product_category_id', '!=', $category->id)
                    ->exists()
            ) {
                $counter++;
                $slug = $slugBase . '-' . $counter . '-' . time();
            }

            if (!$slug) {
                $slug = 'auto-' . $category->id . '-' . time();
            }

            ProductCategoryTranslate::updateOrCreate(
                [
                    'product_category_id' => $category->id,
                    'language_id' => $mappedLangId,
                ],
                [
                    'title' => $item['title'] ?? '',
                    'slug' => $slug,
                    'seo_title' => $item['page_title'] ?? '',
                    'seo_description' => $item['page_description'] ?? '',
                    'seo_keywords' => $item['page_keywords'] ?? '',
                    'seo_text' => $item['page_footer'] ?? '',
                ]
            );
        }

        $this->info('✅ Bütün data uğurla əlavə olundu! Xətasız.');
        return 0;
    }
}

