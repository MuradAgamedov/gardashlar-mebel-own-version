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

    public function getPages()
    {
        return $this->pageRepository->getPages();
    }

    public function findPage($id)
    {
        return $this->pageRepository->findPage($id);
    }

    public function createPage(array $data, array $translates)
    {
        return $this->pageRepository->createPage($data, $translates);
    }

    public function updatePage($id, array $data, array $translates)
    {
        return $this->pageRepository->updatePage($id, $data, $translates);
    }

    public function deletePage($id)
    {
        return $this->pageRepository->deletePage($id);
    }
}
