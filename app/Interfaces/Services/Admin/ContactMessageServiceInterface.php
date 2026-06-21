<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ContactMessageServiceInterface extends BaseServiceInterface
{
    //

    public function getMessages($request);

    public function findMessage(string $id);

    public function markAsRead(\App\Models\ContactMessage $message);

    public function deleteMessage(\App\Models\ContactMessage $message);

    public function deleteSelected(array $ids);
}
