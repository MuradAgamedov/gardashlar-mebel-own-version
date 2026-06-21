<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\PrivacyPolicyRepositoryInterface;
use App\Models\PrivacyPolicy;
use App\Repositories\BaseRepository;
use App\Services\ImageService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PrivacyPolicyRepository extends BaseRepository implements PrivacyPolicyRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(PrivacyPolicy $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
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
