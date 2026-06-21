<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface BecomeAPartnerAdvantageRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getAdvantages();

    public function createAdvantage(array $data, array $translations, $iconFile = null);

    public function updateAdvantage(\App\Models\BecomeAPartnerAdvantage $advantage, array $data, array $translations, $iconFile = null);

    public function deleteAdvantage(\App\Models\BecomeAPartnerAdvantage $advantage);

    public function reorderAdvantages(array $advantages);

    public function deleteSelected(array $ids);
}
