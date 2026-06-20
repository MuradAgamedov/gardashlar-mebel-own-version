<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Vacancy;

interface VacancyServiceInterface extends BaseServiceInterface
{
    public function getActiveNotExpiredOrdered(int $languageId): Collection;
    public function getVacancyBySlug(string $slug): ?Vacancy;
}
