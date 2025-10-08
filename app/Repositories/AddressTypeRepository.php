<?php
namespace App\Repositories;

use App\Models\AddressType;
use App\Repositories\Global\BaseRepository;

class AddressTypeRepository extends BaseRepository
{

    public function __construct(public AddressType $addressType)
    {
    }

    // məsələn bütün məlumatları gətirmək üçün
    public function getAddressTypes()
    {
        return $this->GetData($this->addressType, ['translates.language'])->get();
    }
}
