<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\SimpleImageRepositoryInterface;
use App\Interfaces\Services\Front\SimpleImageServiceInterface;
use App\Services\BaseService;

class SimpleImageService extends BaseService implements SimpleImageServiceInterface
{
    protected SimpleImageRepositoryInterface $simpleImageRepository;

    public function __construct(SimpleImageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->simpleImageRepository = $repository;
    }
}
