<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface RefundPolicyRepositoryInterface extends BaseRepositoryInterface
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
