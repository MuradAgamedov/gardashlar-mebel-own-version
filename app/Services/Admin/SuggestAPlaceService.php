<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\SuggestAPlaceRepositoryInterface;
use App\Interfaces\Services\Admin\SuggestAPlaceServiceInterface;
use App\Services\BaseService;

class SuggestAPlaceService extends BaseService implements SuggestAPlaceServiceInterface
{
    protected SuggestAPlaceRepositoryInterface $SuggestAPlaceRepository;

    public function __construct(SuggestAPlaceRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->SuggestAPlaceRepository = $repository;
    }
}
