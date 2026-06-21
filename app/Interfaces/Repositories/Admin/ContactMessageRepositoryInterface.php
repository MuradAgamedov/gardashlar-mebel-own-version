<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ContactMessageRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getMessages($request);

    public function findMessage(string $id);

    public function markAsRead(\App\Models\ContactMessage $message);

    public function deleteMessage(\App\Models\ContactMessage $message);

    public function deleteSelected(array $ids);
}
