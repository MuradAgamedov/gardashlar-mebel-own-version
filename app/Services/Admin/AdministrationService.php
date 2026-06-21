<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\AdministrationRepositoryInterface;
use App\Interfaces\Services\Admin\AdministrationServiceInterface;
use App\Services\BaseService;

class AdministrationService extends BaseService implements AdministrationServiceInterface
{
    protected AdministrationRepositoryInterface $administrationRepository;

    public function __construct(AdministrationRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->administrationRepository = $repository;
    }

    public function getAdministrations()
    {
        return $this->administrationRepository->getAdministrations();
    }

    public function createAdministration(array $data, array $translates, $imageFile)
    {
        return $this->administrationRepository->createAdministration($data, $translates, $imageFile);
    }

    public function updateAdministration(\App\Models\Administration $administration, array $data, array $translates, $imageFile = null)
    {
        return $this->administrationRepository->updateAdministration($administration, $data, $translates, $imageFile);
    }

    public function deleteAdministration(\App\Models\Administration $administration)
    {
        return $this->administrationRepository->deleteAdministration($administration);
    }

    public function reorderAdministrations(array $order)
    {
        return $this->administrationRepository->reorderAdministrations($order);
    }
}
