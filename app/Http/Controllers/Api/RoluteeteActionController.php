<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RoluteeteAction\AddActionRequest;
use App\Http\Resources\Api\RoluteeteActionResource;
use App\Interfaces\Services\Front\RoluteeteActionServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\LocaleFromHeader;

class RoluteeteActionController extends Controller
{
    use LocaleFromHeader;

    protected RoluteeteActionServiceInterface $roluteeteActionService;

    public function __construct(RoluteeteActionServiceInterface $roluteeteActionService)
    {
        $this->roluteeteActionService = $roluteeteActionService;
    }

    public function index(Request $request): JsonResponse
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        $actions = $this->roluteeteActionService->getActiveActions($languageId);

        return $this->success(RoluteeteActionResource::collection($actions));
    }

    public function add(AddActionRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->error('You must be logged in to add an action!', 401);
        }

        $userAction = $this->roluteeteActionService->addUserAction($user->id, $request->roluteete_action_id);

        return $this->success($userAction, 'Action successfully added to user!');
    }

    public function getUserActions(Request $request): JsonResponse
    {
        $user = $request->user();
    
        if (!$user) {
            return $this->error('You must be logged in to see your actions!', 401);
        }
    
        $languageId = $this->getLanguageIdFromHeader($request);
        $actions = $this->roluteeteActionService->getUserActions($user->id, $languageId);
    
        return $this->success($actions);
    }
}
