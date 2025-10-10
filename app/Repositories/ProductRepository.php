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

    // Məhsulları filtrlə və gətir
    public function getProducts($request)
    {
        $query = $this->product->with(['categories', 'types', 'translates.language', 'tags']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
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
        
        return $query->ordered()->orderBy('id', 'desc')->paginate(30);
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
