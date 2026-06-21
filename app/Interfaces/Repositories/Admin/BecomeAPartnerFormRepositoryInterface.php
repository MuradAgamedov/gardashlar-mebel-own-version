<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface BecomeAPartnerFormRepositoryInterface extends BaseRepositoryInterface
{
    public function getForms($request);
    public function deleteForm($form);
    public function markAsRead($form);
    public function deleteSelected(array $ids);
}
