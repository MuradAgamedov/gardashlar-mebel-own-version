<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\RefundPolicyRepositoryInterface;
use App\Models\Language;
use App\Models\RefundPolicy;
use App\Repositories\BaseRepository;

class RefundPolicyRepository extends BaseRepository implements RefundPolicyRepositoryInterface
{
    public function __construct(RefundPolicy $model)
    {
        parent::__construct($model);
    }

    public function getRefundPolicyWithTranslations()
    {
        return $this->model->newQuery()->with(['translates.language'])->firstOrCreate(
            [],
            ['status' => true]
        );
    }

    public function getActiveLanguages()
    {
        return Language::where('status', 1)->orderBy('order')->get();
    }
}
