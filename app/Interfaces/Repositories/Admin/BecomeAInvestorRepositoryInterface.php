<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface BecomeAInvestorRepositoryInterface extends BaseRepositoryInterface
{
    public function updateBecomeAInvestor($becomeAInvestor, array $translations);
}
