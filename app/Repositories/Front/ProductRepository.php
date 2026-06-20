<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\ProductRepositoryInterface;
use App\Models\Product;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getProductsByProductType(int $productTypeId, int $languageId): Collection
    {
        return $this->model->newQuery()
            ->whereHas('types', function ($q) use ($productTypeId) {
                $q->where('product_types.id', $productTypeId);
            })
            ->active()
            ->with([
                'translates' => fn ($q) => $q->where('language_id', $languageId),
                'types.translates' => fn ($q) => $q->where('language_id', $languageId),
                'images' => fn ($q) => $q->active()->ordered(),
                'images.translates' => fn ($q) => $q->where('language_id', $languageId),
                'categories.translates' => fn ($q) => $q->where('language_id', $languageId),
            ])
            ->orderBy('order')
            ->get();
    }

    public function getCompanyProductsPaginated(int $companyId, int $languageId, string $sort, int $perPage, int $page): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->whereHas('companyProducts', fn($q) => $q->where('company_id', $companyId))
            ->with([
                'translates' => fn($q) => $q->where('language_id', $languageId),
                'images' => fn($q) => $q->where('status', true)->orderBy('order'),
                'types.translates' => fn($q) => $q->where('language_id', $languageId),
                'categories.translates' => fn($q) => $q->where('language_id', $languageId),
            ])
            ->active();

        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            default => $query->orderBy('view_count', 'desc'),
        };

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getProductsByCategories(array $categoryIds, int $languageId, int $limit = 12): Collection
    {
        return $this->model->newQuery()
            ->with([
                'translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'images' => function($query) {
                    $query->where('status', true)->orderBy('order');
                },
                'types.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'categories.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }
            ])
            ->whereHas('categories', function($query) use ($categoryIds) {
                $query->whereIn('product_categories.id', $categoryIds);
            })
            ->active()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
