<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\BecomeAPartnerFormRepositoryInterface;
use App\Models\BecomeAPartnerForm;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class BecomeAPartnerFormRepository extends BaseRepository implements BecomeAPartnerFormRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(BecomeAPartnerForm $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
