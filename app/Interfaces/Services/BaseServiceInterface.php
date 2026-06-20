<?php

namespace App\Interfaces\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BaseServiceInterface
{
    /**
     * Bütün qeydləri əldə et
     */
    public function all(array $with = []): Collection;

    /**
     * ID ilə qeydi tap
     */
    public function find(int $id, array $with = []): ?Model;

    /**
     * ID ilə qeydi tap, tapılmazsa exception
     */
    public function findOrFail(int $id, array $with = []): Model;

    /**
     * Yeni qeyd yarat
     */
    public function create(array $data): Model;

    /**
     * Mövcud qeydi yenilə
     */
    public function update(int $id, array $data): bool;

    /**
     * Qeydi sil
     */
    public function delete(int $id): bool;

    /**
     * Sıralı şəkildə bütün qeydləri əldə et
     */
    public function allOrdered(array $with = []): Collection;

    /**
     * Sıranı yenilə
     */
    public function reorder(array $orderedIds): bool;

    /**
     * Səhifələmə ilə əldə et
     */
    public function paginate(int $perPage = 15, array $with = [], array $orderBy = ['id', 'desc']): LengthAwarePaginator;

    /**
     * Tərcümələrlə birgə yarat
     */
    public function createWithTranslations(array $data, array $translations, string $translationModel, string $foreignKey): Model;

    /**
     * Tərcümələrlə birgə yenilə
     */
    public function updateWithTranslations(int $id, array $data, array $translations, string $foreignKey): Model;

    /**
     * Aktiv və sıralanmış qeydləri tərcümələri ilə birgə əldə et
     */
    public function getActiveOrderedWithTranslations(int $languageId, array $relations = []): Collection;

    /**
     * ID ilə qeydi və onun tərcüməsini əldə et
     */
    public function getByIdWithTranslations(int $id, int $languageId, array $relations = []): ?Model;
}
