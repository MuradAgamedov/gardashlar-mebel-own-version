<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\EmailRepositoryInterface;
use App\Models\Email;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class EmailRepository extends BaseRepository implements EmailRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Email $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
