<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\BecomeAInvestorAdvantageRepositoryInterface;
use App\Models\BecomeAInvestorAdvantage;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class BecomeAInvestorAdvantageRepository extends BaseRepository implements BecomeAInvestorAdvantageRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(BecomeAInvestorAdvantage $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
