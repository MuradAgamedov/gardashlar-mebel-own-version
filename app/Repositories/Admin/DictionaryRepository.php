<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\DictionaryRepositoryInterface;
use App\Models\Dictionary;
use App\Repositories\BaseRepository;

class DictionaryRepository extends BaseRepository implements DictionaryRepositoryInterface
{
    public function __construct(Dictionary $model)
    {
        parent::__construct($model);
    }
}
