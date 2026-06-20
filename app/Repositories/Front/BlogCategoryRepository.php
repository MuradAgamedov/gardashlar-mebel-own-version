<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\BlogCategoryRepositoryInterface;
use App\Models\BlogCategory;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BlogCategoryRepository extends BaseRepository implements BlogCategoryRepositoryInterface
{
    public function __construct(BlogCategory $model)
    {
        parent::__construct($model);
    }

    public function getActivePaginatedWithCount(int $languageId, int $perPage): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->active()
            ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
            ->withCount(['blogPosts' => fn($q) => $q->published()])
            ->ordered()
            ->paginate($perPage);
    }

    public function getActiveBySlug(string $slug, int $languageId): ?BlogCategory
    {
        return $this->model->newQuery()
            ->active()
            ->whereHas('translates', fn($q) => $q->where('slug', $slug)->where('language_id', $languageId))
            ->with(['translates.language'])
            ->withCount(['blogPosts' => fn($q) => $q->published()])
            ->first();
    }
}
