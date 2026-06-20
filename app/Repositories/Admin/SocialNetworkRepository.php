<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\SocialNetworkRepositoryInterface;
use App\Models\SocialNetwork;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class SocialNetworkRepository extends BaseRepository implements SocialNetworkRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(SocialNetwork $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
