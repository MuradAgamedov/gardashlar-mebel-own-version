<?php

namespace App\Services;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use App\Interfaces\Services\BaseServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseService implements BaseServiceInterface
{
    /**
     * Repository instance
     */
    protected BaseRepositoryInterface $repository;

    /**
     * Constructor - uşaq sinif repository-ni ötürməlidir
     */
    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $with = []): Collection
    {
        return $this->repository->all($with);
    }

    /**
     * {@inheritdoc}
     */
    public function first(array $with = []): ?Model
    {
        return $this->repository->first($with);
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $id, array $with = []): ?Model
    {
        return $this->repository->find($id, $with);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail(int $id, array $with = []): Model
    {
        return $this->repository->findOrFail($id, $with);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    public function allOrdered(array $with = []): Collection
    {
        return $this->repository->allOrdered($with);
    }

    /**
     * {@inheritdoc}
     */
    public function reorder(array $orderedIds): bool
    {
        return $this->repository->reorder($orderedIds);
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage = 15, array $with = [], array $orderBy = ['id', 'desc']): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $with, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function createWithTranslations(array $data, array $translations, string $translationModel, string $foreignKey): Model
    {
        return $this->repository->createWithTranslations($data, $translations, $translationModel, $foreignKey);
    }

    /**
     * {@inheritdoc}
     */
    public function updateWithTranslations(int $id, array $data, array $translations, string $foreignKey): Model
    {
        return $this->repository->updateWithTranslations($id, $data, $translations, $foreignKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveOrderedWithTranslations(int $languageId, array $relations = []): Collection
    {
        return $this->repository->getActiveOrderedWithTranslations($languageId, $relations);
    }

    /**
     * {@inheritdoc}
     */
    public function getByIdWithTranslations(int $id, int $languageId, array $relations = []): ?Model
    {
        return $this->repository->getByIdWithTranslations($id, $languageId, $relations);
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveOrdered(array $with = []): Collection
    {
        return $this->repository->getActiveOrdered($with);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstOrCreate(array $relations = []): Model
    {
        return $this->repository->getFirstOrCreate($relations);
    }
}
