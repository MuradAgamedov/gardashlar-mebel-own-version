<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\SeoPageRepositoryInterface;
use App\Interfaces\Services\Admin\SeoPageServiceInterface;
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
