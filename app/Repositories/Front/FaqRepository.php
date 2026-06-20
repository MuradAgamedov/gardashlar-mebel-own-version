<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\FaqRepositoryInterface;
use App\Models\Faq;
use App\Repositories\BaseRepository;

class FaqRepository extends BaseRepository implements FaqRepositoryInterface
{
    public function __construct(Faq $model)
    {
        parent::__construct($model);
    }
}
