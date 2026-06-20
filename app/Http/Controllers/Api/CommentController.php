<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CommentResource;
use App\Interfaces\Services\Front\CommentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\DictionaryHelper;

class CommentController extends Controller
{
    protected CommentServiceInterface $commentService;

    public function __construct(CommentServiceInterface $commentService)
    {
        $this->commentService = $commentService;
    }

    public function addComment(Request $request): JsonResponse
    {
        $lang = $request->header('locale', 'az');
        
        $comment = $this->commentService->create([
            'user_id'    => $request->user()->id, // token-dən user id götürülür
            'product_id' => $request->product_id,
            'text'       => $request->text,
            'status'     => $request->status ?? "pending",
        ]);

        return $this->created(new CommentResource($comment), DictionaryHelper::trans('comment_added', $lang));
    }
}
