<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\SuggestAPlaceFormRepositoryInterface;
use App\Interfaces\Services\Admin\SuggestAPlaceFormServiceInterface;
use App\Services\BaseService;

class SuggestAPlaceFormService extends BaseService implements SuggestAPlaceFormServiceInterface
{
    protected SuggestAPlaceFormRepositoryInterface $SuggestAPlaceFormRepository;

    public function __construct(SuggestAPlaceFormRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->SuggestAPlaceFormRepository = $repository;
    }
}
