<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoluteeteAction;
use App\Models\RoluteeteActionTranslate;
use App\Models\Language;
use App\Services\ImageService;

class RoluteeteActionController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $actions = RoluteeteAction::with('translates.language')->orderBy('id', 'desc')->get();
        return view("admin.RoluteeteAction.index", compact('actions'));
    }

    public function create()
    {
        $languages = Language::all();
        return view("admin.RoluteeteAction.create", compact('languages'));
    }

    public function store(Request $request)
    {
        $languages = Language::all();

        $request->validate([
            'percent' => 'nullable|numeric|min:1|max:100',
            'image' => 'nullable|image|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->upload($request->file('image'), 'roluteete_actions');
        }

        $action = RoluteeteAction::create([
            'percent' => $request->input('percent'),
            'image' => $imagePath,
        ]);

        foreach ($languages as $language) {
            $translationData = $request->input("translations.{$language->id}", []);
            $action->translates()->create([
                'language_id' => $language->id,
                'title' => $translationData['title'] ?? null,
                'type' => $request->input('type') ?? null,
            ]);
        }

        return redirect()->route('admin.roulette-actions.index')
                         ->with('success', 'Roluteete Action created successfully!');
    }

    public function edit($id)
    {
        $action = RoluteeteAction::with('translates')->findOrFail($id);
        $languages = Language::all();
        return view("admin.RoluteeteAction.edit", compact('action', 'languages'));
    }

    public function update(Request $request, $id)
    {
        $action = RoluteeteAction::findOrFail($id);
        $languages = Language::all();

        $request->validate([
            'percent' => 'nullable|numeric|min:1|max:100',
            'image' => 'nullable|image|max:5120',
        ]);

        $imagePath = $action->image;
        if ($request->hasFile('image')) {
            // köhnə şəkli silmək istəyirsənsə burda əlavə et
            $imagePath = $this->imageService->upload($request->file('image'), 'roluteete_actions');
        }

        $action->update([
            'percent' => $request->input('percent'),
            'image' => $imagePath,
        ]);

        // Tərcümələri update et
        foreach ($languages as $language) {
            $translationData = $request->input("translations.{$language->id}", []);
            $translate = RoluteeteActionTranslate::where('roluteete_action_id', $action->id)
                ->where('language_id', $language->id)
                ->first();

            if ($translate) {
                $translate->update([
                    'title' => $translationData['title'] ?? null,
                    'type' => $request->input('type') ?? null,
                ]);
            } else {
                $action->translates()->create([
                    'language_id' => $language->id,
                    'title' => $translationData['title'] ?? null,
                    'type' => $request->input('type') ?? null,
                ]);
            }
        }

        return redirect()->route('admin.roulette-actions.index')
                         ->with('success', 'Roluteete Action updated successfully!');
    }

    public function destroy($id)
    {
        $action = RoluteeteAction::findOrFail($id);

        // Əgər şəkilləri də silmək istəsən:
        // if ($action->image) {
        //     $this->imageService->delete($action->image);
        // }

        $action->delete();

        return redirect()->route('admin.roulette-actions.index')
                         ->with('success', 'Roluteete Action deleted successfully!');
    }


    public function toggleStatus($id)
{
    $action = RoluteeteAction::findOrFail($id);
    $action->status = !$action->status;
    $action->save();

    return redirect()->route('admin.roulette-actions.index')
                     ->with('success', 'Status changed successfully!');
}

}
