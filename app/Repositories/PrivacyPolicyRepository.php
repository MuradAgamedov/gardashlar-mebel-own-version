<?php
namespace App\Repositories;

use App\Models\PrivacyPolicy;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PrivacyPolicyRepository extends BaseRepository
{
    public function __construct(public PrivacyPolicy $privacyPolicy)
    {
    }

    public function getPrivacyPolicy()
    {
        return $this->privacyPolicy->with('translates')->first();
    }

    public function getOrCreatePrivacyPolicy()
    {
        $privacyPolicy = $this->getPrivacyPolicy();
        
        if (!$privacyPolicy) {
            $privacyPolicy = $this->privacyPolicy->create();
        }
        
        return $privacyPolicy;
    }

    public function updatePrivacyPolicy(array $translates, $languages)
    {
        return DB::transaction(function () use ($translates, $languages) {
            $privacyPolicy = $this->getOrCreatePrivacyPolicy();

            foreach ($translates as $translate) {
                $privacyPolicy->translates()->updateOrCreate(
                    ['language_id' => $translate['language_id']],
                    ['content' => $translate['content']]
                );
            }

            foreach ($languages as $language) {
                Cache::forget("privacy_policy_{$language->id}");
            }

            return $privacyPolicy;
        });
    }
}
