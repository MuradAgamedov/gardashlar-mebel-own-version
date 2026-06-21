<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface CertificateServiceInterface extends BaseServiceInterface
{
    //

    public function getCertificates();

    public function createCertificate(array $data, array $translations, $imageFile = null);

    public function updateCertificate(\App\Models\Certificate $certificate, array $data, array $translations, $imageFile = null);

    public function deleteCertificate(\App\Models\Certificate $certificate);

    public function reorderCertificates(array $certificates);
}
