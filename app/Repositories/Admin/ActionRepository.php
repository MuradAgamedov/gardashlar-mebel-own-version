<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ActionRepositoryInterface;
use App\Models\Action;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ActionRepository extends BaseRepository implements ActionRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Action $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getAction($request)
    {
        $query = $this->GetData($this->action, ['company','translates.language']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        return $query->paginate(15);
    }

    public function createAction(array $data, array $translates)
    {
        return DB::transaction(function () use ($data, $translates) {
            $action = $this->action->create([
                'company_id' => $data['company_id'],
                'icon' => $data['icon'] ?? null,
                'status' => $data['status'] ?? true,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
            ]);

            foreach ($translates as $translateData) {
                $translate = new ActionTranslate($translateData);
                
                if (isset($translateData['image'])) {
                    $imagePath = $this->imageService->upload($translateData['image'], 'actions');
                    $translate->image = $imagePath;
                }
                
                $action->translates()->save($translate);
            }

            return $action->load(['company', 'translates.language']);
        });
    }

    public function updateAction(Action $action, array $data, array $translates)
    {
        return DB::transaction(function () use ($action, $data, $translates) {
            $action->update([
                'company_id' => $data['company_id'],
                'icon' => $data['icon'] ?? null,
                'status' => $data['status'] ?? true,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
            ]);

            foreach ($translates as $translateData) {
                $translate = $action->translates()
                    ->where('language_id', $translateData['language_id'])
                    ->first();

                if ($translate) {
                    if (isset($translateData['image'])) {
                        if ($translate->image) {
                            $this->imageService->delete($translate->image);
                        }
                        
                        $imagePath = $this->imageService->upload($translateData['image'], 'actions');
                        $translateData['image'] = $imagePath;
                    }
                    
                    $translate->update($translateData);
                } else {
                    $translate = new ActionTranslate($translateData);
                    
                    if (isset($translateData['image'])) {
                        $imagePath = $this->imageService->upload($translateData['image'], 'actions');
                        $translate->image = $imagePath;
                    }
                    
                    $action->translates()->save($translate);
                }
            }

            return $action->load(['company', 'translates.language']);
        });
    }

    public function deleteAction(Action $action)
    {
        return DB::transaction(function () use ($action) {
            foreach ($action->translates as $translate) {
                if ($translate->image) {
                    $this->imageService->delete($translate->image);
                }
            }

            return $action->delete();
        });
    }
}
