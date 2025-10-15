<?php
namespace App\Repositories;

use App\Models\BecomeAPartner;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\DB;

class BecomeAPartnerRepository extends BaseRepository
{
    public function __construct(public BecomeAPartner $becomeAPartner)
    {
    }

    public function getFirstOrCreate()
    {
        return BecomeAPartner::getFirstOrCreate();
    }

    public function updateBecomeAPartner(BecomeAPartner $becomeAPartner, array $translations)
    {
        return DB::transaction(function () use ($becomeAPartner, $translations) {
            foreach ($translations as $languageId => $translation) {
                $becomeAPartner->translates()->updateOrCreate(
                    ['language_id' => $languageId],
                    [
                        'content' => trim($translation['content'] ?? ''),
                    ]
                );
            }

            return $becomeAPartner->load('translates.language');
        });
    }
}
