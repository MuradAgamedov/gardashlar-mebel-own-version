<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\PhoneTypeRepositoryInterface;
use App\Models\PhoneType;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class PhoneTypeRepository extends BaseRepository implements PhoneTypeRepositoryInterface
{
    public function __construct(PhoneType $model)
    {
        parent::__construct($model);
    }

    public function getPhonesGroupedByTypes(int $languageId): Collection
    {
        return $this->model->newQuery()
            ->with([
                'translates' => function ($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'phones' => function ($query) {
                    $query->where('status', true)
                        ->where('id', '!=', 15)
                        ->orderBy('order');
                }
            ])
            ->where('status', true)
            ->whereNotIn('id', [3, 15])
            ->orderBy('order')
            ->get();
    }

    public function getSpecialPhone(int $typeId): ?PhoneType
    {
        return $this->model->newQuery()
            ->with(['phones' => function ($query) {
                $query->where('status', true)
                      ->orderBy('order');
            }])
            ->where('id', $typeId)
            ->where('status', true)
            ->first();
    }
}
