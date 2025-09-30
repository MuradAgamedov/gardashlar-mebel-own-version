<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with(['user','product'])->latest()->paginate(10);
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
    public function markAsRead(Comment $comment)
    {
        dd(!(int)$comment->is_read);
        $comment->update(['is_read' => !(int)$comment->is_read]);
        return redirect()->back()->with('success', 'Şərh statusu yeniləndi');
    }
    
    
    
    
        
}
