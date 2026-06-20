<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\BlogPostRepositoryInterface;
use App\Interfaces\Services\Front\BlogPostServiceInterface;
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
