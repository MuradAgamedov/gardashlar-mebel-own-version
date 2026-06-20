<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\DictionaryRepositoryInterface;
use App\Interfaces\Services\Front\DictionaryServiceInterface;
use App\Services\BaseService;

class DictionaryService extends BaseService implements DictionaryServiceInterface
{
    protected DictionaryRepositoryInterface $dictionaryRepository;

    public function __construct(DictionaryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->dictionaryRepository = $repository;
    }
}
