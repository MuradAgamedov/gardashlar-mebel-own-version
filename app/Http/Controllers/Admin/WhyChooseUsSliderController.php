<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\WhyChooseUsSlider\ReorderWhyChooseUsSliderRequest;

use App\Http\Requests\Admin\WhyChooseUsSlider\StoreWhyChooseUsSliderRequest;

use App\Http\Controllers\Controller;
use App\Models\WhyChooseUsSlider;
use App\Services\ImageService;
use Illuminate\Http\Request;

use App\Interfaces\Services\Admin\WhyChooseUsSliderServiceInterface;

class WhyChooseUsSliderController extends Controller
{
    public function __construct(
        protected ImageService $imageService,
        protected WhyChooseUsSliderServiceInterface $whyChooseUsSliderService
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sliders = WhyChooseUsSlider::orderBy('order')->get();
        
        return view('admin.why-choose-us-sliders.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.why-choose-us-sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWhyChooseUsSliderRequest $request)
    {
        $request->validated();

        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $this->imageService->upload(
                    $request->file('image'),
                    'why-choose-us-sliders',
                    [
                        'max_size' => 2048,
                        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                    ]
                );
                
                if (!$imagePath) {
                    return redirect()->back()
                        ->with('error', __('Failed to upload image.'))
                        ->withInput();
                }
            }

            // Create slider
            WhyChooseUsSlider::create([
                'image' => $imagePath,
                'video_link' => trim($request->video_link ?? ''),
                'status' => $request->input('status'),
            ]);

            return redirect()->route('admin.why-choose-us-sliders.index')
                ->with('success', __('Slider created successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error creating slider: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WhyChooseUsSlider $whyChooseUsSlider)
    {
        return view('admin.why-choose-us-sliders.edit', compact('whyChooseUsSlider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WhyChooseUsSlider $whyChooseUsSlider)
    {
        $request->validated();

        try {
            // Handle image upload
            $imageData = [];
            if ($request->hasFile('image')) {
                $uploadedPath = $this->imageService->update(
                    $request->file('image'),
                    $whyChooseUsSlider->image,
                    'why-choose-us-sliders',
                    [
                        'max_size' => 2048,
                        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                    ]
                );
                
                if ($uploadedPath) {
                    $imageData['image'] = $uploadedPath;
                } else {
                    return redirect()->back()
                        ->with('error', __('Failed to upload image.'))
                        ->withInput();
                }
            }

            // Update slider
            $whyChooseUsSlider->update(array_merge($imageData, [
                'video_link' => trim($request->video_link ?? ''),
                'status' => $request->input('status'),
            ]));

            return redirect()->route('admin.why-choose-us-sliders.index')
                ->with('success', __('Slider updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating slider: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WhyChooseUsSlider $whyChooseUsSlider)
    {
        try {
            $whyChooseUsSlider->delete();
            return redirect()->route('admin.why-choose-us-sliders.index')
                ->with('success', __('Slider deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting slider: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Reorder sliders
     */
    public function reorder(ReorderWhyChooseUsSliderRequest $request)
    {
        $request->validated();

        try {
            foreach ($request->sliders as $index => $sliderId) {
                WhyChooseUsSlider::where('id', $sliderId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
