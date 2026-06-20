<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\TestimonialRepositoryInterface;
use App\Models\Testimonial;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class TestimonialRepository extends BaseRepository implements TestimonialRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Testimonial $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
