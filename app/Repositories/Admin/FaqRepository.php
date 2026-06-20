<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\FaqRepositoryInterface;
use App\Models\Faq;
use App\Repositories\BaseRepository;

class FaqRepository extends BaseRepository implements FaqRepositoryInterface
{
    public function __construct(Faq $model)
    {
        parent::__construct($model);
    }
}
