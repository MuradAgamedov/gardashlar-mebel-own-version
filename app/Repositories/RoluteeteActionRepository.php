<?php

namespace App\Repositories;

use App\Models\RoluteeteAction;
use App\Models\RoluteeteActionTranslate;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoluteeteActionRepository
{
    protected $model;
    protected $translationModel;
    protected $language;
    protected $imageService;

    public function __construct(RoluteeteAction $model, Language $language, ImageService $imageService)
    {
        $this->model = $model;
        $this->translationModel = new RoluteeteActionTranslate();
        $this->language = $language;
        $this->imageService = $imageService;
    }

    /**
     * Get all actions with translations for index page
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllForIndex()
    {
        return $this->model->with('translates.language')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get active languages for forms
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLanguages()
    {
        return $this->language->all();
    }

    /**
     * Find action by ID with translations
     *
     * @param int $id
     * @return \App\Models\RoluteeteAction|null
     */
    public function findWithTranslations($id)
    {
        return $this->model->with('translates.language')->findOrFail($id);
    }

    /**
     * Create a new action with translations
     *
     * @param array $data
     * @param array $translations
     * @param \Illuminate\Http\UploadedFile|null $image
     * @return \App\Models\RoluteeteAction
     * @throws \Exception
     */
    public function createAction(array $data, array $translations, $image = null)
    {
        DB::beginTransaction();
        
        try {
            // Handle image upload if provided
            if ($image) {
                $data['image'] = $this->imageService->upload($image, 'actions');
            }
            
            // Create the action
            $action = $this->model->create($data);
            
            // Create translations
            foreach ($translations as $langId => $translation) {
                $action->translates()->create([
                    'language_id' => $langId,
                    'title' => $translation['title'] ?? null,
                    'description' => $translation['description'] ?? null,
                ]);
            }
            
            DB::commit();
            return $action;
            
        } catch (\Exception $e) {
            DB::rollBack();
            // Delete uploaded image if creation fails
            if (isset($data['image']) && file_exists(public_path($data['image']))) {
                unlink(public_path($data['image']));
            }
            throw $e;
        }
    }

    /**
     * Update an existing action with translations
     *
     * @param int $id
     * @param array $data
     * @param array $translations
     * @param \Illuminate\Http\UploadedFile|null $image
     * @return \App\Models\RoluteeteAction
     * @throws \Exception
     */
    public function updateAction($id, array $data, array $translations, $image = null)
    {
        DB::beginTransaction();
        
        try {
            $action = $this->model->findOrFail($id);
            $oldImage = $action->image;
            
            // Handle image upload if provided
            if ($image) {
                $data['image'] = $this->imageService->upload($image, 'actions');
                // Delete old image if exists and new one is uploaded
                if ($oldImage && file_exists(public_path($oldImage))) {
                    unlink(public_path($oldImage));
                }
            }
            
            // Update the action
            $action->update($data);
            
            // Update or create translations
            foreach ($translations as $langId => $translation) {
                $action->translates()->updateOrCreate(
                    [
                        'language_id' => $langId,
                        'action_id' => $action->id
                    ],
                    [
                        'title' => $translation['title'] ?? null,
                        'description' => $translation['description'] ?? null,
                    ]
                );
            }
            
            // Delete translations that were not included in the update
            $action->translates()->whereNotIn('language_id', array_keys($translations))->delete();
            
            DB::commit();
            return $action;
            
        } catch (\Exception $e) {
            DB::rollBack();
            // Delete newly uploaded image if update fails
            if (isset($data['image']) && file_exists(public_path($data['image']))) {
                unlink(public_path($data['image']));
            }
            throw $e;
        }
    }

    /**
     * Delete an action and its related data
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteAction($id)
    {
        $action = $this->model->findOrFail($id);
        $imagePath = $action->image;
        
        DB::beginTransaction();
        
        try {
            // Delete translations
            $action->translates()->delete();
            
            // Delete the action
            $deleted = $action->delete();
            
            // Delete the image file if exists
            if ($imagePath && file_exists(public_path($imagePath))) {
                unlink(public_path($imagePath));
            }
            
            DB::commit();
            return $deleted;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle the status of an action
     *
     * @param int $id
     * @return bool
     */
    public function toggleStatus($id)
    {
        $action = $this->model->findOrFail($id);
        $action->status = !$action->status;
        return $action->save();
    }
}
