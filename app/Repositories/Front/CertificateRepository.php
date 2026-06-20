<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\CertificateRepositoryInterface;
use App\Models\Certificate;
use App\Repositories\BaseRepository;

class CertificateRepository extends BaseRepository implements CertificateRepositoryInterface
{
    public function __construct(Certificate $model)
    {
        parent::__construct($model);
    }
}
