<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BecomeAInvestorAdvantageRepositoryInterface;
use App\Interfaces\Services\Admin\BecomeAInvestorAdvantageServiceInterface;
use App\Services\BaseService;

class BecomeAInvestorAdvantageService extends BaseService implements BecomeAInvestorAdvantageServiceInterface
{
    protected BecomeAInvestorAdvantageRepositoryInterface $BecomeAInvestorAdvantageRepository;

    public function __construct(BecomeAInvestorAdvantageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->BecomeAInvestorAdvantageRepository = $repository;
    }

    public function getAdvantages()
    {
        return $this->BecomeAInvestorAdvantageRepository->getAdvantages();
    }

    public function createAdvantage(array $data, array $translations, $iconFile = null)
    {
        return $this->BecomeAInvestorAdvantageRepository->createAdvantage($data, $translations, $iconFile);
    }

    public function updateAdvantage(\App\Models\BecomeAInvestorAdvantage $advantage, array $data, array $translations, $iconFile = null)
    {
        return $this->BecomeAInvestorAdvantageRepository->updateAdvantage($advantage, $data, $translations, $iconFile);
    }

    public function deleteAdvantage(\App\Models\BecomeAInvestorAdvantage $advantage)
    {
        return $this->BecomeAInvestorAdvantageRepository->deleteAdvantage($advantage);
    }

    public function reorderAdvantages(array $advantages)
    {
        return $this->BecomeAInvestorAdvantageRepository->reorderAdvantages($advantages);
    }

    public function deleteSelected(array $ids)
    {
        return $this->BecomeAInvestorAdvantageRepository->deleteSelected($ids);
    }
}
