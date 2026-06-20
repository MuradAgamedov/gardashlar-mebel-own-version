<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\WhyChooseUsSliderRepositoryInterface;
use App\Interfaces\Services\Front\WhyChooseUsSliderServiceInterface;
use App\Services\BaseService;

class WhyChooseUsSliderService extends BaseService implements WhyChooseUsSliderServiceInterface
{
    protected WhyChooseUsSliderRepositoryInterface $whyChooseUsSliderRepository;

    public function __construct(WhyChooseUsSliderRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->whyChooseUsSliderRepository = $repository;
    }
}
