<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface BecomeAPartnerFormServiceInterface extends BaseServiceInterface
{
    public function getForms($request);
    public function deleteForm($form);
    public function markAsRead($form);
    public function deleteSelected(array $ids);
}
