<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\PhoneRepositoryInterface;
use App\Models\Phone;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class PhoneRepository extends BaseRepository implements PhoneRepositoryInterface
{
    public function __construct(Phone $model)
    {
        parent::__construct($model);
    }

    public function getPhonesByType(int $typeId): Collection
    {
        return $this->model->newQuery()
            ->select(['id', 'phone'])
            ->where('phone_type_id', $typeId)
            ->where('status', true)
            ->orderBy('order')
            ->get();
    }
}
