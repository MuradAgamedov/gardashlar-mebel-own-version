<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductTypeRepositoryInterface;
use App\Models\ProductType;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductTypeRepository extends BaseRepository implements ProductTypeRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductType $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

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
