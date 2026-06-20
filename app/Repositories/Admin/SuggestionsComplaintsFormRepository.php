<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\SuggestionsComplaintsFormRepositoryInterface;
use App\Models\SuggestionsComplaintsForm;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class SuggestionsComplaintsFormRepository extends BaseRepository implements SuggestionsComplaintsFormRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(SuggestionsComplaintsForm $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
