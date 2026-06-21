<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface BecomeAPartnerServiceInterface extends BaseServiceInterface
{
    public function updateBecomeAPartner($becomeAPartner, array $translations);
}
