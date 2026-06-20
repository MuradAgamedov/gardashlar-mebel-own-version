<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\ActionRepositoryInterface;
use App\Models\Action;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ActionRepository extends BaseRepository implements ActionRepositoryInterface
{
    public function __construct(Action $model)
    {
        parent::__construct($model);
    }

    public function getActiveCurrentOrdered(int $languageId, ?int $companyId = null): Collection
    {
        $query = $this->model->newQuery()
            ->with(['company', 'translates' => fn($q) => $q->where('language_id', $languageId)])
            ->active()
            ->current()
            ->ordered();

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->get();
    }

    public function getActiveById(int $id, int $languageId): ?Action
    {
        return $this->model->newQuery()
            ->with(['company', 'translates' => fn($q) => $q->where('language_id', $languageId)])
            ->active()
            ->find($id);
    }
}
