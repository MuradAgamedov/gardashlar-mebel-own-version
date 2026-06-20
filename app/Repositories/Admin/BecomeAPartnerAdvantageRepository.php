<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\BecomeAPartnerAdvantageRepositoryInterface;
use App\Models\BecomeAPartnerAdvantage;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class BecomeAPartnerAdvantageRepository extends BaseRepository implements BecomeAPartnerAdvantageRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(BecomeAPartnerAdvantage $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
