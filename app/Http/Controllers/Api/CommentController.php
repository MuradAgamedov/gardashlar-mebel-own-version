<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Helpers\DictionaryHelper;
class CommentController extends Controller
{
    public function addComment(Request $request)
    {
        $lang = $request->header('locale', 'az');
        $data = $request->all();
        $comment = Comment::create([
            'user_id'    => $request->user()->id, // token-dən user id götürülür
            'product_id' => $request->product_id,
            'text'       => $request->text,
            'status'     => $request->status??"pending",
        ]);

        return response()->json([
            'success' => true,
            'message' => DictionaryHelper::trans('comment_added', $lang),
            'data'    => $comment
        ], 201);
    }
}
