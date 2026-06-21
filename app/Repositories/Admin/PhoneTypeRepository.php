<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\PhoneTypeRepositoryInterface;
use App\Models\PhoneType;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class PhoneTypeRepository extends BaseRepository implements PhoneTypeRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(PhoneType $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getPhoneTypes()
    {
        return $this->phoneType->with('translates.language')->orderBy('order')->get();
    }

    public function createPhoneType(array $data, array $translations, $languages)
    {
        $phoneType = $this->phoneType->create($data);

        foreach ($languages as $language) {
            $translationData = $translations[$language->id] ?? [];
            
            $phoneType->translates()->create([
                'language_id' => $language->id,
                'title' => trim($translationData['title'] ?? ''),
            ]);
        }

        return $phoneType;
    }

    public function updatePhoneType(PhoneType $phoneType, array $data, array $translations, $languages)
    {
        $phoneType->update($data);

        foreach ($languages as $language) {
            $translationData = $translations[$language->id] ?? [];
            
            $phoneType->translates()->updateOrCreate(
                ['language_id' => $language->id],
                ['title' => trim($translationData['title'] ?? '')]
            );
        }

        return $phoneType;
    }

    public function deletePhoneType(PhoneType $phoneType)
    {
        if ($phoneType->id == 3) {
            throw new \Exception('This phone type cannot be deleted as it is protected.');
        }
        return $phoneType->delete();
    }

    public function reorderPhoneTypes(array $phoneTypes)
    {
        foreach ($phoneTypes as $index => $phoneTypeId) {
            $this->phoneType->where('id', $phoneTypeId)->update(['order' => $index + 1]);
        }
        return true;
    }
}
