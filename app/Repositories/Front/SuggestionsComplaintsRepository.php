<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\SuggestionsComplaintsRepositoryInterface;
use App\Models\SuggestionsComplaints;
use App\Repositories\BaseRepository;

class SuggestionsComplaintsRepository extends BaseRepository implements SuggestionsComplaintsRepositoryInterface
{
    public function __construct(SuggestionsComplaints $model)
    {
        parent::__construct($model);
    }
}
