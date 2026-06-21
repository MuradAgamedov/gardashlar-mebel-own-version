<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface PageServiceInterface extends BaseServiceInterface
{
    //

    public function getPages();

    public function findPage($id);

    public function createPage(array $data, array $translates);

    public function updatePage($id, array $data, array $translates);

    public function deletePage($id);
}
