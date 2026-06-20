<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\About\ReorderAboutRequest;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\AboutServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Models\About;
use App\Http\Requests\Admin\About\StoreAboutRequest;
use App\Http\Requests\Admin\About\UpdateAboutRequest;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function __construct(
        protected AboutServiceInterface $aboutService,
        protected LanguageServiceInterface $languageService
    ) {
    }

    public function index()
    {
        $abouts = $this->aboutService->getAbout();
        return view('admin.abouts.index', compact('abouts'));
    }

    public function create()
    {
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.abouts.create', compact('languages'));
    }

    public function store(StoreAboutRequest $request)
    {
        try {
            $this->aboutService->createAbout(
                [
                    'date' => $request->input('date'),
                    'status' => $request->input('status', true)
                ],
                $request->input('translates'),
                $request->file('image')
            );

            return redirect()->route('admin.abouts.index')
                ->with('success', __('About information created successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));;
        }
    }

    public function edit(About $about)
    {
        $about->load('translates');
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.abouts.edit', compact('about', 'languages'));
    }

    public function update(UpdateAboutRequest $request, About $about)
    {
        try {
            $this->aboutService->updateAbout(
                $about,
                [
                    'date' => $request->input('date'),
                    'status' => $request->input('status', true)
                ],
                $request->input('translates'),
                $request->file('image')
            );

            return redirect()->route('admin.abouts.index')
                ->with('success', __('About information updated successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));;
        }
    }

    public function destroy(About $about)
    {
        try {
            $this->aboutService->deleteAbout($about);

            return redirect()->route('admin.abouts.index')
                ->with('success', __('About information deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));;
        }
    }

    public function reorder(ReorderAboutRequest $request)
    {
        $request->validated();

        try {
            $this->aboutService->reorderAbouts($request->input('order'));
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}