<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ActionRepositoryInterface;
use App\Models\Action;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ActionRepository extends BaseRepository implements ActionRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Action $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}