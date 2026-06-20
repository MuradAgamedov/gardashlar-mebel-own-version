<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\HeroProductRepositoryInterface;
use App\Models\HeroProduct;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class HeroProductRepository extends BaseRepository implements HeroProductRepositoryInterface
{
    public function __construct(HeroProduct $model)
    {
        parent::__construct($model);
    }

    public function getActiveOrderedByCategory(int $categoryId, int $languageId): Collection
    {
        return $this->model->newQuery()
            ->active()
            ->ordered()
            ->with([
                'product.translates' => fn($q) => $q->where('language_id', $languageId),
                'product.images' => fn($q) => $q->active()->ordered(),
                'product.categories.translates' => fn($q) => $q->where('language_id', $languageId),
            ])
            ->get()
            ->filter(fn($heroProduct) => $heroProduct->product && $heroProduct->product->categories->contains('id', $categoryId));
    }
}
