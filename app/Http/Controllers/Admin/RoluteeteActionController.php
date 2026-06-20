<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RoluteeteAction\UpdateRoluteeteActionRequest;

use App\Http\Requests\Admin\RoluteeteAction\StoreRoluteeteActionRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Models\RoluteeteAction;
use App\Models\RoluteeteActionTranslate;
use App\Services\ImageService;

class RoluteeteActionController extends Controller
{
    public function __construct(protected LanguageServiceInterface $languageService, protected ImageService $imageService)
    {
}

    public function index()
    {
        $actions = RoluteeteAction::with('translates.language')->orderBy('id', 'desc')->get();
        return view("admin.RoluteeteAction.index", compact('actions'));
    }

    public function create()
    {
        $languages = $this->languageService->getAllLanguages();
        return view("admin.RoluteeteAction.create", compact('languages'));
    }

    public function store(StoreRoluteeteActionRequest $request)
    {
        $languages = $this->languageService->getAllLanguages();

        $request->validated();

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
                         ->with('success', __('Roulette Action created successfully!'));
    }

    public function edit($id)
    {
        $action = RoluteeteAction::with('translates')->findOrFail($id);
        $languages = $this->languageService->getAllLanguages();
        return view("admin.RoluteeteAction.edit", compact('action', 'languages'));
    }

    public function update(UpdateRoluteeteActionRequest $request, $id)
    {
        $action = $this->languageService->findOrFail($id);
        $languages = $this->languageService->getAllLanguages();

        $request->validated();

        $imagePath = $action->image;
        if ($request->hasFile('image')) {
            // köhnə şəkli silmək istəyirsənsə burda əlavə et
            $imagePath = $this->imageService->upload($request->file('image'), 'roluteete_actions');
        }

        $action->update([
            'percent' => $request->input('percent'),
            'image' => $imagePath,
            "percent_value" => "nullable"
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
                         ->with('success', __('Roulette Action updated successfully!'));
    }

    public function destroy($id)
    {
        $action = $this->languageService->findOrFail($id);

        // Əgər şəkilləri də silmək istəsən:
        // if ($action->image) {
        //     $this->imageService->delete($action->image);
        // }

        $action->delete();

        return redirect()->route('admin.roulette-actions.index')
                         ->with('success', __('Roulette Action deleted successfully!'));
    }


    public function toggleStatus($id)
{
    $action = $this->languageService->findOrFail($id);
    $action->status = !$action->status;
    $action->save();

    return redirect()->route('admin.roulette-actions.index')
                     ->with('success', __('Status changed successfully!'));
}

}