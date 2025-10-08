<?php
namespace App\Repositories;

use App\Models\BecomeAInvestorAdvantage;
use App\Repositories\Global\BaseRepository;

class BecomeAInvestorAdvantageRepository extends BaseRepository
{

    public function __construct(public BecomeAInvestorAdvantage $advantage)
    {
    }

    // məsələn bütün məlumatları gətirmək üçün
    public function getAdvantages()
    {
        return $this->GetData($this->advantage, ['translates.language'])->get();
    }
}
