<?php
namespace App\Repositories;

use App\Models\ServiceStaticData;
use App\Repositories\Global\BaseRepository;

class ServiceStaticDataRepository extends BaseRepository
{

    public function __construct(public ServiceStaticData $serviceStaticData)
    {
    }

    public function getFirstOrCreate()
    {
        return ServiceStaticData::with(['translates.language'])->first() ?? ServiceStaticData::create([]);
    }
}
