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

    public function getMessages($request)
    {
        return $this->contactMessageRepository->getMessages($request);
    }

    public function findMessage(string $id)
    {
        return $this->contactMessageRepository->findMessage($id);
    }

    public function markAsRead(\App\Models\ContactMessage $message)
    {
        return $this->contactMessageRepository->markAsRead($message);
    }

    public function deleteMessage(\App\Models\ContactMessage $message)
    {
        return $this->contactMessageRepository->deleteMessage($message);
    }

    public function deleteSelected(array $ids)
    {
        return $this->contactMessageRepository->deleteSelected($ids);
    }
}
