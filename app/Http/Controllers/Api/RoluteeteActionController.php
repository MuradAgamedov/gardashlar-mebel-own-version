<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoluteeteAction;
use App\Models\Language;

class RoluteeteActionController extends Controller
{
    public function index(Request $request)
    {
        // dil ID-si göndərilibsə götür, yoxdursa əsas dili seç
        $languageId = $request->header('language_id') ?? Language::getMainLanguage()->id;

        // yalnız statusu 1 olan aksiyalar
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
}
