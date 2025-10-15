<?php
namespace App\Repositories;

use App\Models\BecomeAInvestor;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\DB;

class BecomeAInvestorRepository extends BaseRepository
{
    public function __construct(public BecomeAInvestor $becomeAInvestor)
    {
    }

    public function getFirstOrCreate()
    {
        return BecomeAInvestor::getFirstOrCreate();
    }

    public function updateBecomeAInvestor(BecomeAInvestor $becomeAInvestor, array $translations)
    {
        return DB::transaction(function () use ($becomeAInvestor, $translations) {
            foreach ($translations as $languageId => $translation) {
                $becomeAInvestor->translates()->updateOrCreate(
                    ['language_id' => $languageId],
                    [
                        'content' => trim($translation['content'] ?? ''),
                    ]
                );
            }

            return $becomeAInvestor->load('translates.language');
        });
    }
}
