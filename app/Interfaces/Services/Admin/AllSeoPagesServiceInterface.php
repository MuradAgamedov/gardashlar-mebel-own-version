<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface AllSeoPagesServiceInterface extends BaseServiceInterface
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
