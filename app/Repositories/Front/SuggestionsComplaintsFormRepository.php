<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\SuggestionsComplaintsFormRepositoryInterface;
use App\Models\SuggestionsComplaintsForm;
use App\Repositories\BaseRepository;

class SuggestionsComplaintsFormRepository extends BaseRepository implements SuggestionsComplaintsFormRepositoryInterface
{
    public function __construct(SuggestionsComplaintsForm $model)
    {
        parent::__construct($model);
    }
}
