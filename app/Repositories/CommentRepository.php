<?php
namespace App\Repositories;

use App\Models\Comment;
use App\Models\CommentAnswer;
use App\Repositories\Global\BaseRepository;

class CommentRepository extends BaseRepository
{
    public function __construct(public Comment $comment)
    {
    }

    public function getComments()
    {
        return $this->comment->with(['user','product'])->latest()->paginate(10);
    }

    public function deleteComment(Comment $comment)
    {
        return $comment->delete();
    }

    public function toggleReadStatus(Comment $comment)
    {
        $comment->update([
            'is_read' => $comment->is_read === 'active' ? 'passive' : 'active'
        ]);
        return $comment;
    }

    public function toggleApprove(Comment $comment)
    {
        $comment->update([
            'status' => $comment->status === 'approved' ? 'pending' : 'approved'
        ]);
        return $comment;
    }

    public function getAnswers(Comment $comment)
    {
        return $comment->answers()->latest()->get();
    }

    public function storeAnswer(Comment $comment, array $data)
    {
        return $comment->answers()->create($data);
    }

    public function deleteAnswer(CommentAnswer $answer)
    {
        return $answer->delete();
    }
}
