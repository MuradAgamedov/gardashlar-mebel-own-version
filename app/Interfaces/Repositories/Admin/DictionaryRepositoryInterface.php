<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface DictionaryRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getDictionaries($request);
}
