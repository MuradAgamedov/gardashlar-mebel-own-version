<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\ProductRepositoryInterface;
use App\Interfaces\Services\Front\ProductServiceInterface;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductService extends BaseService implements ProductServiceInterface
{
    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->productRepository = $repository;
    }

    public function getProductsByProductType(int $productTypeId, int $languageId): Collection
    {
        return $this->productRepository->getProductsByProductType($productTypeId, $languageId);
    }

    public function getCompanyProductsPaginated(int $companyId, int $languageId, string $sort, int $perPage, int $page): LengthAwarePaginator
    {
        return $this->productRepository->getCompanyProductsPaginated($companyId, $languageId, $sort, $perPage, $page);
    }

    public function getProductsByCategories(array $categoryIds, int $languageId, int $limit = 12): Collection
    {
        return $this->productRepository->getProductsByCategories($categoryIds, $languageId, $limit);
    }
}
