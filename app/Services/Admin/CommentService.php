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
}
