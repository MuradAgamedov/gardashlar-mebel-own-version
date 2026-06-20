<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\FaqRepositoryInterface;
use App\Interfaces\Services\Admin\FaqServiceInterface;
use App\Services\BaseService;

class FaqService extends BaseService implements FaqServiceInterface
{
    protected FaqRepositoryInterface $faqRepository;

    public function __construct(FaqRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->faqRepository = $repository;
    }
}
