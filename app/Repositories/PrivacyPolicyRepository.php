<?php
namespace App\Repositories;

use App\Models\PrivacyPolicy;
use App\Repositories\Global\BaseRepository;

class PrivacyPolicyRepository extends BaseRepository
{

    public function __construct(public PrivacyPolicy $privacyPolicy)
    {
    }

    // Gizlilik siyasətini gətir
    public function getPrivacyPolicy()
    {
        return $this->privacyPolicy->with('translates')->first();
    }

    // Gizlilik siyasəti yarat və ya gətir
    public function getOrCreatePrivacyPolicy()
    {
        $privacyPolicy = $this->getPrivacyPolicy();
        
        if (!$privacyPolicy) {
            $privacyPolicy = $this->privacyPolicy->create();
        }
        
        return $privacyPolicy;
    }
}
