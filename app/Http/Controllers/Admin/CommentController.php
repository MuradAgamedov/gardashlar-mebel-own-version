<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentAnswer;
use Illuminate\Http\Request;
use App\Repositories\CommentRepository;

class CommentController extends Controller
{
    public function __construct(public CommentRepository $repository)
    {
    }

    public function index()
    {
        $comments = $this->repository->getComments();
        return view("admin.comments.index", compact('comments'));
    }

    public function show(Comment $comment)
    {
        return view("admin.comments.show", compact('comment'));
    }

    public function destroy(Comment $comment)
    {
        $this->repository->deleteComment($comment);
        return redirect()->route('admin.comments.index')->with('success', 'Şərh silindi');
    }
    public function markAsRead($id)
    {
        $comment = Comment::find($id);
        $this->repository->toggleReadStatus($comment);
        return redirect()->back()->with('success', 'Şərh statusu yeniləndi');
    }
    
    
    public function toggleApprove(Comment $comment)
    {
        $this->repository->toggleApprove($comment);
        return redirect()->back()->with('success', 'Şərhin təsdiq statusu dəyişdirildi');
    }

    
    public function answers(Comment $comment)
    {
        $answers = $this->repository->getAnswers($comment);
        return view('admin.comments.answers', compact('comment', 'answers'));
    }

    public function storeAnswer(Request $request, Comment $comment)
    {
        $request->validate([
            'text' => 'required|string|max:1000',
        ]);

        $this->repository->storeAnswer($comment, ['text' => $request->text]);
        return redirect()->back()->with('success', 'Cavab əlavə olundu');
    }

    public function destroyAnswer(CommentAnswer $answer)
    {
        $this->repository->deleteAnswer($answer);
        return redirect()->back()->with('success', 'Cavab silindi');
    }

        
}
