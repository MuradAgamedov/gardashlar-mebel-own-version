<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\BlogCategoryRepositoryInterface;
use App\Interfaces\Services\Front\BlogCategoryServiceInterface;
use App\Services\BaseService;

class BlogCategoryService extends BaseService implements BlogCategoryServiceInterface
{
    protected BlogCategoryRepositoryInterface $blogCategoryRepository;

    public function __construct(BlogCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->blogCategoryRepository = $repository;
    }
}
