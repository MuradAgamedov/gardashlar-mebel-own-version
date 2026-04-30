<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Repositories\AboutRepository;
use App\Http\Requests\Admin\About\StoreAboutRequest;
use App\Http\Requests\Admin\About\UpdateAboutRequest;

class AboutController extends Controller
{
    public function __construct(public AboutRepository $repository)
    {
    }

    public function index()
    {
        $abouts = $this->repository->getAbout();
        return view('admin.abouts.index', compact('abouts'));
    }

    public function create()
    {
        $languages = Language::getActiveLanguages();
        return view('admin.abouts.create', compact('languages'));
    }

    public function store(StoreAboutRequest $request)
    {
        try {
            $this->repository->createAbout(
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
        $languages = Language::getActiveLanguages();
        return view('admin.abouts.edit', compact('about', 'languages'));
    }

    public function update(UpdateAboutRequest $request, About $about)
    {
        try {
            $this->repository->updateAbout(
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
            $this->repository->deleteAbout($about);
            
            return redirect()->route('admin.abouts.index')
                ->with('success', __('About information deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error occurred: :error', ['error' => $e->getMessage()]));;
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:abouts,id'
        ]);

        try {
            $this->repository->reorderAbouts($request->input('order'));
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
} 