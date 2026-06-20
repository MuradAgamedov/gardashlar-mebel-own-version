<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface LanguageRepositoryInterface extends BaseRepositoryInterface
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
     * Aktiv dillərin sayını əldə et
     */
    public function getActiveLanguagesCount(): int;

    /**
     * Bütün əsas dilləri sıfırla (is_main_language = false)
     */
    public function resetAllMainLanguages(): void;

    /**
     * Dilləri yenidən sırala
     */
    public function reorderLanguages(array $languages): bool;
}
