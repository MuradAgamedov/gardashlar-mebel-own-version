<?php
namespace App\Repositories;

use App\Models\Comment;
use App\Repositories\Global\BaseRepository;

class CommentRepository extends BaseRepository
{

    public function __construct(public Comment $comment)
    {
    }

    // Şərhləri paginate ilə gətir
    public function getComments()
    {
        return $this->comment->with(['user','product'])->latest()->paginate(10);
    }
}
