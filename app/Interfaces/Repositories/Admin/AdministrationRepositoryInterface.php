<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface AdministrationRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getAdministrations();

    public function createAdministration(array $data, array $translates, $imageFile);

    public function updateAdministration(\App\Models\Administration $administration, array $data, array $translates, $imageFile = null);

    public function deleteAdministration(\App\Models\Administration $administration);

    public function reorderAdministrations(array $order);
}
