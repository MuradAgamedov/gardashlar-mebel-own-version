<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\CommentRepositoryInterface;
use App\Interfaces\Services\Front\CommentServiceInterface;
use App\Services\BaseService;

class CommentService extends BaseService implements CommentServiceInterface
{
    protected CommentRepositoryInterface $commentRepository;

    public function __construct(CommentRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->commentRepository = $repository;
    }
}
