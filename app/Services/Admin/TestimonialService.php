<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\TestimonialRepositoryInterface;
use App\Interfaces\Services\Admin\TestimonialServiceInterface;
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
