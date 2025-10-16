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
        // Sadəcə lazım olan əlaqələri yüklə
        $query = $this->product->select(['id', 'price', 'discount_price', 'status', 'view_count', 'created_at'])
            ->with([
                'translates' => function($q) {
                    $q->select(['product_id', 'language_id', 'title', 'slug'])
                      ->where('language_id', 1); // Sadəcə default dil
                },
                'categories' => function($q) {
                    $q->select(['id'])->with(['translates' => function($tq) {
                        $tq->select(['product_category_id', 'title'])->where('language_id', 1);
                    }]);
                },
                'types' => function($q) {
                    $q->select(['id'])->with(['translates' => function($tq) {
                        $tq->select(['product_type_id', 'title'])->where('language_id', 1);
                    }]);
                }
            ]);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->where('language_id', 1); // Default dildə axtarış
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
            $query->where('status', $request->input('status'));
        }
        
        return $query->orderBy('id', 'desc')->paginate(15); // Pagination azaldıldı
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
