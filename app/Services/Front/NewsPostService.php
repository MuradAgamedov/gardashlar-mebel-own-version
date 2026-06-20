<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\NewsPostRepositoryInterface;
use App\Interfaces\Services\Front\NewsPostServiceInterface;
use App\Services\BaseService;

class NewsPostService extends BaseService implements NewsPostServiceInterface
{
    protected NewsPostRepositoryInterface $newsPostRepository;

    public function __construct(NewsPostRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->newsPostRepository = $repository;
    }
}
