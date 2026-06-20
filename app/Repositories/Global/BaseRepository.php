<?php

namespace App\Repositories\Global;

/**
 * @deprecated Yeni BaseRepository (App\Repositories\BaseRepository) istifadə edin.
 * Bu sinif geriyə uyğunluq üçün saxlanılır.
 * Yeni modullarda App\Repositories\BaseRepository istifadə edin.
 */
abstract class BaseRepository
{
    public function GetData($model, $with = [])
    {
        return $model::with($with)->ordered();
    }

    public function FindById($model, $id, $with = [])
    {
        return $model::with($with)->findOrFail($id);
    }

    public function UpdateOrder($model, array $items)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($model, $items) {
            foreach ($items as $index => $id) {
                $model::where('id', $id)->update(['order' => $index + 1]);
            }
            return true;
        });
    }

    protected function createTranslations($record, array $translations, string $translationModel, string $foreignKey)
    {
        foreach ($translations as $translation) {
            $translationModel::create(array_merge(
                [$foreignKey => $record->id],
                $translation
            ));
        }
    }

    protected function updateTranslations($record, array $translations, string $foreignKey)
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

    protected function handleImageUpload($imageFile, string $folder, $imageService)
    {
        if ($imageFile) {
            return $imageService->upload($imageFile, $folder);
        }
        return null;
    }

    protected function handleImageUpdate($record, $imageFile, string $folder, $imageService)
    {
        if ($imageFile) {
            if ($record->image) {
                $imageService->delete($record->image);
            }
            return $imageService->upload($imageFile, $folder);
        }
        return null;
    }

    protected function handleImageDelete($imagePath, $imageService)
    {
        if ($imagePath) {
            $imageService->delete($imagePath);
        }
    }
}