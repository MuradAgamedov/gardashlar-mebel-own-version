<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\PhoneRepositoryInterface;
use App\Interfaces\Services\Admin\PhoneServiceInterface;
use App\Services\BaseService;

class PhoneService extends BaseService implements PhoneServiceInterface
{
    protected PhoneRepositoryInterface $PhoneRepository;

    public function __construct(PhoneRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->PhoneRepository = $repository;
    }
}
