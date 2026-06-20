<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\ServiceRepositoryInterface;
use App\Models\Service;
use App\Repositories\BaseRepository;

class ServiceRepository extends BaseRepository implements ServiceRepositoryInterface
{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }

    public function getServiceBySlugWithRelations(string $slug, int $languageId)
    {
        return $this->model->newQuery()
            ->active()
            ->with([
                'translates.language',
                'advantages.translates' => fn($q) => $q->where('language_id', $languageId),
            ])
            ->whereHas('translates', fn($q) => $q->where('slug', $slug))
            ->first();
    }
}
