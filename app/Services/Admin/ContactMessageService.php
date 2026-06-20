<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ContactMessageRepositoryInterface;
use App\Interfaces\Services\Admin\ContactMessageServiceInterface;
use App\Services\BaseService;

class ContactMessageService extends BaseService implements ContactMessageServiceInterface
{
    protected ContactMessageRepositoryInterface $contactMessageRepository;

    public function __construct(ContactMessageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->contactMessageRepository = $repository;
    }
}
