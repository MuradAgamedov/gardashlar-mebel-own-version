<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\PhoneRepositoryInterface;
use App\Models\Phone;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class PhoneRepository extends BaseRepository implements PhoneRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Phone $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
