<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\PrivacyPolicyRepositoryInterface;
use App\Models\PrivacyPolicy;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class PrivacyPolicyRepository extends BaseRepository implements PrivacyPolicyRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(PrivacyPolicy $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
