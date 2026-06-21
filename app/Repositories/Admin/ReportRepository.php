<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ReportRepositoryInterface;
use App\Models\Language;
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

    public function getAllReports()
    {
        return $this->report->with('translates.language')
            ->orderBy('order')
            ->get();
    }

    public function getActiveLanguages()
    {
        return Language::where('status', 1)->get();
    }
}
