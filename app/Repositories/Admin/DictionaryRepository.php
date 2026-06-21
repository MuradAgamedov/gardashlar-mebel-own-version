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

    public function getDictionaries($request)
    {
        $query = $this->dictionary->with('translates.language');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('keyword', 'like', "%{$search}%")
                  ->orWhereHas('translates', function($translateQuery) use ($search) {
                      $translateQuery->where('value', 'like', "%{$search}%");
                  });
            });
        }

        return $query->paginate(40)->withQueryString();
    }
}
