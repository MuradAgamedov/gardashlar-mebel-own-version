<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoluteeteAction;
use App\Models\UserRoluteeteAction;
use App\Models\Language;
use App\Traits\LocaleFromHeader;
class RoluteeteActionController extends Controller
{
    use LocaleFromHeader;
    public function index(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);

        $actions = RoluteeteAction::where('status', 1)
            ->with(['translates' => function($q) use ($languageId) {
                $q->where('language_id', $languageId);
            }])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($action) {
                $tr = $action->translates->first();
                return [
                    'id'      => $action->id,
                    'percent' => $action->percent,
                    'title'   => $tr ? $tr->title : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $actions,
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
}
