<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\TestimonialRepositoryInterface;
use App\Interfaces\Services\Front\TestimonialServiceInterface;
use App\Services\BaseService;

class TestimonialService extends BaseService implements TestimonialServiceInterface
{
    protected TestimonialRepositoryInterface $testimonialRepository;

    public function __construct(TestimonialRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->testimonialRepository = $repository;
    }
}
