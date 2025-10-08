<?php
namespace App\Repositories;

use App\Models\Faq;
use App\Repositories\Global\BaseRepository;

class FaqRepository extends BaseRepository
{

    public function __construct(public Faq $faq)
    {
    }

    // FAQ-ları filtrlə və gətir
    public function getFaqs($request)
    {
        $query = $this->faq->with(['translates.language']);
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->whereHas('translates', function($q) use ($searchTerm) {
                $q->where('question', 'like', '%' . $searchTerm . '%')
                  ->orWhere('answer', 'like', '%' . $searchTerm . '%');
            });
        }
        
        return $query->ordered()->get();
    }
}
