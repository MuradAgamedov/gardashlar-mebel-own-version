<?php
namespace App\Repositories;

use App\Models\Action;
use App\Models\About;
use App\Repositories\Global\BaseRepository;

class ActionRepository extends BaseRepository
{

    public function __construct(public Action $action)
    {
    }

    // məsələn bütün məlumatları gətirmək üçün
    public function getAction($request)
    {
        $query = $this->GetData($this->action, ['company','translates.language']);


          // Search functionality
          if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        return $query->paginate(15);
    }
}
