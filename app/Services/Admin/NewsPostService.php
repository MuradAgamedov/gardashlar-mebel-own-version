<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\NewsPostRepositoryInterface;
use App\Interfaces\Services\Admin\NewsPostServiceInterface;
use App\Services\BaseService;

class NewsPostService extends BaseService implements NewsPostServiceInterface
{
    protected NewsPostRepositoryInterface $NewsPostRepository;

    public function __construct(NewsPostRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->NewsPostRepository = $repository;
    }
}
