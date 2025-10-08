<?php
namespace App\Repositories;

use App\Models\BlogPost;
use App\Repositories\Global\BaseRepository;

class BlogPostRepository extends BaseRepository
{

    public function __construct(public BlogPost $blogPost)
    {
    }

    // Blog postları filtrlə və gətir
    public function getPosts($request)
    {
        $query = $this->blogPost->with(['category.translates', 'translates.language']);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('blog_category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('translates', function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('card_description', 'like', '%' . $searchTerm . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
