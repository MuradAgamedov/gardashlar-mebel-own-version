<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\AboutRepositoryInterface;
use App\Interfaces\Services\Admin\AboutServiceInterface;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AboutService extends BaseService implements AboutServiceInterface
{
    protected AboutRepositoryInterface $aboutRepository;

    public function __construct(AboutRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->aboutRepository = $repository;
    }

    public function getAbout(): Collection
    {
        return $this->aboutRepository->getAbout();
    }

    public function createAbout(array $data, array $translates, $imageFile): Model
    {
        return $this->aboutRepository->createAbout($data, $translates, $imageFile);
    }

    public function updateAbout(Model $about, array $data, array $translates, $imageFile = null): Model
    {
        return $this->aboutRepository->updateAbout($about, $data, $translates, $imageFile);
    }

    public function deleteAbout(Model $about): bool
    {
        return $this->aboutRepository->deleteAbout($about);
    }

    public function reorderAbouts(array $order): bool
    {
        return $this->aboutRepository->reorderAbouts($order);
    }
}
