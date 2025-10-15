<?php

namespace App\Repositories;

use App\Models\ProjectImage;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\DB;

class ProjectImageRepository extends BaseRepository
{
    protected $imageService;

    public function __construct(
        public ProjectImage $projectImage,
        \App\Services\ImageService $imageService
    ) {
        $this->imageService = $imageService;
    }

    public function getOrderedImages($projectId)
    {
        return $this->projectImage->where('project_id', $projectId)
            ->orderBy('order')
            ->get();
    }

    public function getMaxOrder($projectId)
    {
        return $this->projectImage->where('project_id', $projectId)
            ->max('order') ?? 0;
    }
}
