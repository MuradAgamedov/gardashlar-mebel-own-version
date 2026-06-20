<?php

namespace App\Interfaces\Repositories\Front;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\BlogCategory;

interface BlogCategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getActivePaginatedWithCount(int $languageId, int $perPage): LengthAwarePaginator;
    public function getActiveBySlug(string $slug, int $languageId): ?BlogCategory;
}
