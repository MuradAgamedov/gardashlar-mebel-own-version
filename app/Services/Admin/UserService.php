<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\UserRepositoryInterface;
use App\Interfaces\Services\Admin\UserServiceInterface;
use App\Services\BaseService;

class UserService extends BaseService implements UserServiceInterface
{
    protected UserRepositoryInterface $UserRepository;

    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->UserRepository = $repository;
    }
}
