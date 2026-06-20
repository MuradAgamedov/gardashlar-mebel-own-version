<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\SuggestAPlaceRepositoryInterface;
use App\Interfaces\Services\Front\SuggestAPlaceServiceInterface;
use App\Services\BaseService;

class SuggestAPlaceService extends BaseService implements SuggestAPlaceServiceInterface
{
    protected SuggestAPlaceRepositoryInterface $SuggestAPlaceRepository;

    public function __construct(SuggestAPlaceRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $SuggestAPlaceRepository = $repository;
    }
}
