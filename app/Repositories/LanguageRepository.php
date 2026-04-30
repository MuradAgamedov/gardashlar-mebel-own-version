<?php
namespace App\Repositories;

use App\Models\Language;
use App\Repositories\Global\BaseRepository;

class LanguageRepository extends BaseRepository
{

    public function __construct(public Language $language)
    {
    }

    // Bütün dillər
    public function getAllLanguages()
    {
        return $this->language->orderBy('order')->get();
    }

    // Aktiv dillər
    public function getActiveLanguages()
    {
        return $this->language->getActiveLanguages();
    }

    // Əsas dil
    public function getMainLanguage()
    {
        return $this->language->getMainLanguage();
    }
}
