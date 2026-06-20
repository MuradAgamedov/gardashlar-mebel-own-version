<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\PageRepositoryInterface;
use App\Interfaces\Services\Front\PageServiceInterface;
use App\Services\BaseService;

class PageService extends BaseService implements PageServiceInterface
{
    protected PageRepositoryInterface $pageRepository;

    public function __construct(PageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->pageRepository = $repository;
    }
}
