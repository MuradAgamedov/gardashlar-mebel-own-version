<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface SeoPageRepositoryInterface extends BaseRepositoryInterface
{
    public function getSeoPages();
    public function findSeoPage($id);
    public function getActiveCategories();
    public function createSeoPage(array $data, array $translates);
    public function updateSeoPage($id, array $data, array $translates);
    public function deleteSeoPage($id);
}
