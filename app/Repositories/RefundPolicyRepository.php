<?php

namespace App\Repositories;

use App\Models\RefundPolicy;
use App\Models\Language;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RefundPolicyRepository
{
    protected $refundPolicy;
    protected $language;

    public function __construct(RefundPolicy $refundPolicy, Language $language)
    {
        $this->refundPolicy = $refundPolicy;
        $this->language = $language;
    }

    /**
     * Get refund policy with translations
     *
     * @return \App\Models\RefundPolicy
     */
    public function getRefundPolicyWithTranslations()
    {
        return $this->refundPolicy->with(['translates.language'])->firstOrCreate(
            [],
            ['status' => true]
        );
    }

    /**
     * Get active languages
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLanguages()
    {
        return $this->language->getActiveLanguages();
    }

    /**
     * Update refund policy
     *
     * @param array $translations
     * @return \App\Models\RefundPolicy
     * @throws \Exception
     */
    public function updateRefundPolicy(array $translations)
    {
        return DB::transaction(function () use ($translations) {
            $refundPolicy = $this->refundPolicy->firstOrCreate(
                [],
                ['status' => true]
            );

            // Update translations
            foreach ($translations as $languageId => $translation) {
                if (!empty($translation['content'])) {
                    $refundPolicy->translates()->updateOrCreate(
                        ['language_id' => $languageId],
                        ['content' => trim($translation['content'])]
                    );
                }
            }

            // Clear cache for all languages
            $this->clearRefundPolicyCache();

            return $refundPolicy;
        });
    }

    /**
     * Clear refund policy cache for all languages
     */
    public function clearRefundPolicyCache()
    {
        $languages = $this->getActiveLanguages();
        foreach ($languages as $language) {
            Cache::forget("refund_policy_{$language->id}");
        }
    }
}
