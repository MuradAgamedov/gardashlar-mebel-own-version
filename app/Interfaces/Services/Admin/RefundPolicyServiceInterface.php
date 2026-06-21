<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface RefundPolicyServiceInterface extends BaseServiceInterface
{
    //

    /**
     * Get refund policy with translations
     *
     * @return \App\Models\RefundPolicy
     */
    public function getRefundPolicyWithTranslations();

    /**
     * Get active languages
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLanguages();
}
