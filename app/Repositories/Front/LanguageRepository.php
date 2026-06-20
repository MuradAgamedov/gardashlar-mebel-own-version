<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\LanguageRepositoryInterface;
use App\Models\Language;
use App\Repositories\BaseRepository;

class LanguageRepository extends BaseRepository implements LanguageRepositoryInterface
{
    public function __construct(Language $model)
    {
        parent::__construct($model);
    }
}
