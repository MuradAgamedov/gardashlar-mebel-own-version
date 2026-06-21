<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface KivServiceInterface extends BaseServiceInterface
{
    //

    public function getKivs($request);

    public function getCategories();

    public function createKiv(array $data, $imageFile, $imageService);

    public function updateKiv(\App\Models\Kiv $kiv, array $data, $imageFile, $imageService);

    public function deleteKiv(\App\Models\Kiv $kiv);

    public function reorderKivs(array $kivs);
}
