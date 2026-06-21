<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\AddressRepositoryInterface;
use App\Models\Address;
use App\Models\AddressTranslate;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class AddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    public function __construct(Address $model)
    {
        parent::__construct($model);
    }

    public function getAddresses()
    {
        return $this->GetData($this->addressType, ['translates.language', 'addresses.translates.language'])->get();
    }

    public function createAddress(array $data, array $translations)
    {
        return DB::transaction(function () use ($data, $translations) {
            $address = $this->address->create([
                'address_type_id' => $data['address_type_id'],
                'status' => $data['status'] ?? false,
            ]);

            foreach ($translations as $languageId => $translation) {
                if (!empty($translation['address'])) {
                    AddressTranslate::create([
                        'address_id' => $address->id,
                        'language_id' => $languageId,
                        'address' => trim($translation['address']),
                    ]);
                }
            }

            return $address->load('translates.language');
        });
    }

    public function updateAddress(Address $address, array $data, array $translations)
    {
        return DB::transaction(function () use ($address, $data, $translations) {
            $address->update([
                'address_type_id' => $data['address_type_id'],
                'status' => $data['status'] ?? false,
            ]);

            foreach ($translations as $languageId => $translation) {
                if (!empty($translation['address'])) {
                    AddressTranslate::updateOrCreate(
                        [
                            'address_id' => $address->id,
                            'language_id' => $languageId,
                        ],
                        [
                            'address' => trim($translation['address']),
                        ]
                    );
                }
            }

            return $address->load('translates.language');
        });
    }

    public function deleteAddress(Address $address)
    {
        return $address->delete();
    }

    public function reorderAddresses(array $items)
    {
        return $this->UpdateOrder($this->address, $items);
    }
}
