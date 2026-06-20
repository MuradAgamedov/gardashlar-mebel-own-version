<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\BecomeAPartnerRepositoryInterface;
use App\Models\BecomeAPartner;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class BecomeAPartnerRepository extends BaseRepository implements BecomeAPartnerRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(BecomeAPartner $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
