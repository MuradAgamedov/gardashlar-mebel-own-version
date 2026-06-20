<?php

namespace App\Interfaces\Repositories\Front;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\BlogPost;

interface BlogPostRepositoryInterface extends BaseRepositoryInterface
{
    public function getPublishedPaginated(int $languageId, int $perPage, ?string $categorySlug, string $sort): LengthAwarePaginator;
    public function getPublishedBySlug(string $slug, int $languageId): ?BlogPost;
}
