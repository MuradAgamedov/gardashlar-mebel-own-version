<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface PrivacyPolicyRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getPrivacyPolicy();

    public function getOrCreatePrivacyPolicy();

    public function updatePrivacyPolicy(array $translates, $languages);
}
