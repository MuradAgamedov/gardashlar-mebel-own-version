<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\RefundPolicyRepositoryInterface;
use App\Models\RefundPolicy;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class RefundPolicyRepository extends BaseRepository implements RefundPolicyRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(RefundPolicy $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
