<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ProductTypeServiceInterface extends BaseServiceInterface
{
    //

    public function getTypes($request);
}
