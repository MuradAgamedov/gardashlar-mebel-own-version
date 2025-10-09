<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\RoluteeteAction;
use App\Models\Language;
use App\Services\ImageService;
use App\Repositories\RoluteeteActionRepository;

class RoluteeteActionController extends Controller
{
    protected $imageService;
    protected $repository;

    public function __construct(ImageService $imageService, RoluteeteActionRepository $repository)
    {
        $this->imageService = $imageService;
        $this->repository = $repository;
    }

    public function index()
    {
        $actions = $this->repository->getAllForIndex();
        return view("admin.RoluteeteAction.index", compact('actions'));
    }

    public function create()
    {
        $languages = $this->repository->getActiveLanguages();
        return view("admin.RoluteeteAction.create", compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'percent' => 'nullable|numeric|min:1|max:100',
            'image' => 'nullable|image|max:5120',
            'type' => 'nullable|string',
            'translations' => 'required|array',
            'translations.*' => 'array:title,type',
        ]);

        $data = [
            'percent' => $request->input('percent'),
            'status' => true,
        ];

        $translations = [];
        foreach ($request->input('translations', []) as $langId => $translation) {
            $translations[$langId] = [
                'title' => $translation['title'] ?? null,
                'description' => $translation['description'] ?? null,
            ];
        }

        $action = $this->repository->createAction(
            $data,
            $translations,
            $request->file('image')
        );

        return redirect()
            ->route('admin.roulette-actions.index')
            ->with('success', 'Roluteete Action created successfully!');
    }

    public function edit($id)
    {
        $action = $this->repository->findWithTranslations($id);
        $languages = $this->repository->getActiveLanguages();
        return view("admin.RoluteeteAction.edit", compact('action', 'languages'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'percent' => 'nullable|numeric|min:1|max:100',
            'image' => 'nullable|image|max:5120',
            'type' => 'nullable|string',
            'translations' => 'required|array',
            'translations.*' => 'array:title,type',
        ]);

        $data = [
            'percent' => $request->input('percent'),
        ];

        $translations = [];
        foreach ($request->input('translations', []) as $langId => $translation) {
            $translations[$langId] = [
                'title' => $translation['title'] ?? null,
                'description' => $translation['description'] ?? null,
            ];
        }

        $action = $this->repository->updateAction(
            $id,
            $data,
            $translations,
            $request->file('image')
        );

        return redirect()
            ->route('admin.roulette-actions.index')
            ->with('success', 'Roluteete Action updated successfully!');
    }

    public function destroy($id)
    {
        $this->repository->deleteAction($id);

        return redirect()
            ->route('admin.roulette-actions.index')
            ->with('success', 'Roluteete Action deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $this->repository->toggleStatus($id);

        return redirect()
            ->route('admin.roulette-actions.index')
            ->with('success', 'Status changed successfully!');
    }
}
