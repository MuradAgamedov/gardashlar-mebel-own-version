<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\AddressTypeRepositoryInterface;
use App\Models\AddressType;
use App\Repositories\BaseRepository;

class AddressTypeRepository extends BaseRepository implements AddressTypeRepositoryInterface
{
    public function __construct(AddressType $model)
    {
        parent::__construct($model);
    }
}
