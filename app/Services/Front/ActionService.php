<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\ActionRepositoryInterface;
use App\Interfaces\Services\Front\ActionServiceInterface;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Action;

class ActionService extends BaseService implements ActionServiceInterface
{
    protected ActionRepositoryInterface $actionRepository;

    public function __construct(ActionRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->actionRepository = $repository;
    }

    public function getActiveCurrentOrdered(int $languageId, ?int $companyId = null): Collection
    {
        return $this->actionRepository->getActiveCurrentOrdered($languageId, $companyId);
    }

    public function getActiveById(int $id, int $languageId): ?Action
    {
        return $this->actionRepository->getActiveById($id, $languageId);
    }
}
