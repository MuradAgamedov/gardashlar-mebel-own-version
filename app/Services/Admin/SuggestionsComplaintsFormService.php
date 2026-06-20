<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\SuggestionsComplaintsFormRepositoryInterface;
use App\Interfaces\Services\Admin\SuggestionsComplaintsFormServiceInterface;
use App\Services\BaseService;

class SuggestionsComplaintsFormService extends BaseService implements SuggestionsComplaintsFormServiceInterface
{
    protected SuggestionsComplaintsFormRepositoryInterface $SuggestionsComplaintsFormRepository;

    public function __construct(SuggestionsComplaintsFormRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->SuggestionsComplaintsFormRepository = $repository;
    }
}
