<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\BecomeAPartnerRepositoryInterface;
use App\Models\BecomeAPartner;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class BecomeAPartnerRepository extends BaseRepository implements BecomeAPartnerRepositoryInterface
{
    public function __construct(BecomeAPartner $model)
    {
        parent::__construct($model);
    }

    public function updateBecomeAPartner($becomeAPartner, array $translations)
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
