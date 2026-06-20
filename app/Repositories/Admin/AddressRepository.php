<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\AddressRepositoryInterface;
use App\Models\Address;
use App\Repositories\BaseRepository;

class AddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    public function __construct(Address $model)
    {
        parent::__construct($model);
    }
}
