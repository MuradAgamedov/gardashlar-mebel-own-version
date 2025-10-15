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
}
