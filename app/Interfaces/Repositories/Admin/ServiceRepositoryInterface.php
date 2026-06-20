<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ServiceRepositoryInterface extends BaseRepositoryInterface
{
    public function getServices($request): Collection;
    public function createService(array $data, array $translates): Model;
    public function updateService(int $id, array $data, array $translates): Model;
    public function updateServiceOrder(array $services): bool;
    public function bulkDelete(array $ids): int;
}
