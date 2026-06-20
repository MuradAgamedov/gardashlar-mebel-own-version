<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\BlogPostRepositoryInterface;
use App\Interfaces\Services\Front\BlogPostServiceInterface;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\BlogPost;

class BlogPostService extends BaseService implements BlogPostServiceInterface
{
    protected BlogPostRepositoryInterface $blogPostRepository;

    public function __construct(BlogPostRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->blogPostRepository = $repository;
    }

    public function getPublishedPaginated(int $languageId, int $perPage, ?string $categorySlug, string $sort): LengthAwarePaginator
    {
        return $this->blogPostRepository->getPublishedPaginated($languageId, $perPage, $categorySlug, $sort);
    }

    public function getPublishedBySlug(string $slug, int $languageId): ?BlogPost
    {
        return $this->blogPostRepository->getPublishedBySlug($slug, $languageId);
    }
}
