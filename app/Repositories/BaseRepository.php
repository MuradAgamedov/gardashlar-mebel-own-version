<?php

namespace App\Repositories;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * Eloquent model instance
     */
    protected Model $model;

    /**
     * Constructor - uşaq sinif model-i ötürməlidir
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $with = []): Collection
    {
        return $this->model->newQuery()->with($with)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $id, array $with = []): ?Model
    {
        return $this->model->newQuery()->with($with)->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail(int $id, array $with = []): Model
    {
        return $this->model->newQuery()->with($with)->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): Model
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, array $data): bool
    {
        $record = $this->findOrFail($id);
        return $record->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        $record = $this->findOrFail($id);
        return $record->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function allOrdered(array $with = []): Collection
    {
        return $this->model->newQuery()->with($with)->ordered()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function reorder(array $orderedIds): bool
    {
        return DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $id) {
                $this->model->newQuery()->where('id', $id)->update(['order' => $index + 1]);
            }
            return true;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage = 15, array $with = [], array $orderBy = ['id', 'desc']): LengthAwarePaginator
    {
        return $this->model->newQuery()->with($with)->orderBy($orderBy[0], $orderBy[1])->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function createWithTranslations(array $data, array $translations, string $translationModel, string $foreignKey): Model
    {
        return DB::transaction(function () use ($data, $translations, $translationModel, $foreignKey) {
            $record = $this->create($data);
            $this->createTranslations($record, $translations, $translationModel, $foreignKey);
            return $record->load('translates.language');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function updateWithTranslations(int $id, array $data, array $translations, string $foreignKey): Model
    {
        return DB::transaction(function () use ($id, $data, $translations, $foreignKey) {
            $record = $this->findOrFail($id);
            $record->update($data);
            $this->updateTranslations($record, $translations, $foreignKey);
            return $record->load('translates.language');
        });
    }

    /**
     * Translation yaratma helper metodu
     */
    protected function createTranslations(Model $record, array $translations, string $translationModel, string $foreignKey): void
    {
        foreach ($translations as $translation) {
            $translationModel::create(array_merge(
                [$foreignKey => $record->id],
                $translation
            ));
        }
    }

    /**
     * Translation yeniləmə helper metodu
     */
    protected function updateTranslations(Model $record, array $translations, string $foreignKey): void
    {
        foreach ($translations as $translation) {
            $record->translates()->updateOrCreate(
                [
                    $foreignKey => $record->id,
                    'language_id' => $translation['language_id']
                ],
                $translation
            );
        }
    }

    /**
     * Şəkil yükləmə helper metodu
     */
    protected function handleImageUpload($imageFile, string $folder, $imageService): ?string
    {
        if ($imageFile) {
            return $imageService->upload($imageFile, $folder);
        }
        return null;
    }

    /**
     * Şəkil yeniləmə helper metodu
     */
    protected function handleImageUpdate(Model $record, $imageFile, string $folder, $imageService): ?string
    {
        if ($imageFile) {
            if ($record->image) {
                $imageService->delete($record->image);
            }
            return $imageService->upload($imageFile, $folder);
        }
        return null;
    }

    /**
     * Şəkil silmə helper metodu
     */
    protected function handleImageDelete(?string $imagePath, $imageService): void
    {
        if ($imagePath) {
            $imageService->delete($imagePath);
        }
    }

    /**
     * Model query builder qaytarır
     */
    protected function query()
    {
        return $this->model->newQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveOrderedWithTranslations(int $languageId, array $relations = []): Collection
    {
        $query = $this->query();
        
        if (method_exists($this->model, 'scopeActive')) {
            $query->active();
        }
        
        if (method_exists($this->model, 'scopeOrdered')) {
            $query->ordered();
        }
        
        $query->with(array_merge([
            'translates' => function ($q) use ($languageId) {
                $q->where('language_id', $languageId);
            }
        ], $relations));
        
        return $query->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getByIdWithTranslations(int $id, int $languageId, array $relations = []): ?Model
    {
        $query = $this->query();
        
        if (method_exists($this->model, 'scopeActive')) {
            $query->active();
        }
        
        $query->with(array_merge([
            'translates' => function ($q) use ($languageId) {
                $q->where('language_id', $languageId);
            }
        ], $relations));
        
        return $query->find($id);
    }
}
