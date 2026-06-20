<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\SuggestAPlaceFormRepositoryInterface;
use App\Models\SuggestAPlaceForm;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class SuggestAPlaceFormRepository extends BaseRepository implements SuggestAPlaceFormRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(SuggestAPlaceForm $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
