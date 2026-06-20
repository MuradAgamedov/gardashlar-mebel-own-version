<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface AboutRepositoryInterface extends BaseRepositoryInterface
{
    public function getAbout(): Collection;
    public function createAbout(array $data, array $translates, $imageFile): Model;
    public function updateAbout(Model $about, array $data, array $translates, $imageFile = null): Model;
    public function deleteAbout(Model $about): bool;
    public function reorderAbouts(array $order): bool;
}
