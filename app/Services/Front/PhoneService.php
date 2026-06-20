<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\PhoneRepositoryInterface;
use App\Interfaces\Services\Front\PhoneServiceInterface;
use App\Services\BaseService;

class PhoneService extends BaseService implements PhoneServiceInterface
{
    protected PhoneRepositoryInterface $phoneRepository;

    public function __construct(PhoneRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->phoneRepository = $repository;
    }
}
