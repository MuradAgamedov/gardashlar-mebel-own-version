<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\RoluteeteActionRepositoryInterface;
use App\Models\RoluteeteAction;
use App\Repositories\BaseRepository;

class RoluteeteActionRepository extends BaseRepository implements RoluteeteActionRepositoryInterface
{
    public function __construct(RoluteeteAction $model)
    {
        parent::__construct($model);
    }

    public function getActiveActions(int $languageId)
    {
        return $this->model->newQuery()
            ->where('status', 1)
            ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function addUserAction(int $userId, int $actionId)
    {
        return \App\Models\UserRoluteeteAction::updateOrCreate(
            [
                'user_id' => $userId,
                'roluteete_action_id' => $actionId,
            ],
            [
                'used' => false,
            ]
        );
    }

    public function getUserActions(int $userId, int $languageId)
    {
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return collect();
        }

        return $user->roluteeteActions()
            ->with(['roluteeteAction.translates' => function($q) use ($languageId) {
                $q->where('language_id', $languageId);
            }])
            ->get()
            ->map(function ($userAction) {
                $tr = $userAction->roluteeteAction->translates->first();
    
                return [
                    'id'      => $userAction->roluteeteAction->id,
                    'percent' => $userAction->roluteeteAction->percent,
                    'title'   => $tr ? $tr->title : null,
                    'used'    => $userAction->used,
                    'image'   => $userAction->roluteeteAction->image 
                        ? asset('storage/' . $userAction->roluteeteAction->image) 
                        : null,
                ];
            });
    }
}
