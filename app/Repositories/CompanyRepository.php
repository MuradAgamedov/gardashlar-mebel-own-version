<?php
namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Global\BaseRepository;

class CompanyRepository extends BaseRepository
{

    public function __construct(public Company $company)
    {
    }

    // məsələn bütün məlumatları gətirmək üçün
    public function getCompanies()
    {
        return $this->GetData($this->company, ['translates.language'])->get();
    }
}
