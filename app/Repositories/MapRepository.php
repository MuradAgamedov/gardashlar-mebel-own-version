<?php
namespace App\Repositories;

use App\Models\Map;
use App\Repositories\Global\BaseRepository;

class MapRepository extends BaseRepository
{

    public function __construct(public Map $map)
    {
    }

    // Bütün xəritələri gətir
    public function getMaps()
    {
        return $this->GetData($this->map, [])->get();
    }
}
