<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BlogPostRepositoryInterface;
use App\Interfaces\Services\Admin\BlogPostServiceInterface;
use App\Services\BaseService;

class BlogPostService extends BaseService implements BlogPostServiceInterface
{
    protected BlogPostRepositoryInterface $blogPostRepository;

    public function __construct(BlogPostRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->blogPostRepository = $repository;
    }
}
