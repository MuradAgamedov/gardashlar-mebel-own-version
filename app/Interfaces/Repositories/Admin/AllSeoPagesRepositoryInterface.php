<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface AllSeoPagesRepositoryInterface extends BaseRepositoryInterface
{
    //

    /**
     * Get the first (and only) SEO pages record or create it if it doesn't exist
     */
    public function getOrCreate();

    /**
     * Get the current SEO pages data
     */
    public function get();
}
