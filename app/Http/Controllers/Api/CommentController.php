<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function addComment(Request $request)
    {
        $data = $request->all();
        $comment = Comment::create([
            'user_id'    => $request->user()->id, // token-dən user id götürülür
            'product_id' => $request->product_id,
            'text'       => $request->text,
            'status'     => $request->status??"pending",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment əlavə olundu',
            'data'    => $comment
        ], 201);
    }
}
