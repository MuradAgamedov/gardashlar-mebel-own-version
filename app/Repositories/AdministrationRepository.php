<?php
namespace App\Repositories;

use App\Models\Administration;
use App\Repositories\Global\BaseRepository;

class AdministrationRepository extends BaseRepository
{

    public function __construct(public Administration $administration)
    {
    }

    // məsələn bütün məlumatları gətirmək üçün
    public function getAdministrations()
    {
        return $this->GetData($this->administration, ['translates.language'])->get();
    }
}
