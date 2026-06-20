<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\WhyChooseUsSliderRepositoryInterface;
use App\Interfaces\Services\Admin\WhyChooseUsSliderServiceInterface;
use App\Services\BaseService;

class WhyChooseUsSliderService extends BaseService implements WhyChooseUsSliderServiceInterface
{
    protected WhyChooseUsSliderRepositoryInterface $WhyChooseUsSliderRepository;

    public function __construct(WhyChooseUsSliderRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->WhyChooseUsSliderRepository = $repository;
    }
}
