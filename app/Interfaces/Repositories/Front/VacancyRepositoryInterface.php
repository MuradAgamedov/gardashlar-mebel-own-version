<?php

namespace App\Interfaces\Repositories\Front;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Vacancy;

interface VacancyRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveNotExpiredOrdered(int $languageId): Collection;
    public function getVacancyBySlug(string $slug): ?Vacancy;
}
