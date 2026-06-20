<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\SuggestionsComplaintsFormRepositoryInterface;
use App\Interfaces\Services\Front\SuggestionsComplaintsFormServiceInterface;
use App\Services\BaseService;

class SuggestionsComplaintsFormService extends BaseService implements SuggestionsComplaintsFormServiceInterface
{
    protected SuggestionsComplaintsFormRepositoryInterface $suggestionsComplaintsFormRepository;

    public function __construct(SuggestionsComplaintsFormRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->suggestionsComplaintsFormRepository = $repository;
    }
}
