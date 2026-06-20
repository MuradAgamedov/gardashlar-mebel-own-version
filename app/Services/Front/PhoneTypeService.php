<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\PhoneTypeRepositoryInterface;
use App\Interfaces\Services\Front\PhoneTypeServiceInterface;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use App\Models\PhoneType;

class PhoneTypeService extends BaseService implements PhoneTypeServiceInterface
{
    protected PhoneTypeRepositoryInterface $phoneTypeRepository;

    public function __construct(PhoneTypeRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->phoneTypeRepository = $repository;
    }

    public function getPhonesGroupedByTypes(int $languageId): Collection
    {
        return $this->phoneTypeRepository->getPhonesGroupedByTypes($languageId);
    }

    public function getSpecialPhone(int $typeId): ?PhoneType
    {
        return $this->phoneTypeRepository->getSpecialPhone($typeId);
    }
}
