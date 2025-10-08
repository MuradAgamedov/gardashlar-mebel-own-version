<?php
namespace App\Repositories;

use App\Models\Image;
use App\Repositories\Global\BaseRepository;

class ImageRepository extends BaseRepository
{

    public function __construct(public Image $image)
    {
    }

    // Bütün şəkilləri gətir
    public function getImages()
    {
        return $this->image->with('translates.language')->get();
    }
}
