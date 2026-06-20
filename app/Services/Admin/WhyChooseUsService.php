<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\WhyChooseUsRepositoryInterface;
use App\Interfaces\Services\Admin\WhyChooseUsServiceInterface;
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
