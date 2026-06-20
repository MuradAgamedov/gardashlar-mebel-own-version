<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ServiceRepositoryInterface;
use App\Interfaces\Services\Admin\ServiceServiceInterface;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ServiceService extends BaseService implements ServiceServiceInterface
{
    protected ServiceRepositoryInterface $serviceRepository;

    public function __construct(ServiceRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->serviceRepository = $repository;
    }

    public function getServices($request): Collection
    {
        return $this->serviceRepository->getServices($request);
    }

    public function createService(array $data, array $translates): Model
    {
        return $this->serviceRepository->createService($data, $translates);
    }

    public function updateService(int $id, array $data, array $translates): Model
    {
        return $this->serviceRepository->updateService($id, $data, $translates);
    }

    public function updateServiceOrder(array $services): bool
    {
        return $this->serviceRepository->updateServiceOrder($services);
    }

    public function bulkDelete(array $ids): int
    {
        return $this->serviceRepository->bulkDelete($ids);
    }
}
