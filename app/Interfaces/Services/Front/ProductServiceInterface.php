<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductServiceInterface extends BaseServiceInterface
{
    public function getProductsByProductType(int $productTypeId, int $languageId): Collection;
    public function getCompanyProductsPaginated(int $companyId, int $languageId, string $sort, int $perPage, int $page): LengthAwarePaginator;
    public function getProductsByCategories(array $categoryIds, int $languageId, int $limit = 12): Collection;
}
