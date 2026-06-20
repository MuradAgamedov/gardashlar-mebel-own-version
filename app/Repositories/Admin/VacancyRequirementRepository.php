<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\VacancyRequirementRepositoryInterface;
use App\Models\VacancyRequirement;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class VacancyRequirementRepository extends BaseRepository implements VacancyRequirementRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(VacancyRequirement $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
