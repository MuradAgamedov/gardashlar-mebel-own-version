<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface BecomeAPartnerRepositoryInterface extends BaseRepositoryInterface
{
    public function updateBecomeAPartner($becomeAPartner, array $translations);
}
