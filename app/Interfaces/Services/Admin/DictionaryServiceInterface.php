<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface DictionaryServiceInterface extends BaseServiceInterface
{
    //

    public function getDictionaries($request);
}
