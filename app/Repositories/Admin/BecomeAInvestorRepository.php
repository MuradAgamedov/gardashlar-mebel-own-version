<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\BecomeAInvestorRepositoryInterface;
use App\Models\BecomeAInvestor;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class BecomeAInvestorRepository extends BaseRepository implements BecomeAInvestorRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(BecomeAInvestor $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
