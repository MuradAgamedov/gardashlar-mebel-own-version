<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ContactMessageRepositoryInterface;
use App\Models\ContactMessage;
use App\Repositories\BaseRepository;

class ContactMessageRepository extends BaseRepository implements ContactMessageRepositoryInterface
{
    public function __construct(ContactMessage $model)
    {
        parent::__construct($model);
    }
}
