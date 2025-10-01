<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\CommentAnswer;

class CommentAnswerSeeder extends Seeder
{
    public function run()
    {
        // 1. Əvvəlcə product_id = 2 olan şərhləri götürürük
        $comments = Comment::where('product_id', 2)->get();

        foreach ($comments as $comment) {
            // 2. Hər şərh üçün 3 cavab yaradaq
            for ($i = 1; $i <= 3; $i++) {
                CommentAnswer::create([
                    'comment_id' => $comment->id,
                    'text' => "Bu, şərh #{$comment->id} üçün test cavabıdır $i",
                ]);
            }
        }
    }
}
