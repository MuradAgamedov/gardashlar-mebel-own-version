<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Testimonial\ReorderTestimonialRequest;

use App\Http\Requests\Admin\Testimonial\StoreTestimonialRequest;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Services\ImageService;
use App\Interfaces\Services\Admin\TestimonialServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

class TestimonialController extends Controller
{
    public function __construct(
        protected ImageService $imageService,
        protected TestimonialServiceInterface $testimonialService,
        protected LanguageServiceInterface $languageService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $testimonials = $this->testimonialService->allOrdered(['translates.language']);
        $languages = $this->languageService->getAllLanguages();
        
        return view('admin.testimonials.index', compact('testimonials', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.testimonials.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTestimonialRequest $request)
    {
        $languages = $this->languageService->getAllLanguages();

        try {
            // Handle customer image upload
            $customerImagePath = null;
            if ($request->hasFile('customer_image')) {
                $customerImagePath = $this->imageService->upload(
                    $request->file('customer_image'),
                    'testimonials/customers',
                    [
                        'max_size' => 2048,
                        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                    ]
                );
                
                if (!$customerImagePath) {
                    return redirect()->back()
                        ->with('error', __('Failed to upload customer image.'))
                        ->withInput();
                }
            }

            // Handle company logo upload
            $companyLogoPath = null;
            if ($request->hasFile('company_logo')) {
                $companyLogoPath = $this->imageService->upload(
                    $request->file('company_logo'),
                    'testimonials/companies',
                    [
                        'max_size' => 2048,
                        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                    ]
                );
                
                if (!$companyLogoPath) {
                    return redirect()->back()
                        ->with('error', __('Failed to upload company logo.'))
                        ->withInput();
                }
            }

            // Create testimonial
            $testimonial = Testimonial::create([
                'customer_image' => $customerImagePath,
                'company_logo' => $companyLogoPath,
                'status' => $request->input('status'),
            ]);

            // Create translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $testimonial->translates()->create([
                    'language_id' => $language->id,
                    'full_name' => trim($translationData['full_name'] ?? ''),
                    'position' => trim($translationData['position'] ?? ''),
                    'comment' => trim($translationData['comment'] ?? ''),
                ]);
            }

            return redirect()->route('admin.testimonials.index')
                ->with('success', __('Testimonial created successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error creating testimonial: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Testimonial $testimonial)
    {
        $testimonial->load('translates.language');
        $languages = $this->languageService->getAllLanguages();
        
        return view('admin.testimonials.edit', compact('testimonial', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTestimonialRequest $request, Testimonial $testimonial)
    {
        $languages = $this->languageService->getAllLanguages();

        try {
            // Handle customer image upload
            $imageData = [];
            if ($request->hasFile('customer_image')) {
                $uploadedPath = $this->imageService->update(
                    $request->file('customer_image'),
                    $testimonial->customer_image,
                    'testimonials/customers',
                    [
                        'max_size' => 2048,
                        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                    ]
                );
                
                if ($uploadedPath) {
                    $imageData['customer_image'] = $uploadedPath;
                } else {
                    return redirect()->back()
                        ->with('error', __('Failed to upload customer image.'))
                        ->withInput();
                }
            }

            // Handle company logo upload
            if ($request->hasFile('company_logo')) {
                $uploadedPath = $this->imageService->update(
                    $request->file('company_logo'),
                    $testimonial->company_logo,
                    'testimonials/companies',
                    [
                        'max_size' => 2048,
                        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                    ]
                );
                
                if ($uploadedPath) {
                    $imageData['company_logo'] = $uploadedPath;
                } else {
                    return redirect()->back()
                        ->with('error', __('Failed to upload company logo.'))
                        ->withInput();
                }
            }

            // Update testimonial
            $testimonial->update(array_merge($imageData, [
                'status' => $request->input('status'),
            ]));

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $testimonial->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'full_name' => trim($translationData['full_name'] ?? ''),
                        'position' => trim($translationData['position'] ?? ''),
                        'comment' => trim($translationData['comment'] ?? ''),
                    ]
                );
            }

            return redirect()->route('admin.testimonials.index')
                ->with('success', __('Testimonial updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating testimonial: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimonial $testimonial)
    {
        try {
            if ($testimonial->customer_image) {
                $this->imageService->delete($testimonial->customer_image);
            }
            if ($testimonial->company_logo) {
                $this->imageService->delete($testimonial->company_logo);
            }
            $testimonial->delete();
            return redirect()->route('admin.testimonials.index')
                ->with('success', __('Testimonial deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting testimonial: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Reorder testimonials
     */
    public function reorder(ReorderTestimonialRequest $request)
    {
        $request->validated();

        try {
            foreach ($request->testimonials as $index => $testimonialId) {
                Testimonial::where('id', $testimonialId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
