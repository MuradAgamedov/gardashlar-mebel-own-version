<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\AddressTypeRepositoryInterface;
use App\Models\AddressType;
use App\Models\AddressTypeTranslate;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class AddressTypeRepository extends BaseRepository implements AddressTypeRepositoryInterface
{
    public function __construct(AddressType $model)
    {
        parent::__construct($model);
    }

    public function getAddressTypes()
    {
        return $this->GetData($this->addressType, ['translates.language'])->get();
    }

    public function createAddressType(array $data, array $translations)
    {
        return DB::transaction(function () use ($data, $translations) {
            $addressType = $this->addressType->create([
                'status' => $data['status'] ?? false,
            ]);

            foreach ($translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    AddressTypeTranslate::create([
                        'address_type_id' => $addressType->id,
                        'language_id' => $languageId,
                        'title' => trim($translation['title']),
                    ]);
                }
            }

            return $addressType->load('translates.language');
        });
    }

    public function updateAddressType(AddressType $addressType, array $data, array $translations)
    {
        return DB::transaction(function () use ($addressType, $data, $translations) {
            $addressType->update([
                'status' => $data['status'] ?? false,
            ]);

            foreach ($translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    AddressTypeTranslate::updateOrCreate(
                        [
                            'address_type_id' => $addressType->id,
                            'language_id' => $languageId,
                        ],
                        [
                            'title' => trim($translation['title']),
                        ]
                    );
                }
            }

            return $addressType->load('translates.language');
        });
    }

    public function deleteAddressType(AddressType $addressType)
    {
        return $addressType->delete();
    }

    public function reorderAddressTypes(array $items)
    {
        return $this->UpdateOrder($this->addressType, $items);
    }
}
