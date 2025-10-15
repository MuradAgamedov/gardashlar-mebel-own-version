<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerSlider;
use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Repositories\PartnerSliderRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\PartnerSlider\StorePartnerSliderRequest;
use App\Http\Requests\Admin\PartnerSlider\UpdatePartnerSliderRequest;

class PartnerSliderController extends Controller
{
    public function __construct(
        protected ImageService $imageService,
        public PartnerSliderRepository $repository,
        public LanguageRepository $languageRepository
    ) {
    }

    public function index()
    {
        $sliders = $this->repository->getPartnerSliders();
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.partner-sliders.index', compact('sliders', 'languages'));
    }

    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.partner-sliders.create', compact('languages'));
    }

    public function store(StorePartnerSliderRequest $request)
    {
        try {
            $languages = $this->languageRepository->getAllLanguages();
            
            $this->repository->createPartnerSlider(
                [
                    'status' => $request->boolean('status'),
                    'link' => $request->input('link'),
                ],
                $request->input('translations'),
                $request->file('image'),
                $this->imageService,
                $languages
            );

            return redirect()->route('admin.partner-sliders.index')
                ->with('success', 'Partner slider created successfully!');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Error creating partner slider: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(PartnerSlider $partnerSlider)
    {
        $partnerSlider->load('translates.language');
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.partner-sliders.edit', compact('partnerSlider', 'languages'));
    }

    public function update(UpdatePartnerSliderRequest $request, PartnerSlider $partnerSlider)
    {
        try {
            $languages = $this->languageRepository->getAllLanguages();
            
            $this->repository->updatePartnerSlider(
                $partnerSlider,
                [
                    'status' => $request->boolean('status'),
                    'link' => $request->input('link'),
                ],
                $request->input('translations'),
                $request->file('image'),
                $this->imageService,
                $languages
            );

            return redirect()->route('admin.partner-sliders.index')
                ->with('success', 'Partner slider updated successfully!');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Error updating partner slider: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(PartnerSlider $partnerSlider)
    {
        try {
            $this->repository->deletePartnerSlider($partnerSlider);
            return redirect()->route('admin.partner-sliders.index')
                ->with('success', 'Partner slider deleted successfully!');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Error deleting partner slider: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'sliders' => 'required|array',
            'sliders.*' => 'required|integer|exists:partner_sliders,id',
        ]);

        try {
            $this->repository->reorderPartnerSliders($request->sliders);
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
