<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\CertificateRepositoryInterface;
use App\Interfaces\Services\Front\CertificateServiceInterface;
use App\Services\BaseService;

class CertificateService extends BaseService implements CertificateServiceInterface
{
    protected CertificateRepositoryInterface $certificateRepository;

    public function __construct(CertificateRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->certificateRepository = $repository;
    }
}
