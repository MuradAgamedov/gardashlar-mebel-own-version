<?php
namespace App\Repositories;

use App\Models\CreditCard;
use App\Repositories\Global\BaseRepository;

class CreditCardRepository extends BaseRepository
{

    public function __construct(public CreditCard $creditCard)
    {
    }

    // məsələn bütün məlumatları gətirmək üçün
    public function getCreditCards()
    {
        return $this->GetData($this->creditCard, [])->get();
    }
}
