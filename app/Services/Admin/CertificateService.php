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

    public function getCertificates()
    {
        return $this->certificateRepository->getCertificates();
    }

    public function createCertificate(array $data, array $translations, $imageFile = null)
    {
        return $this->certificateRepository->createCertificate($data, $translations, $imageFile);
    }

    public function updateCertificate(\App\Models\Certificate $certificate, array $data, array $translations, $imageFile = null)
    {
        return $this->certificateRepository->updateCertificate($certificate, $data, $translations, $imageFile);
    }

    public function deleteCertificate(\App\Models\Certificate $certificate)
    {
        return $this->certificateRepository->deleteCertificate($certificate);
    }

    public function reorderCertificates(array $certificates)
    {
        return $this->certificateRepository->reorderCertificates($certificates);
    }
}
