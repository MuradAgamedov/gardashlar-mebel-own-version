<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface LanguageServiceInterface extends BaseServiceInterface
{
    /**
     * Bütün dilləri sıralı şəkildə əldə et
     */
    public function getAllLanguages(): Collection;

    /**
     * Yalnız aktiv dilləri əldə et
     */
    public function getActiveLanguages(): Collection;

    /**
     * Əsas dili əldə et
     */
    public function getMainLanguage(): ?Model;

    /**
     * Yeni dil yarat (biznes qaydalarını tətbiq edərək)
     */
    public function storeLanguage(array $data): Model;

    /**
     * Mövcud dili yenilə (biznes qaydalarını tətbiq edərək)
     *
     * @throws \InvalidArgumentException
     */
    public function updateLanguage(Model $language, array $data): Model;

    /**
     * Dili sil (biznes qaydalarını yoxlayaraq)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteLanguage(Model $language): bool;

    /**
     * Dilləri yenidən sırala
     */
    public function reorderLanguages(array $languages): bool;
}
