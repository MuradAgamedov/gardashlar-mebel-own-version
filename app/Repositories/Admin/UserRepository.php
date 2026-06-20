<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\UserRepositoryInterface;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(User $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
