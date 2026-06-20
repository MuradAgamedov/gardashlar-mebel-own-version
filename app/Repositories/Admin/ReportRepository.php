<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ReportRepositoryInterface;
use App\Models\Report;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ReportRepository extends BaseRepository implements ReportRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Report $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
