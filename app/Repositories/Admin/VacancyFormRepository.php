<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\VacancyFormRepositoryInterface;
use App\Models\VacancyForm;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class VacancyFormRepository extends BaseRepository implements VacancyFormRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(VacancyForm $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
