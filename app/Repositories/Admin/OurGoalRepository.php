<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\OurGoalRepositoryInterface;
use App\Models\OurGoal;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class OurGoalRepository extends BaseRepository implements OurGoalRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(OurGoal $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getOurGoals()
    {
        return $this->GetData($this->ourGoal, ['translates.language'])->get();
    }

    public function createOurGoal(array $data, array $translations)
    {
        $ourGoal = $this->ourGoal->create($data);

        foreach ($translations as $languageId => $translation) {
            if (!empty($translation['title'])) {
                $ourGoal->translates()->create([
                    'language_id' => $languageId,
                    'title' => $translation['title'],
                    'description' => $translation['description'],
                ]);
            }
        }

        return $ourGoal;
    }

    public function updateOurGoal(OurGoal $ourGoal, array $data, array $translations)
    {
        $ourGoal->update($data);

        foreach ($translations as $languageId => $translation) {
            if (!empty($translation['title'])) {
                $ourGoal->translates()->updateOrCreate(
                    ['language_id' => $languageId],
                    [
                        'title' => $translation['title'],
                        'description' => $translation['description'],
                    ]
                );
            }
        }

        return $ourGoal;
    }

    public function deleteOurGoal(OurGoal $ourGoal)
    {
        return $ourGoal->delete();
    }

    public function reorderOurGoals(array $items)
    {
        foreach ($items as $index => $id) {
            $this->ourGoal->where('id', $id)->update(['order' => $index + 1]);
        }
        return true;
    }
}
