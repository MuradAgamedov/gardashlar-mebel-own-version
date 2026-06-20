<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\PopupAdRepositoryInterface;
use App\Interfaces\Services\Admin\PopupAdServiceInterface;
use App\Services\BaseService;

class PopupAdService extends BaseService implements PopupAdServiceInterface
{
    protected PopupAdRepositoryInterface $PopupAdRepository;

    public function __construct(PopupAdRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->PopupAdRepository = $repository;
    }
}
