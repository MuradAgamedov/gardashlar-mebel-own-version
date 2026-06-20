<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SimpleImage\QuickUploadStoreSimpleImageRequest;

use App\Http\Requests\Admin\SimpleImage\UpdateSimpleImageRequest;

use App\Http\Requests\Admin\SimpleImage\StoreSimpleImageRequest;

use App\Http\Controllers\Controller;
use App\Models\SimpleImage;
use App\Models\SimpleImageTranslate;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Services\ImageService;
use App\Interfaces\Services\Admin\SimpleImageServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

class SimpleImageController extends Controller
{
    public function __construct(
        protected ImageService $imageService,
        protected SimpleImageServiceInterface $simpleImageService,
        protected LanguageServiceInterface $languageService
    )
    {
}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $images = $this->simpleImageService->getImages();
        return view('admin.simple-images.index', compact('images'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.simple-images.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSimpleImageRequest $request): RedirectResponse
    {
        $request->validated();

        try {
            // Additional file validation
            $file = $request->file('image');
            
            if (!$file->isValid()) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                    UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
                    UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
                ];
                
                $errorMessage = $errorMessages[$file->getError()] ?? __('Unknown upload error occurred.');
                return back()->withInput()->with('error', __('File upload failed: :error', ['error' => $errorMessage]));
            }

            // Check file type manually
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return back()->withInput()->with('error', __('Invalid file type. Please upload a JPG, PNG, GIF, or WebP image.'));
            }

            try {
                $imagePath = $this->imageService->upload($file, 'simple-images');
                
                if (!$imagePath) {
                    return back()->withInput()
                        ->with('error', __('Failed to upload image. Please check server storage permissions and try again.'));
                }
            } catch (\Exception $uploadException) {
                // Log the specific upload error
                \Log::error('Image upload error in store: ' . $uploadException->getMessage(), [
                    'file' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'error' => $uploadException->getMessage()
                ]);
                
                return back()->withInput()
                    ->with('error', __('Image upload failed: :error', ['error' => $uploadException->getMessage()]));
            }

            $simpleImage = SimpleImage::create([
                'keyword' => $request->keyword,
                'image_path' => $imagePath,
            ]);

            // Create translations
            foreach ($request->translations as $languageId => $translation) {
                SimpleImageTranslate::create([
                    'simple_image_id' => $simpleImage->id,
                    'language_id' => $languageId,
                    'image_go_url' => $translation['image_go_url'] ?? null,
                    'image_alt_text' => $translation['image_alt_text'] ?? null,
                ]);
            }

            return redirect()->route('admin.simple-images.index')
                ->with('success', __('Image created successfully.'));

        } catch (\Exception $e) {
            \Log::error('SimpleImage store error: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', __('Error creating image: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SimpleImage $simpleImage): View
    {
        $languages = $this->languageService->getActiveLanguages();
        $translations = $simpleImage->translates->keyBy('language_id');
        return view('admin.simple-images.edit', compact('simpleImage', 'languages', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSimpleImageRequest $request, SimpleImage $simpleImage): RedirectResponse
    {
        $request->validated();

        try {
            $data = [
                'keyword' => $request->keyword,
            ];

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                
                // Additional file validation
                if (!$file->isValid()) {
                    $errorMessages = [
                        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
                        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
                    ];
                    
                    $errorMessage = $errorMessages[$file->getError()] ?? __('Unknown upload error occurred.');
                    return back()->withInput()->with('error', __('File upload failed: :error', ['error' => $errorMessage]));
                }

                // Check file type manually
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($file->getMimeType(), $allowedTypes)) {
                    return back()->withInput()->with('error', __('Invalid file type. Please upload a JPG, PNG, GIF, or WebP image.'));
                }

                // Delete old image
                if ($simpleImage->image_path) {
                    $this->imageService->delete($simpleImage->image_path);
                }
                
                // Upload new image
                try {
                    $imagePath = $this->imageService->upload($file, 'simple-images');
                    
                    if (!$imagePath) {
                        return back()->withInput()
                            ->with('error', __('Failed to upload image. Please check server storage permissions and try again.'));
                    }
                } catch (\Exception $uploadException) {
                    // Log the specific upload error
                    \Log::error('Image upload error in update: ' . $uploadException->getMessage(), [
                        'file' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'error' => $uploadException->getMessage()
                    ]);
                    
                    return back()->withInput()
                        ->with('error', __('Image upload failed: :error', ['error' => $uploadException->getMessage()]));
                }
                
                $data['image_path'] = $imagePath;
            }

            $simpleImage->update($data);

            // Update or create translations
            foreach ($request->translations as $languageId => $translation) {
                SimpleImageTranslate::updateOrCreate(
                    [
                        'simple_image_id' => $simpleImage->id,
                        'language_id' => $languageId,
                    ],
                    [
                        'image_go_url' => $translation['image_go_url'] ?? null,
                        'image_alt_text' => $translation['image_alt_text'] ?? null,
                    ]
                );
            }

            return redirect()->route('admin.simple-images.index')
                ->with('success', __('Image updated successfully.'));

        } catch (\Exception $e) {
            \Log::error('SimpleImage update error: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', __('Error updating image: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Quick upload image by keyword
     */
    public function quickUpload(Request $request, string $keyword): View
    {
        $simpleImage = SimpleImage::where('keyword', $keyword)->first();
        $languages = $this->languageService->getActiveLanguages();
        
        if ($simpleImage) {
            $translations = $simpleImage->translates->keyBy('language_id');
        } else {
            $translations = collect();
        }
        
        return view('admin.simple-images.quick-upload', compact('keyword', 'simpleImage', 'languages', 'translations'));
    }

    /**
     * Store quick uploaded image by keyword
     */
    public function quickUploadStore(QuickUploadStoreSimpleImageRequest $request, string $keyword): RedirectResponse
    {
        $request->validated();

        try {
            $file = $request->file('image');
            
            if (!$file->isValid()) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                    UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
                    UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
                ];
                
                $errorMessage = $errorMessages[$file->getError()] ?? __('Unknown upload error occurred.');
                return back()->withInput()->with('error', __('File upload failed: :error', ['error' => $errorMessage]));
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return back()->withInput()->with('error', __('Invalid file type. Please upload a JPG, PNG, GIF, or WebP image.'));
            }

            try {
                $imagePath = $this->imageService->upload($file, 'simple-images');
                
                if (!$imagePath) {
                    return back()->withInput()
                        ->with('error', __('Failed to upload image. Please check server storage permissions and try again.'));
                }
            } catch (\Exception $uploadException) {
                \Log::error('Image upload error in quick upload: ' . $uploadException->getMessage(), [
                    'keyword' => $keyword,
                    'file' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'error' => $uploadException->getMessage()
                ]);
                
                return back()->withInput()
                    ->with('error', __('Image upload failed: :error', ['error' => $uploadException->getMessage()]));
            }

            // Find existing or create new
            $simpleImage = SimpleImage::where('keyword', $keyword)->first();
            
            if ($simpleImage) {
                // Delete old image
                if ($simpleImage->image_path) {
                    $this->imageService->delete($simpleImage->image_path);
                }
                
                $simpleImage->update(['image_path' => $imagePath]);
                
                // Delete old translations
                $simpleImage->translates()->delete();
            } else {
                $simpleImage = SimpleImage::create([
                    'keyword' => $keyword,
                    'image_path' => $imagePath,
                ]);
            }

            // Create translations
            foreach ($request->translations as $languageId => $translation) {
                SimpleImageTranslate::create([
                    'simple_image_id' => $simpleImage->id,
                    'language_id' => $languageId,
                    'image_go_url' => $translation['image_go_url'] ?? null,
                    'image_alt_text' => $translation['image_alt_text'] ?? null,
                ]);
            }

            return redirect()->route('admin.simple-images.quick-upload', $keyword)
                ->with('success', __('Image uploaded successfully for keyword: :keyword', ['keyword' => $keyword]));

        } catch (\Exception $e) {
            \Log::error('SimpleImage quick upload error: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', __('Error uploading image: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SimpleImage $simpleImage): RedirectResponse
    {
        try {
            // Delete image file
            if ($simpleImage->image_path) {
                $this->imageService->delete($simpleImage->image_path);
            }
            
            $simpleImage->delete();
            
            return redirect()->route('admin.simple-images.index')
                ->with('success', __('Image deleted successfully.'));
        } catch (\Exception $e) {
            \Log::error('SimpleImage destroy error: ' . $e->getMessage());
            return back()->with('error', __('Error deleting image: :error', ['error' => $e->getMessage()]));
        }
    }


}
