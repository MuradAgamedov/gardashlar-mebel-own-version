<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\LanguageRepositoryInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class LanguageService extends BaseService implements LanguageServiceInterface
{
    /**
     * @var LanguageRepositoryInterface
     */
    protected $languageRepository;

    public function __construct(LanguageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->languageRepository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllLanguages(): Collection
    {
        return $this->languageRepository->getAllLanguages();
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveLanguages(): Collection
    {
        return $this->languageRepository->getActiveLanguages();
    }

    /**
     * {@inheritdoc}
     */
    public function getMainLanguage(): ?Model
    {
        return $this->languageRepository->getMainLanguage();
    }

    /**
     * {@inheritdoc}
     */
    public function storeLanguage(array $data): Model
    {
        // Əgər bu dil əsas dil olaraq təyin olunursa, digər əsas dilləri sıfırla
        if (!empty($data['is_main_language'])) {
            $this->languageRepository->resetAllMainLanguages();
        }

        return $this->languageRepository->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function updateLanguage(Model $language, array $data): Model
    {
        $currentIsMain = $language->is_main_language;
        $newIsMain = $data['is_main_language'] ?? false;
        $currentStatus = $language->status;
        $newStatus = $data['status'] ?? false;

        // Əsas dili deaktiv etmə yoxlaması
        if ($currentIsMain && !$newIsMain) {
            throw new \InvalidArgumentException(
                __('Cannot deactivate main language. Please set another language as main first.')
            );
        }

        // Yeganə aktiv dili deaktiv etmə yoxlaması
        if ($currentStatus && !$newStatus) {
            $activeCount = $this->languageRepository->getActiveLanguagesCount();
            if ($activeCount <= 1) {
                throw new \InvalidArgumentException(
                    __('Cannot deactivate the only active language. Please activate another language first.')
                );
            }
        }

        // Əgər bu dil yeni əsas dil olaraq təyin olunursa, digərlərini sıfırla
        if ($newIsMain && !$currentIsMain) {
            $this->languageRepository->resetAllMainLanguages();
        }

        $language->update($data);

        return $language;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteLanguage(Model $language): bool
    {
        // Əsas dili silmə yoxlaması
        if ($language->is_main_language) {
            throw new \InvalidArgumentException(
                __('Cannot delete the main language. Please set another language as main first.')
            );
        }

        // Yeganə aktiv dili silmə yoxlaması
        if ($language->status) {
            $activeCount = $this->languageRepository->getActiveLanguagesCount();
            if ($activeCount <= 1) {
                throw new \InvalidArgumentException(
                    __('Cannot delete the only active language. Please activate another language first.')
                );
            }
        }

        return $language->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function reorderLanguages(array $languages): bool
    {
        return $this->languageRepository->reorderLanguages($languages);
    }
}
