<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\SuggestionRepositoryInterface;
use App\Models\Suggestion;
use App\Repositories\BaseRepository;

class SuggestionRepository extends BaseRepository implements SuggestionRepositoryInterface
{
    public function __construct(Suggestion $model)
    {
        parent::__construct($model);
    }
}
