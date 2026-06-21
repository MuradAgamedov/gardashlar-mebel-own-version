<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface CommentServiceInterface extends BaseServiceInterface
{
    //

    public function getComments();

    public function deleteComment(\App\Models\Comment $comment);

    public function toggleReadStatus(\App\Models\Comment $comment);

    public function toggleApprove(\App\Models\Comment $comment);

    public function getAnswers(\App\Models\Comment $comment);

    public function storeAnswer(\App\Models\Comment $comment, array $data);

    public function deleteAnswer(\App\Models\CommentAnswer $answer);
}
