<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface BecomeAPartnerAdvantageServiceInterface extends BaseServiceInterface
{
    //

    public function getAdvantages();

    public function createAdvantage(array $data, array $translations, $iconFile = null);

    public function updateAdvantage(\App\Models\BecomeAPartnerAdvantage $advantage, array $data, array $translations, $iconFile = null);

    public function deleteAdvantage(\App\Models\BecomeAPartnerAdvantage $advantage);

    public function reorderAdvantages(array $advantages);

    public function deleteSelected(array $ids);
}
