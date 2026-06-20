<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\PhoneRepositoryInterface;
use App\Models\Phone;
use App\Repositories\BaseRepository;

class PhoneRepository extends BaseRepository implements PhoneRepositoryInterface
{
    public function __construct(Phone $model)
    {
        parent::__construct($model);
    }
}
