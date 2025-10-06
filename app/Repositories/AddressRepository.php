<?php
namespace App\Repositories;

use App\Models\AddressType;
use App\Repositories\Global\BaseRepository;
class AddressRepository extends BaseRepository
{

    public function __construct(public AddressType $address)
    {
    }

    // məsələn bütün məlumatları gətirmək üçün
    public function getAddresses()
    {
        return $this->GetData($this->address, ['translates.language', 'addresses.translates.language'])->get();
    }
}
