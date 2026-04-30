<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Testimonial;
use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Repositories\TestimonialRepository;
use App\Repositories\LanguageRepository;

class TestimonialController extends Controller
{
    protected $imageService;

    public function __construct(
        ImageService $imageService,
        public TestimonialRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $testimonials = $this->repository->getTestimonials();
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.testimonials.index', compact('testimonials', 'languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.testimonials.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $languages = $this->languageRepository->getAllLanguages();
        
        // Base validation
        $request->validate([
            'customer_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'status' => 'boolean',
        ], [
            'customer_image.image' => 'Customer image must be an image file.',
            'customer_image.mimes' => 'Customer image must be a file of type: jpg, jpeg, png, gif, webp.',
            'customer_image.max' => 'Customer image may not be greater than 2MB.',
            'company_logo.image' => 'Company logo must be an image file.',
            'company_logo.mimes' => 'Company logo must be a file of type: jpg, jpeg, png, gif, webp.',
            'company_logo.max' => 'Company logo may not be greater than 2MB.',
            'status.boolean' => 'Status must be true or false.',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.full_name" => 'required|string|max:255',
                "translations.{$language->id}.position" => 'required|string|max:255',
                "translations.{$language->id}.comment" => 'required|string|max:1000',
            ], [
                "translations.{$language->id}.full_name.required" => "Full name is required for {$language->name}.",
                "translations.{$language->id}.full_name.max" => "Full name may not be greater than 255 characters for {$language->name}.",
                "translations.{$language->id}.position.required" => "Position is required for {$language->name}.",
                "translations.{$language->id}.position.max" => "Position may not be greater than 255 characters for {$language->name}.",
                "translations.{$language->id}.comment.required" => "Comment is required for {$language->name}.",
                "translations.{$language->id}.comment.max" => "Comment may not be greater than 1000 characters for {$language->name}.",
            ]);
        }

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
                        ->with('error', 'Failed to upload customer image.')
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
                        ->with('error', 'Failed to upload company logo.')
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
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.testimonials.edit', compact('testimonial', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $languages = $this->languageRepository->getAllLanguages();
        
        // Base validation
        $request->validate([
            'customer_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'status' => 'boolean',
        ], [
            'customer_image.image' => 'Customer image must be an image file.',
            'customer_image.mimes' => 'Customer image must be a file of type: jpg, jpeg, png, gif, webp.',
            'customer_image.max' => 'Customer image may not be greater than 2MB.',
            'company_logo.image' => 'Company logo must be an image file.',
            'company_logo.mimes' => 'Company logo must be a file of type: jpg, jpeg, png, gif, webp.',
            'company_logo.max' => 'Company logo may not be greater than 2MB.',
            'status.boolean' => 'Status must be true or false.',
        ]);

        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.full_name" => 'required|string|max:255',
                "translations.{$language->id}.position" => 'required|string|max:255',
                "translations.{$language->id}.comment" => 'required|string|max:1000',
            ], [
                "translations.{$language->id}.full_name.required" => "Full name is required for {$language->name}.",
                "translations.{$language->id}.full_name.max" => "Full name may not be greater than 255 characters for {$language->name}.",
                "translations.{$language->id}.position.required" => "Position is required for {$language->name}.",
                "translations.{$language->id}.position.max" => "Position may not be greater than 255 characters for {$language->name}.",
                "translations.{$language->id}.comment.required" => "Comment is required for {$language->name}.",
                "translations.{$language->id}.comment.max" => "Comment may not be greater than 1000 characters for {$language->name}.",
            ]);
        }

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
                        ->with('error', 'Failed to upload customer image.')
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
                        ->with('error', 'Failed to upload company logo.')
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
    public function reorder(Request $request)
    {
        $request->validate([
            'testimonials' => 'required|array',
            'testimonials.*' => 'required|integer|exists:testimonials,id',
        ]);

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
