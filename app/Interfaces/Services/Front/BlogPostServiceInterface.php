<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\BlogPost;

interface BlogPostServiceInterface extends BaseServiceInterface
{
    public function getPublishedPaginated(int $languageId, int $perPage, ?string $categorySlug, string $sort): LengthAwarePaginator;
    public function getPublishedBySlug(string $slug, int $languageId): ?BlogPost;
}
