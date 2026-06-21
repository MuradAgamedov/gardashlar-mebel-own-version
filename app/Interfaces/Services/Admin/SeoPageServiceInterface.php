<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface SeoPageServiceInterface extends BaseServiceInterface
{
    public function getSeoPages();
    public function findSeoPage($id);
    public function getActiveCategories();
    public function createSeoPage(array $data, array $translates);
    public function updateSeoPage($id, array $data, array $translates);
    public function deleteSeoPage($id);
}
