<?php
namespace App\Repositories;

use App\Models\ProductType;
use App\Repositories\Global\BaseRepository;

class ProductTypeRepository extends BaseRepository
{

    public function __construct(public ProductType $productType)
    {
    }

    // Tipləri filtrlə və gətir
    public function getTypes($request)
    {
        $query = $this->productType->with('translates.language');
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('translates', function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%');
            });
        }
        
        return $query->ordered()->get();
    }
}
