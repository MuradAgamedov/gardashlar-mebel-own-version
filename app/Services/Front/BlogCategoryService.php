<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\BlogCategoryRepositoryInterface;
use App\Interfaces\Services\Front\BlogCategoryServiceInterface;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\BlogCategory;

class BlogCategoryService extends BaseService implements BlogCategoryServiceInterface
{
    protected BlogCategoryRepositoryInterface $blogCategoryRepository;

    public function __construct(BlogCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->blogCategoryRepository = $repository;
    }

    public function getActivePaginatedWithCount(int $languageId, int $perPage): LengthAwarePaginator
    {
        return $this->blogCategoryRepository->getActivePaginatedWithCount($languageId, $perPage);
    }

    public function getActiveBySlug(string $slug, int $languageId): ?BlogCategory
    {
        return $this->blogCategoryRepository->getActiveBySlug($slug, $languageId);
    }
}
