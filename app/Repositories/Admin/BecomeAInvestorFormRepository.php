<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\BecomeAInvestorFormRepositoryInterface;
use App\Models\BecomeAInvestorForm;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class BecomeAInvestorFormRepository extends BaseRepository implements BecomeAInvestorFormRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(BecomeAInvestorForm $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
