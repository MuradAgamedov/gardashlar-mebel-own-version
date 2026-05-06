<?php
namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductType;
use App\Models\ProductTag;
use App\Repositories\Global\BaseRepository;

class ProductRepository extends BaseRepository
{

    public function __construct(public Product $product)
    {
    }

    // Məhsulları filtrlə və gətir - Optimized
    public function getProducts($request)
    {
        // Get current language ID dynamically
        $currentLanguageId = \App\Models\Language::getMainLanguage()->id ?? 1;
        
        // Sadəcə lazım olan əlaqələri yüklə
        $query = $this->product->select(['products.id', 'products.price', 'products.discount_price', 'products.status', 'products.view_count', 'products.created_at'])
            ->with([
                'translates' => function($q) use ($currentLanguageId) {
                    $q->select(['product_id', 'language_id', 'title', 'slug'])
                      ->where('language_id', $currentLanguageId); // Mövcud dil
                },
                'categories' => function($q) use ($currentLanguageId) {
                    $q->select(['product_categories.id'])->with(['translates' => function($tq) use ($currentLanguageId) {
                        $tq->select(['product_category_id', 'title'])->where('language_id', $currentLanguageId);
                    }]);
                },
                'types' => function($q) use ($currentLanguageId) {
                    $q->select(['product_types.id'])->with(['translates' => function($tq) use ($currentLanguageId) {
                        $tq->select(['product_type_id', 'title'])->where('language_id', $currentLanguageId);
                    }]);
                }
            ]);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('translates', function($q) use ($search, $currentLanguageId) {
                $q->where('title', 'like', "%{$search}%")
                  ->where('language_id', $currentLanguageId); // Mövcuddildə axtarış
            });
        }
        
        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('product_categories.id', $request->input('category'));
            });
        }
        
        // Type filter
        if ($request->filled('type')) {
            $query->whereHas('types', function($q) use ($request) {
                $q->where('product_types.id', $request->input('type'));
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('products.status', $request->input('status'));
        }
        
        return $query->orderBy('products.id', 'desc')->paginate(15); // Pagination azaldıldı
    }

    // Aktiv alt kateqoriyaları gətir
    public function getActiveSubcategories()
    {
        return ProductCategory::active()->whereNotNull('parent_id')->ordered()->get();
    }

    // Aktiv tipləri gətir
    public function getActiveTypes()
    {
        return ProductType::active()->ordered()->get();
    }

    // Aktiv tag-ları gətir
    public function getActiveTags()
    {
        return ProductTag::active()->ordered()->get();
    }
}
