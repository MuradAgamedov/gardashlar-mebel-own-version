<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\ActionRepositoryInterface;
use App\Models\Action;
use App\Repositories\BaseRepository;

class ActionRepository extends BaseRepository implements ActionRepositoryInterface
{
    public function __construct(Action $model)
    {
        parent::__construct($model);
    }
}
