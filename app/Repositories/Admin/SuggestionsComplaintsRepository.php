<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\SuggestionsComplaintsRepositoryInterface;
use App\Models\SuggestionsComplaints;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class SuggestionsComplaintsRepository extends BaseRepository implements SuggestionsComplaintsRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(SuggestionsComplaints $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
