<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Action;

interface ActionServiceInterface extends BaseServiceInterface
{
    public function getActiveCurrentOrdered(int $languageId, ?int $companyId = null): Collection;
    public function getActiveById(int $id, int $languageId): ?Action;
}
