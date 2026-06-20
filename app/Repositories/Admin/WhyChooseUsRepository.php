<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\WhyChooseUsRepositoryInterface;
use App\Models\WhyChooseUs;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class WhyChooseUsRepository extends BaseRepository implements WhyChooseUsRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(WhyChooseUs $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
