<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BlogCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\BlogCategoryServiceInterface;
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
