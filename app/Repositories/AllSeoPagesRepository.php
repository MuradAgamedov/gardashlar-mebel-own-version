<?php

namespace App\Repositories;

use App\Models\AllSeoPages;

class AllSeoPagesRepository
{
    protected $model;

    public function __construct(AllSeoPages $model)
    {
        $this->model = $model;
    }

    /**
     * Get the first (and only) SEO pages record or create it if it doesn't exist
     */
    public function getOrCreate()
    {
        return $this->model->firstOrCreate(
            [],
            [
                'is_indexed' => true,
                'is_followed' => true,
                'page_header' => null,
                'page_footer' => null,
            ]
        );
    }

    /**
     * Update the SEO pages record
     */
    public function update(array $data)
    {
        $seoPages = $this->getOrCreate();
        
        return $seoPages->update([
            'is_indexed' => isset($data['is_indexed']) ? (bool)$data['is_indexed'] : false,
            'is_followed' => isset($data['is_followed']) ? (bool)$data['is_followed'] : false,
            'page_header' => $data['page_header'] ?? null,
            'page_footer' => $data['page_footer'] ?? null,
        ]);
    }

    /**
     * Get the current SEO pages data
     */
    public function get()
    {
        return $this->getOrCreate();
    }
}
