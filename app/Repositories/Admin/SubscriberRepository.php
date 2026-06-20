<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\SubscriberRepositoryInterface;
use App\Models\Subscriber;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class SubscriberRepository extends BaseRepository implements SubscriberRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Subscriber $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
