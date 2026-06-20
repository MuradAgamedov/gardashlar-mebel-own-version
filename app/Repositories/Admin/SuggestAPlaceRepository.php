<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\SuggestAPlaceRepositoryInterface;
use App\Models\SuggestAPlace;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class SuggestAPlaceRepository extends BaseRepository implements SuggestAPlaceRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(SuggestAPlace $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
