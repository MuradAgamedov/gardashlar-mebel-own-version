<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\SuggestionsComplaintsRepositoryInterface;
use App\Interfaces\Services\Admin\SuggestionsComplaintsServiceInterface;
use App\Services\BaseService;

class SuggestionsComplaintsService extends BaseService implements SuggestionsComplaintsServiceInterface
{
    protected SuggestionsComplaintsRepositoryInterface $SuggestionsComplaintsRepository;

    public function __construct(SuggestionsComplaintsRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->SuggestionsComplaintsRepository = $repository;
    }
}
