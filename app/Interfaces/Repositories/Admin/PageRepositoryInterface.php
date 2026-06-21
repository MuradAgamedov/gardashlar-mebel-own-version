<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface PageRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getPages();

    public function findPage($id);

    public function createPage(array $data, array $translates);

    public function updatePage($id, array $data, array $translates);

    public function deletePage($id);
}
