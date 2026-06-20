<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\SuggestionsComplaintsRepositoryInterface;
use App\Interfaces\Services\Front\SuggestionsComplaintsServiceInterface;
use App\Services\BaseService;

class SuggestionsComplaintsService extends BaseService implements SuggestionsComplaintsServiceInterface
{
    protected SuggestionsComplaintsRepositoryInterface $suggestionsComplaintsRepository;

    public function __construct(SuggestionsComplaintsRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->suggestionsComplaintsRepository = $repository;
    }
}
