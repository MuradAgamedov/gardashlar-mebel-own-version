<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\SeoPageRepositoryInterface;
use App\Interfaces\Services\Front\SeoPageServiceInterface;
use App\Services\BaseService;

class SeoPageService extends BaseService implements SeoPageServiceInterface
{
    protected SeoPageRepositoryInterface $seoPageRepository;

    public function __construct(SeoPageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->seoPageRepository = $repository;
    }
}
