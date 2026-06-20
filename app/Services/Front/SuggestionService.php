<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\SuggestionRepositoryInterface;
use App\Interfaces\Services\Front\SuggestionServiceInterface;
use App\Services\BaseService;

class SuggestionService extends BaseService implements SuggestionServiceInterface
{
    protected SuggestionRepositoryInterface $suggestionRepository;

    public function __construct(SuggestionRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->suggestionRepository = $repository;
    }
}
