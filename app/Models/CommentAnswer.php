<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentAnswer extends Model
{
    protected $fillable = ['comment_id', 'text'];

    // Əlaqə: CommentAnswer -> Comment
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
