<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\PopupAdRepositoryInterface;
use App\Interfaces\Services\Front\PopupAdServiceInterface;
use App\Services\BaseService;

class PopupAdService extends BaseService implements PopupAdServiceInterface
{
    protected PopupAdRepositoryInterface $popupAdRepository;

    public function __construct(PopupAdRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->popupAdRepository = $repository;
    }
}
