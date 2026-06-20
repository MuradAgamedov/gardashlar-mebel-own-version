<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PartnerSlider\ReorderPartnerSliderRequest;

use App\Http\Controllers\Controller;
use App\Models\PartnerSlider;
use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\PartnerSliderServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Http\Requests\Admin\PartnerSlider\StorePartnerSliderRequest;
use App\Http\Requests\Admin\PartnerSlider\UpdatePartnerSliderRequest;

class PartnerSliderController extends Controller
{
    public function __construct(
        protected ImageService $imageService,
        protected PartnerSliderServiceInterface $partnerSliderService,
        protected LanguageServiceInterface $languageService
    )
    {
    }

    public function index()
    {
        $sliders = $this->partnerSliderService->getPartnerSliders();
        $languages = $this->languageService->getAllLanguages();
        return view('admin.partner-sliders.index', compact('sliders', 'languages'));
    }

    public function create()
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.partner-sliders.create', compact('languages'));
    }

    public function store(StorePartnerSliderRequest $request)
    {
        try {
            $languages = $this->languageService->getAllLanguages();
            
            $this->partnerSliderService->createPartnerSlider(
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
                ->with('success', __('Partner slider created successfully!'));

        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', __('Error creating partner slider: ') . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(PartnerSlider $partnerSlider)
    {
        $partnerSlider->load('translates.language');
        $languages = $this->languageService->getAllLanguages();
        return view('admin.partner-sliders.edit', compact('partnerSlider', 'languages'));
    }

    public function update(UpdatePartnerSliderRequest $request, PartnerSlider $partnerSlider)
    {
        try {
            $languages = $this->languageService->getAllLanguages();
            
            $this->partnerSliderService->updatePartnerSlider(
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
                ->with('success', __('Partner slider updated successfully!'));

        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', __('Error updating partner slider: ') . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(PartnerSlider $partnerSlider)
    {
        try {
            $this->partnerSliderService->deletePartnerSlider($partnerSlider);
            return redirect()->route('admin.partner-sliders.index')
                ->with('success', __('Partner slider deleted successfully!'));
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', __('Error deleting partner slider: ') . $e->getMessage());
        }
    }

    public function reorder(ReorderPartnerSliderRequest $request)
    {
        $request->validated();

        try {
            $this->partnerSliderService->reorderPartnerSliders($request->sliders);
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
