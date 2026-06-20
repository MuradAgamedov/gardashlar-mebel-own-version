<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\LanguageRepositoryInterface;
use App\Models\Language;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class LanguageRepository extends BaseRepository implements LanguageRepositoryInterface
{
    public function __construct(Language $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllLanguages(): Collection
    {
        return $this->query()->orderBy('order')->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveLanguages(): Collection
    {
        return $this->query()
            ->where('status', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getMainLanguage(): ?Model
    {
        return $this->query()
            ->where('is_main_language', true)
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveLanguagesCount(): int
    {
        return $this->query()
            ->where('status', true)
            ->count();
    }

    /**
     * {@inheritdoc}
     */
    public function resetAllMainLanguages(): void
    {
        $this->query()
            ->where('is_main_language', true)
            ->update(['is_main_language' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function reorderLanguages(array $languages): bool
    {
        foreach ($languages as $languageData) {
            $language = $this->find($languageData['id']);
            if ($language) {
                $language->update(['order' => $languageData['order']]);
            }
        }
        return true;
    }
}
