<?php
namespace App\Repositories;

use App\Models\Kiv;
use App\Models\KivCategory;
use App\Repositories\Global\BaseRepository;

class KivRepository extends BaseRepository
{

    public function __construct(public Kiv $kiv)
    {
    }

    // KIV-ləri filtrlə və gətir
    public function getKivs($request)
    {
        $query = $this->kiv->with('category.translates');
        
        // Category filter
        if ($request->filled('category_id')) {
            $query->where('kiv_category_id', $request->category_id);
        }
        
        return $query->orderBy('order')->get();
    }

    // KIV kateqoriyalarını gətir
    public function getCategories()
    {
        return KivCategory::with('translates')->orderBy('order')->get();
    }
}
