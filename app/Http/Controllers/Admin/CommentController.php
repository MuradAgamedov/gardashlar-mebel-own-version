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
        $comment->delete();
        return redirect()->route('admin.comments.index')->with('success', 'Şərh silindi');
    }
    public function markAsRead($id)
    {
        $comment = Comment::find($id);
     
        $comment->update([
            'is_read' => $comment->is_read === 'active' ? 'passive' : 'active'
        ]);
    
        return redirect()->back()->with('success', 'Şərh statusu yeniləndi');
    }
    
    
    public function toggleApprove(Comment $comment)
{
    $comment->update([
        'status' => $comment->status === 'approved' ? 'pending' : 'approved'
    ]);

    return redirect()->back()->with('success', 'Şərhin təsdiq statusu dəyişdirildi');
}

    
// Cavabların siyahısı
public function answers(Comment $comment)
{
    $answers = $comment->answers()->latest()->get();
    return view('admin.comments.answers', compact('comment', 'answers'));
}

// Cavab əlavə etmək
public function storeAnswer(Request $request, Comment $comment)
{
    $request->validate([
        'text' => 'required|string|max:1000',
    ]);

    $comment->answers()->create([
        'text' => $request->text,
    ]);

    return redirect()->back()->with('success', 'Cavab əlavə olundu');
}

// Cavabı silmək
public function destroyAnswer(CommentAnswer $answer)
{
    $answer->delete();
    return redirect()->back()->with('success', 'Cavab silindi');
}

        
}
