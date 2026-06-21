<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface BecomeAInvestorAdvantageServiceInterface extends BaseServiceInterface
{
    //

    public function getAdvantages();

    public function createAdvantage(array $data, array $translations, $iconFile = null);

    public function updateAdvantage(\App\Models\BecomeAInvestorAdvantage $advantage, array $data, array $translations, $iconFile = null);

    public function deleteAdvantage(\App\Models\BecomeAInvestorAdvantage $advantage);

    public function reorderAdvantages(array $advantages);

    public function deleteSelected(array $ids);
}
