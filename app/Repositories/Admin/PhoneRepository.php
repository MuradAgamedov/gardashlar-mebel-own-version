<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\PhoneRepositoryInterface;
use App\Models\Phone;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class PhoneRepository extends BaseRepository implements PhoneRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Phone $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getPhones()
    {
        return $this->phone->with('phoneType.translates.language')->orderBy('order')->get();
    }

    public function getPhoneTypes()
    {
        return PhoneType::with('translates.language')->orderBy('order')->get();
    }

    public function getActivePhoneTypes()
    {
        return PhoneType::with('translates.language')->active()->ordered()->get();
    }

    public function findPhoneType($typeId)
    {
        return PhoneType::with('translates.language')->findOrFail($typeId);
    }

    public function createPhone(array $data)
    {
        return $this->phone->create($data);
    }

    public function updatePhone(Phone $phone, array $data)
    {
        $phone->update($data);
        return $phone;
    }

    public function deletePhone(Phone $phone)
    {
        return $phone->delete();
    }

    public function reorderPhones(array $phones)
    {
        foreach ($phones as $index => $phoneId) {
            $this->phone->where('id', $phoneId)->update(['order' => $index + 1]);
        }
        return true;
    }
}
