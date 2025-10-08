<?php
namespace App\Repositories;

use App\Models\Phone;
use App\Models\PhoneType;
use App\Repositories\Global\BaseRepository;

class PhoneRepository extends BaseRepository
{

    public function __construct(public Phone $phone)
    {
    }

    // Bütün telefonları gətir
    public function getPhones()
    {
        return $this->phone->with('phoneType.translates.language')->orderBy('order')->get();
    }

    // Telefon tiplərini gətir
    public function getPhoneTypes()
    {
        return PhoneType::with('translates.language')->orderBy('order')->get();
    }

    // Aktiv telefon tiplərini gətir
    public function getActivePhoneTypes()
    {
        return PhoneType::with('translates.language')->active()->ordered()->get();
    }
}
