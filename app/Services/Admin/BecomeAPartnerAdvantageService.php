<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BecomeAPartnerAdvantageRepositoryInterface;
use App\Interfaces\Services\Admin\BecomeAPartnerAdvantageServiceInterface;
use App\Services\BaseService;

class BecomeAPartnerAdvantageService extends BaseService implements BecomeAPartnerAdvantageServiceInterface
{
    protected BecomeAPartnerAdvantageRepositoryInterface $BecomeAPartnerAdvantageRepository;

    public function __construct(BecomeAPartnerAdvantageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->BecomeAPartnerAdvantageRepository = $repository;
    }

    public function getAdvantages()
    {
        return $this->BecomeAPartnerAdvantageRepository->getAdvantages();
    }

    public function createAdvantage(array $data, array $translations, $iconFile = null)
    {
        return $this->BecomeAPartnerAdvantageRepository->createAdvantage($data, $translations, $iconFile);
    }

    public function updateAdvantage(\App\Models\BecomeAPartnerAdvantage $advantage, array $data, array $translations, $iconFile = null)
    {
        return $this->BecomeAPartnerAdvantageRepository->updateAdvantage($advantage, $data, $translations, $iconFile);
    }

    public function deleteAdvantage(\App\Models\BecomeAPartnerAdvantage $advantage)
    {
        return $this->BecomeAPartnerAdvantageRepository->deleteAdvantage($advantage);
    }

    public function reorderAdvantages(array $advantages)
    {
        return $this->BecomeAPartnerAdvantageRepository->reorderAdvantages($advantages);
    }

    public function deleteSelected(array $ids)
    {
        return $this->BecomeAPartnerAdvantageRepository->deleteSelected($ids);
    }
}
