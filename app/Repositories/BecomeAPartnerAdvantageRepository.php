<?php
namespace App\Repositories;

use App\Models\BecomeAPartnerAdvantage;
use App\Repositories\Global\BaseRepository;

class BecomeAPartnerAdvantageRepository extends BaseRepository
{

    public function __construct(public BecomeAPartnerAdvantage $advantage)
    {
    }

    // məsələn bütün məlumatları gətirmək üçün
    public function getAdvantages()
    {
        return $this->GetData($this->advantage, ['translates.language'])->get();
    }
}
