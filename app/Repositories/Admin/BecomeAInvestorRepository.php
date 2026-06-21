<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\BecomeAInvestorRepositoryInterface;
use App\Models\BecomeAInvestor;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class BecomeAInvestorRepository extends BaseRepository implements BecomeAInvestorRepositoryInterface
{
    public function __construct(BecomeAInvestor $model)
    {
        parent::__construct($model);
    }

    public function updateBecomeAInvestor($becomeAInvestor, array $translations)
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
