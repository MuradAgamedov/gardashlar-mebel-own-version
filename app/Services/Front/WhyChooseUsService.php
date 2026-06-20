<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\WhyChooseUsRepositoryInterface;
use App\Interfaces\Services\Front\WhyChooseUsServiceInterface;
use App\Services\BaseService;

class WhyChooseUsService extends BaseService implements WhyChooseUsServiceInterface
{
    protected WhyChooseUsRepositoryInterface $whyChooseUsRepository;

    public function __construct(WhyChooseUsRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->whyChooseUsRepository = $repository;
    }
}
