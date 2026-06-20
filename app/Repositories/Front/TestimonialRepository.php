<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\TestimonialRepositoryInterface;
use App\Models\Testimonial;
use App\Repositories\BaseRepository;

class TestimonialRepository extends BaseRepository implements TestimonialRepositoryInterface
{
    public function __construct(Testimonial $model)
    {
        parent::__construct($model);
    }
}
