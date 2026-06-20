<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\DictionaryRepositoryInterface;
use App\Models\Dictionary;
use App\Repositories\BaseRepository;

class DictionaryRepository extends BaseRepository implements DictionaryRepositoryInterface
{
    public function __construct(Dictionary $model)
    {
        parent::__construct($model);
    }
}
