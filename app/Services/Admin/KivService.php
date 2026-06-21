<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\KivRepositoryInterface;
use App\Interfaces\Services\Admin\KivServiceInterface;
use App\Services\BaseService;

class KivService extends BaseService implements KivServiceInterface
{
    protected KivRepositoryInterface $KivRepository;

    public function __construct(KivRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->KivRepository = $repository;
    }

    public function getKivs($request)
    {
        return $this->KivRepository->getKivs($request);
    }

    public function getCategories()
    {
        return $this->KivRepository->getCategories();
    }

    public function createKiv(array $data, $imageFile, $imageService)
    {
        return $this->KivRepository->createKiv($data, $imageFile, $imageService);
    }

    public function updateKiv(\App\Models\Kiv $kiv, array $data, $imageFile, $imageService)
    {
        return $this->KivRepository->updateKiv($kiv, $data, $imageFile, $imageService);
    }

    public function deleteKiv(\App\Models\Kiv $kiv)
    {
        return $this->KivRepository->deleteKiv($kiv);
    }

    public function reorderKivs(array $kivs)
    {
        return $this->KivRepository->reorderKivs($kivs);
    }
}
