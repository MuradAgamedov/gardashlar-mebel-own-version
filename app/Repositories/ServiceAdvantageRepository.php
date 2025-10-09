<?php

namespace App\Repositories;

use App\Models\Service;
use App\Models\ServiceAdvantage;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceAdvantageRepository
{
    protected $serviceAdvantage;
    protected $service;
    protected $language;
    protected $imageService;

    public function __construct(
        ServiceAdvantage $serviceAdvantage, 
        Service $service, 
        Language $language,
        ImageService $imageService
    ) {
        $this->serviceAdvantage = $serviceAdvantage;
        $this->service = $service;
        $this->language = $language;
        $this->imageService = $imageService;
    }

    /**
     * Get all advantages for a service with translations
     *
     * @param int $serviceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getServiceAdvantages($serviceId)
    {
        return $this->serviceAdvantage->with(['translates.language'])
            ->where('service_id', $serviceId)
            ->orderBy('order')
            ->get();
    }

    /**
     * Find advantage by ID with translations
     *
     * @param int $id
     * @return \App\Models\ServiceAdvantage
     */
    public function findWithTranslations($id)
    {
        return $this->serviceAdvantage->with(['translates.language'])->findOrFail($id);
    }

    /**
     * Get service with translations
     *
     * @param int $serviceId
     * @return \App\Models\Service
     */
    public function getServiceWithTranslations($serviceId)
    {
        return $this->service->with(['translates.language'])
            ->findOrFail($serviceId);
    }

    /**
     * Get active languages ordered by order field
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLanguages()
    {
        return $this->language->active()
            ->orderBy('order')
            ->get();
    }

    /**
     * Create a new service advantage with translations
     *
     * @param array $data
     * @param array $translations
     * @param \Illuminate\Http\UploadedFile|null $icon
     * @return \App\Models\ServiceAdvantage
     * @throws \Exception
     */
    public function createAdvantage(array $data, array $translations, $icon = null)
    {
        DB::beginTransaction();
        
        try {
            // Handle icon upload
            if ($icon) {
                $data['icon'] = $this->imageService->upload($icon, 'service-advantages');
            }
            
            // Create the advantage
            $advantage = $this->serviceAdvantage->create($data);
            
            // Create translations
            foreach ($translations as $translation) {
                $advantage->translates()->create([
                    'language_id' => $translation['language_id'],
                    'title' => $translation['title'] ?? null,
                    'icon_alt_text' => $translation['icon_alt_text'] ?? null,
                    'description' => $translation['description'] ?? null,
                ]);
            }
            
            DB::commit();
            return $advantage;
            
        } catch (\Exception $e) {
            DB::rollBack();
            // Delete uploaded icon if creation fails
            if (isset($data['icon']) && file_exists(public_path($data['icon']))) {
                unlink(public_path($data['icon']));
            }
            throw $e;
        }
    }

    /**
     * Update an existing advantage with translations
     *
     * @param int $id
     * @param array $data
     * @param array $translations
     * @param \Illuminate\Http\UploadedFile|null $icon
     * @return \App\Models\ServiceAdvantage
     * @throws \Exception
     */
    public function updateAdvantage($id, array $data, array $translations, $icon = null)
    {
        DB::beginTransaction();
        
        try {
            $advantage = $this->serviceAdvantage->findOrFail($id);
            $oldIcon = $advantage->icon;
            
            // Handle icon upload if provided
            if ($icon) {
                $data['icon'] = $this->imageService->update($icon, $oldIcon, 'service-advantages');
            }
            
            // Update the advantage
            $advantage->update($data);
            
            // Delete existing translations
            $advantage->translates()->delete();
            
            // Create new translations
            foreach ($translations as $translation) {
                $advantage->translates()->create([
                    'language_id' => $translation['language_id'],
                    'title' => $translation['title'] ?? null,
                    'icon_alt_text' => $translation['icon_alt_text'] ?? null,
                    'description' => $translation['description'] ?? null,
                ]);
            }
            
            DB::commit();
            return $advantage;
            
        } catch (\Exception $e) {
            DB::rollBack();
            // Delete newly uploaded icon if update fails
            if (isset($data['icon']) && file_exists(public_path($data['icon']))) {
                unlink(public_path($data['icon']));
            }
            throw $e;
        }
    }

    /**
     * Delete an advantage and its related data
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteAdvantage($id)
    {
        $advantage = $this->serviceAdvantage->findOrFail($id);
        $iconPath = $advantage->icon;
        
        DB::beginTransaction();
        
        try {
            // Delete translations
            $advantage->translates()->delete();
            
            // Delete the advantage
            $deleted = $advantage->delete();
            
            // Delete the icon file if exists
            if ($iconPath && file_exists(public_path($iconPath))) {
                unlink(public_path($iconPath));
            }
            
            DB::commit();
            return $deleted;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle the status of an advantage
     *
     * @param int $id
     * @return bool
     */
    public function toggleStatus($id)
    {
        $advantage = $this->serviceAdvantage->findOrFail($id);
        $advantage->status = !$advantage->status;
        return $advantage->save();
    }

    /**
     * Update the order of advantages
     *
     * @param array $advantages
     * @param int $serviceId
     * @return bool
     */
    public function updateOrder(array $advantages, $serviceId)
    {
        DB::beginTransaction();
        
        try {
            foreach ($advantages as $item) {
                $this->serviceAdvantage->where('id', $item['id'])
                    ->where('service_id', $serviceId)
                    ->update(['order' => $item['order']]);
            }
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
