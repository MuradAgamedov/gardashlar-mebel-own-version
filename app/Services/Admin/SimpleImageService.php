<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\SimpleImageRepositoryInterface;
use App\Interfaces\Services\Admin\SimpleImageServiceInterface;
use App\Services\BaseService;

class SimpleImageService extends BaseService implements SimpleImageServiceInterface
{
    protected SimpleImageRepositoryInterface $simpleImageRepository;

    public function __construct(SimpleImageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->simpleImageRepository = $repository;
    }

    public function getImages()
    {
        return $this->simpleImageRepository->getImages();
    }
}
