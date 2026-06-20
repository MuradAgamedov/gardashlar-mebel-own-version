<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\CreditCardRepositoryInterface;
use App\Models\CreditCard;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class CreditCardRepository extends BaseRepository implements CreditCardRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(CreditCard $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
