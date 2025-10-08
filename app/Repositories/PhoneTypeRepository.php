<?php
namespace App\Repositories;

use App\Models\PhoneType;
use App\Repositories\Global\BaseRepository;

class PhoneTypeRepository extends BaseRepository
{

    public function __construct(public PhoneType $phoneType)
    {
    }

    // Bütün telefon tiplərini gətir
    public function getPhoneTypes()
    {
        return $this->phoneType->with('translates.language')->orderBy('order')->get();
    }
}
