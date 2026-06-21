<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\DictionaryRepositoryInterface;
use App\Interfaces\Services\Admin\DictionaryServiceInterface;
use App\Services\BaseService;

class DictionaryService extends BaseService implements DictionaryServiceInterface
{
    protected DictionaryRepositoryInterface $dictionaryRepository;

    public function __construct(DictionaryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->dictionaryRepository = $repository;
    }

    public function getDictionaries($request)
    {
        return $this->dictionaryRepository->getDictionaries($request);
    }
}
