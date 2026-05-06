<?php
namespace App\Repositories;

use App\Models\Dictionary;
use App\Repositories\Global\BaseRepository;

class DictionaryRepository extends BaseRepository
{

    public function __construct(public Dictionary $dictionary)
    {
    }

    // Dictionary-ləri filtrlə və gətir
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
