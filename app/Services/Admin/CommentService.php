<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\CommentRepositoryInterface;
use App\Interfaces\Services\Admin\CommentServiceInterface;
use App\Services\BaseService;

class CommentService extends BaseService implements CommentServiceInterface
{
    protected CommentRepositoryInterface $commentRepository;

    public function __construct(CommentRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->commentRepository = $repository;
    }

    public function getComments()
    {
        return $this->commentRepository->getComments();
    }

    public function deleteComment(\App\Models\Comment $comment)
    {
        return $this->commentRepository->deleteComment($comment);
    }

    public function toggleReadStatus(\App\Models\Comment $comment)
    {
        return $this->commentRepository->toggleReadStatus($comment);
    }

    public function toggleApprove(\App\Models\Comment $comment)
    {
        return $this->commentRepository->toggleApprove($comment);
    }

    public function getAnswers(\App\Models\Comment $comment)
    {
        return $this->commentRepository->getAnswers($comment);
    }

    public function storeAnswer(\App\Models\Comment $comment, array $data)
    {
        return $this->commentRepository->storeAnswer($comment, $data);
    }

    public function deleteAnswer(\App\Models\CommentAnswer $answer)
    {
        return $this->commentRepository->deleteAnswer($answer);
    }
}
