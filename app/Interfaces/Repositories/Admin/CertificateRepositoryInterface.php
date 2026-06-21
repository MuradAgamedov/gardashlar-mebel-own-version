<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface CertificateRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getCertificates();

    public function createCertificate(array $data, array $translations, $imageFile = null);

    public function updateCertificate(\App\Models\Certificate $certificate, array $data, array $translations, $imageFile = null);

    public function deleteCertificate(\App\Models\Certificate $certificate);

    public function reorderCertificates(array $certificates);
}
