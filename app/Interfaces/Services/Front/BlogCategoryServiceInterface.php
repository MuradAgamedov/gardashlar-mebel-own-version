<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\BlogCategory;

interface BlogCategoryServiceInterface extends BaseServiceInterface
{
    public function getActivePaginatedWithCount(int $languageId, int $perPage): LengthAwarePaginator;
    public function getActiveBySlug(string $slug, int $languageId): ?BlogCategory;
}
