<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\NewsImageRepositoryInterface;
use App\Interfaces\Services\Admin\NewsImageServiceInterface;
use App\Services\BaseService;

class NewsImageService extends BaseService implements NewsImageServiceInterface
{
    protected NewsImageRepositoryInterface $NewsImageRepository;

    public function __construct(NewsImageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->NewsImageRepository = $repository;
    }
}
