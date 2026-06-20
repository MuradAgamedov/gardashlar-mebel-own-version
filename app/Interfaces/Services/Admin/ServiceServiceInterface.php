<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ServiceServiceInterface extends BaseServiceInterface
{
    public function getServices($request): Collection;
    public function createService(array $data, array $translates): Model;
    public function updateService(int $id, array $data, array $translates): Model;
    public function updateServiceOrder(array $services): bool;
    public function bulkDelete(array $ids): int;
}
