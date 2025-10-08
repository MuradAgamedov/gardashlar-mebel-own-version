<?php
namespace App\Repositories;

use App\Models\ProductColor;
use App\Repositories\Global\BaseRepository;

class ProductColorRepository extends BaseRepository
{

    public function __construct(public ProductColor $productColor)
    {
    }

    // Rəngləri filtrlə və gətir
    public function getProductColors($request)
    {
        $query = $this->productColor->query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('color_code', 'like', "%{$search}%");
        }
        
        return $query->orderBy('created_at', 'desc')->paginate(30);
    }
}
