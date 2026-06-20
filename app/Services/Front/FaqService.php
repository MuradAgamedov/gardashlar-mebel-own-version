<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\FaqRepositoryInterface;
use App\Interfaces\Services\Front\FaqServiceInterface;
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
