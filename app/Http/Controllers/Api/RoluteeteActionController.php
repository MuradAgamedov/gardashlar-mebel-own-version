<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RoluteeteAction\AddActionRequest;
use App\Http\Resources\Api\RoluteeteActionResource;
use App\Models\RoluteeteAction;
use App\Models\UserRoluteeteAction;
use Illuminate\Http\Request;
use App\Traits\LocaleFromHeader;
class RoluteeteActionController extends Controller
{
    use LocaleFromHeader;
    public function index(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);

        $actions = RoluteeteAction::where('status', 1)
            ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => RoluteeteActionResource::collection($actions),
        ]);
    }

    public function add(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "You must be logged in to add an action!"
            ], 401);
        }

        $request->validate([
            'roluteete_action_id' => 'required|exists:roluteete_actions,id',
        ]);

        // User üçün aksiya əlavə et (dublikat olmadan)
        $userAction = UserRoluteeteAction::updateOrCreate(
            [
                'user_id' => $user->id,
                'roluteete_action_id' => $request->roluteete_action_id,
            ],
            [
                'used' => false,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Action successfully added to user!',
            'data'    => $userAction
        ]);
    }

    public function getUserActions(Request $request)
    {
        $user = $request->user();
    
        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "You must be logged in to see your actions!"
            ], 401);
        }
    
        $languageId = $this->getLanguageIdFromHeader($request);
    
        $actions = $user->roluteeteActions()
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
    
        return response()->json([
            'success' => true,
            'data'    => $actions,
        ]);
    }
    
}
