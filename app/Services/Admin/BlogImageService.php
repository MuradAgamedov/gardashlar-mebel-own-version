<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BlogImageRepositoryInterface;
use App\Interfaces\Services\Admin\BlogImageServiceInterface;
use App\Services\BaseService;

class BlogImageService extends BaseService implements BlogImageServiceInterface
{
    protected BlogImageRepositoryInterface $blogImageRepository;

    public function __construct(BlogImageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->blogImageRepository = $repository;
    }
}
