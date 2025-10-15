<?php
namespace App\Repositories;

use App\Models\Testimonial;
use App\Repositories\Global\BaseRepository;

class TestimonialRepository extends BaseRepository
{

    public function __construct(public Testimonial $testimonial)
    {
    }

    public function getTestimonials()
    {
        return $this->GetData($this->testimonial, ['translates.language'])->orderBy('order')->get();
    }
}
