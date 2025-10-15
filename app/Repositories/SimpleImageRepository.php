<?php
namespace App\Repositories;

use App\Models\SimpleImage;
use App\Repositories\Global\BaseRepository;

class SimpleImageRepository extends BaseRepository
{

    public function __construct(public SimpleImage $simpleImage)
    {
    }

    public function getImages()
    {
        return $this->GetData($this->simpleImage, ['translates.language'])->get();
    }
}
