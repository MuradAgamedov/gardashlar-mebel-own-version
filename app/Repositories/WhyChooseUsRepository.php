<?php
namespace App\Repositories;

use App\Models\WhyChooseUs;
use App\Repositories\Global\BaseRepository;

class WhyChooseUsRepository extends BaseRepository
{

    public function __construct(public WhyChooseUs $whyChooseUs)
    {
    }

    public function getFirstOrCreate()
    {
        return WhyChooseUs::with(['translates.language'])->first() ?? WhyChooseUs::create([]);
    }
}
