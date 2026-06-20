<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\AllSeoPagesRepositoryInterface;
use App\Models\AllSeoPages;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class AllSeoPagesRepository extends BaseRepository implements AllSeoPagesRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(AllSeoPages $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
