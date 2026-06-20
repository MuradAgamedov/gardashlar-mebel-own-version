<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\VacancyRepositoryInterface;
use App\Models\Vacancy;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class VacancyRepository extends BaseRepository implements VacancyRepositoryInterface
{
    public function __construct(Vacancy $model)
    {
        parent::__construct($model);
    }

    public function getActiveNotExpiredOrdered(int $languageId): Collection
    {
        return $this->model->newQuery()
            ->with([
                'translates' => fn($q) => $q->where('language_id', $languageId),
                'requirements.translates' => fn($q) => $q->where('language_id', $languageId),
            ])
            ->active()
            ->notExpired()
            ->ordered()
            ->get();
    }

    public function getVacancyBySlug(string $slug): ?Vacancy
    {
        return $this->model->newQuery()
            ->with(['translates.language', 'requirements.translates.language'])
            ->whereHas('translates', fn($q) => $q->where('slug', $slug))
            ->active()
            ->notExpired()
            ->first();
    }
}
