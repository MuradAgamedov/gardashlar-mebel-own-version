<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\CommentAnswer;

class CommentAnswerSeeder extends Seeder
{
    public function run()
    {
        // 1. Product ID 47 üçün iki şərh əlavə edək
        $commentsData = [
            [
                'user_id' => null, // admin tərəfindən yaradılır
                'product_id' => 47,
                'text' => 'Test şərh 1',
                'status' => 'approved',
                'is_read' => 'active'
            ],
            [
                'user_id' => null,
                'product_id' => 47,
                'text' => 'Test şərh 2',
                'status' => 'approved',
                'is_read' => 'active'
            ]
        ];

        foreach ($commentsData as $data) {
            $comment = Comment::create($data);

            // Hər şərh üçün 2 cavab əlavə edək
            for ($i = 1; $i <= 2; $i++) {
                CommentAnswer::create([
                    'comment_id' => $comment->id,
                    'text' => "Şərh #{$comment->id} üçün cavab $i",
                ]);
            }
        }
    }
}
