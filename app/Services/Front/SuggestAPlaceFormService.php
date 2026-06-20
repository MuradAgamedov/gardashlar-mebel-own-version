<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\SuggestAPlaceFormRepositoryInterface;
use App\Interfaces\Services\Front\SuggestAPlaceFormServiceInterface;
use App\Services\BaseService;

class SuggestAPlaceFormService extends BaseService implements SuggestAPlaceFormServiceInterface
{
    protected SuggestAPlaceFormRepositoryInterface $suggestAPlaceFormRepository;

    public function __construct(SuggestAPlaceFormRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->suggestAPlaceFormRepository = $repository;
    }
}
