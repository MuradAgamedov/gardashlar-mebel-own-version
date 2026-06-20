<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\SuggestionRepositoryInterface;
use App\Models\Suggestion;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class SuggestionRepository extends BaseRepository implements SuggestionRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Suggestion $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
