<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\LanguageRepositoryInterface;
use App\Interfaces\Services\Front\LanguageServiceInterface;
use App\Services\BaseService;

class LanguageService extends BaseService implements LanguageServiceInterface
{
    protected LanguageRepositoryInterface $languageRepository;

    public function __construct(LanguageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->languageRepository = $repository;
    }
}
