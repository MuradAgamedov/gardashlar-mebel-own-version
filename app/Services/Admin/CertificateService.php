<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\CertificateRepositoryInterface;
use App\Interfaces\Services\Admin\CertificateServiceInterface;
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
