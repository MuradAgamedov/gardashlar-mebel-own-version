<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\AddressRepositoryInterface;
use App\Models\Address;
use App\Repositories\BaseRepository;

class AddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    public function __construct(Address $model)
    {
        parent::__construct($model);
    }
}
