<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\CertificateRepositoryInterface;
use App\Models\Certificate;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class CertificateRepository extends BaseRepository implements CertificateRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Certificate $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
