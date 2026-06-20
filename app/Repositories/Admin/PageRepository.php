<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\PageRepositoryInterface;
use App\Models\Page;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class PageRepository extends BaseRepository implements PageRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Page $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
