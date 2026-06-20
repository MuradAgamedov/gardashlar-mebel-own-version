<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\BlogPostRepositoryInterface;
use App\Models\BlogPost;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BlogPostRepository extends BaseRepository implements BlogPostRepositoryInterface
{
    public function __construct(BlogPost $model)
    {
        parent::__construct($model);
    }

    public function getPublishedPaginated(int $languageId, int $perPage, ?string $categorySlug, string $sort): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->published()
            ->with([
                'translates' => fn($q) => $q->where('language_id', $languageId),
                'category.translates' => fn($q) => $q->where('language_id', $languageId),
            ]);

        if ($categorySlug) {
            $query->whereHas('category.translates', fn($q) => $q->where('slug', $categorySlug));
        }

        $query->orderBy('published_at', $sort === 'oldest' ? 'asc' : 'desc');

        return $query->paginate($perPage);
    }

    public function getPublishedBySlug(string $slug, int $languageId): ?BlogPost
    {
        return $this->model->newQuery()
            ->published()
            ->with([
                'translates.language',
                'category.translates.language',
                'images',
            ])
            ->whereHas('translates', fn($q) => $q->where('slug', $slug))
            ->first();
    }
}
