<?php
namespace App\Repositories;

use App\Models\Certificate;
use App\Repositories\Global\BaseRepository;

class CertificateRepository extends BaseRepository
{

    public function __construct(public Certificate $certificate)
    {
    }

    // məsələn bütün məlumatları gətirmək üçün
    public function getCertificates()
    {
        return $this->GetData($this->certificate, ['translates.language'])->get();
    }
}
