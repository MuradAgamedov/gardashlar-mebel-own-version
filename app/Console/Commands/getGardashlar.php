<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslate;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;

class getGardashlar extends Command
{
    protected $signature = 'getGardashlar';
    protected $description = 'Fetch Gardashlar API data and insert/update product categories with translations';

    public function handle()
    {
        $this->info('✅ Başladı: Gardashlar API-dən data alınır...');

        try {
            $response = Http::retry(3, 10000)
                ->timeout(120)
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

        // eyni id-ləri qrupla
        $grouped = collect($data)->groupBy('id');

        foreach ($grouped as $categoryId => $translations) {

            $first = $translations->first();

            $parentId = $first['parent_id'] ?? null;
            if ($parentId === 0 || !ProductCategory::where('id', $parentId)->exists()) {
                $parentId = null;
            }

            // yalnız 1 dəfə ProductCategory yaradılır
            $category = ProductCategory::updateOrCreate(
                ['id' => $categoryId],
                [
                    'parent_id' => $parentId,
                    'order' => $first['order'] ?? 1,
                    'status' => isset($first['status']) ? (bool)$first['status'] : true,
                    'image' => $first['image'] ?? null,
                ]
            );

            // bütün dilləri eyni category üçün yazırıq
            foreach ($translations as $item) {
                $langId = $item['lang_id'] ?? 1;
                $mappedLangId = match ($langId) {
                    1 => 3,
                    2 => 1,
                    3 => 4,
                    default => $langId,
                };

                // slug təmizlə və ehtiyatla yarat
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

                // ehtiyat slug
                if (!$slug) {
                    $slug = 'auto-' . $category->id . '-' . uniqid();
                }

                // Tərcüməni etibarlı şəkildə insert et
                try {
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
                } catch (QueryException $e) {
                    // Əgər UNIQUE CONSTRAINT xəta verərsə — fərqli slug ilə yenidən yaz
                    if ($e->getCode() === '23000') {
                        $safeSlug = $slugBase . '-' . uniqid();
                        ProductCategoryTranslate::updateOrCreate(
                            [
                                'product_category_id' => $category->id,
                                'language_id' => $mappedLangId,
                            ],
                            [
                                'title' => $item['title'] ?? '',
                                'slug' => $safeSlug,
                                'seo_title' => $item['page_title'] ?? '',
                                'seo_description' => $item['page_description'] ?? '',
                                'seo_keywords' => $item['page_keywords'] ?? '',
                                'seo_text' => $item['page_footer'] ?? '',
                            ]
                        );
                    } else {
                        throw $e;
                    }
                }
            }
        }

        $this->info('✅ Bütün data uğurla əlavə olundu! Heç bir duplicate slug mümkün deyil.');
        return 0;
    }
}
