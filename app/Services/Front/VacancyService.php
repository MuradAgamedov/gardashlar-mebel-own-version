<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\VacancyRepositoryInterface;
use App\Interfaces\Services\Front\VacancyServiceInterface;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Vacancy;

class VacancyService extends BaseService implements VacancyServiceInterface
{
    protected VacancyRepositoryInterface $vacancyRepository;

    public function __construct(VacancyRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->vacancyRepository = $repository;
    }

    public function getActiveNotExpiredOrdered(int $languageId): Collection
    {
        return $this->vacancyRepository->getActiveNotExpiredOrdered($languageId);
    }

    public function getVacancyBySlug(string $slug): ?Vacancy
    {
        return $this->vacancyRepository->getVacancyBySlug($slug);
    }
}
