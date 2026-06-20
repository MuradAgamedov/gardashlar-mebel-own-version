<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\CreditCardRepositoryInterface;
use App\Models\CreditCard;
use App\Repositories\BaseRepository;

class CreditCardRepository extends BaseRepository implements CreditCardRepositoryInterface
{
    public function __construct(CreditCard $model)
    {
        parent::__construct($model);
    }
}
