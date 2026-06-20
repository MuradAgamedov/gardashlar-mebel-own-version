<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\SuggestionRepositoryInterface;
use App\Interfaces\Services\Admin\SuggestionServiceInterface;
use App\Services\BaseService;

class SuggestionService extends BaseService implements SuggestionServiceInterface
{
    protected SuggestionRepositoryInterface $SuggestionRepository;

    public function __construct(SuggestionRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->SuggestionRepository = $repository;
    }
}
