<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface AdministrationServiceInterface extends BaseServiceInterface
{
    //

    public function getAdministrations();

    public function createAdministration(array $data, array $translates, $imageFile);

    public function updateAdministration(\App\Models\Administration $administration, array $data, array $translates, $imageFile = null);

    public function deleteAdministration(\App\Models\Administration $administration);

    public function reorderAdministrations(array $order);
}
