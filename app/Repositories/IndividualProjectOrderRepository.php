<?php
namespace App\Repositories;

use App\Models\IndividualProjectOrder;
use App\Repositories\Global\BaseRepository;

class IndividualProjectOrderRepository extends BaseRepository
{

    public function __construct(public IndividualProjectOrder $individualProjectOrder)
    {
    }

    // Fərdi layihə sifarişlərini filtrlə və gətir
    public function getIndividualProjectOrders($request)
    {
        $query = $this->individualProjectOrder->with('translates.language');
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        return $query->ordered()->get();
    }
}
