<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\PageRepositoryInterface;
use App\Interfaces\Services\Admin\PageServiceInterface;
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
