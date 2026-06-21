<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\EmailRepositoryInterface;
use App\Interfaces\Services\Admin\EmailServiceInterface;
use App\Services\BaseService;

class EmailService extends BaseService implements EmailServiceInterface
{
    protected EmailRepositoryInterface $EmailRepository;

    public function __construct(EmailRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->EmailRepository = $repository;
    }

    public function getEmails()
    {
        return $this->EmailRepository->getEmails();
    }
}
