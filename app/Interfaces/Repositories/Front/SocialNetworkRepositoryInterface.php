<?php

namespace App\Interfaces\Repositories\Front;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface SocialNetworkRepositoryInterface extends BaseRepositoryInterface
{
    public function getByType(string $type);
    public function getByTypes(array $types);
    public function getAvailableTypes(): array;
}
