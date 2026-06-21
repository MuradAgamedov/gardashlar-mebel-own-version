<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface BecomeAInvestorServiceInterface extends BaseServiceInterface
{
    public function updateBecomeAInvestor($becomeAInvestor, array $translations);
}
