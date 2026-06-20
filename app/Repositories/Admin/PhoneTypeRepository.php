<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\PhoneTypeRepositoryInterface;
use App\Models\PhoneType;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class PhoneTypeRepository extends BaseRepository implements PhoneTypeRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(PhoneType $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
