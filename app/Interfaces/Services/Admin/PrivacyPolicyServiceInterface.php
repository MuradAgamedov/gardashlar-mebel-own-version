<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface PrivacyPolicyServiceInterface extends BaseServiceInterface
{
    //

    public function getPrivacyPolicy();

    public function getOrCreatePrivacyPolicy();

    public function updatePrivacyPolicy(array $translates, $languages);
}
