<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\PageRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\Page\StorePageRequest;
use App\Http\Requests\Admin\Page\UpdatePageRequest;
use App\Services\ImageService;

class PageController extends Controller
{
    public function __construct(
        public PageRepository $repository,
        public LanguageRepository $languageRepository,
        public ImageService $imageService
    )
    {
    }

    public function index()
    {
        $pages = $this->repository->getPages();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.pages.create', compact('languages'));
    }

    public function store(StorePageRequest $request)
    {
        try {
            $data = [
                'status' => $request->has('status') ? true : false,
                'keyword' => trim($request->input('keyword', ''))
            ];

            // Handle OpenGraph image upload
            if ($request->hasFile('opengraph_image')) {
                $imagePath = $this->imageService->upload($request->file('opengraph_image'), 'pages/opengraph');
                $data['opengraph_image'] = $imagePath;
                
                // Get image dimensions
                $imageInfo = getimagesize($request->file('opengraph_image')->getPathname());
                if ($imageInfo) {
                    $data['opengraph_image_width'] = $imageInfo[0];
                    $data['opengraph_image_height'] = $imageInfo[1];
                }
            }

            $this->repository->createPage($data, $request->translates);

            return redirect()->route('admin.pages.index')
                ->with('success', __('Page created successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error creating page: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $page = $this->repository->findPage($id);
        return view('admin.pages.show', compact('page'));
    }

    public function edit(string $id)
    {
        $page = $this->repository->findPage($id);
        $languages = $this->languageRepository->getActiveLanguages();
        return view('admin.pages.edit', compact('page', 'languages'));
    }

    public function update(UpdatePageRequest $request, string $id)
    {
        try {
            $page = $this->repository->findPage($id);
            
            $data = [
                'status' => $request->has('status') ? true : false,
                'keyword' => trim($request->input('keyword', ''))
            ];

            // Handle OpenGraph image upload
            if ($request->hasFile('opengraph_image')) {
                // Delete old image if exists
                if ($page->opengraph_image) {
                    $this->imageService->delete($page->opengraph_image);
                }
                
                $imagePath = $this->imageService->upload($request->file('opengraph_image'), 'pages/opengraph');
                $data['opengraph_image'] = $imagePath;
                
                // Get image dimensions
                $imageInfo = getimagesize($request->file('opengraph_image')->getPathname());
                if ($imageInfo) {
                    $data['opengraph_image_width'] = $imageInfo[0];
                    $data['opengraph_image_height'] = $imageInfo[1];
                }
            }

            $this->repository->updatePage($id, $data, $request->translates);

            return redirect()->route('admin.pages.index')
                ->with('success', __('Page updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating page: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->repository->deletePage($id);
            return redirect()->route('admin.pages.index')
                ->with('success', __('Page deleted successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting page: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Delete OpenGraph image
     */
    public function deleteOpenGraphImage(string $id)
    {
        try {
            $page = $this->repository->findPage($id);
            
            if ($page->opengraph_image) {
                // Delete the image file
                $this->imageService->delete($page->opengraph_image);
                
                // Update database
                $page->update([
                    'opengraph_image' => null,
                    'opengraph_image_width' => null,
                    'opengraph_image_height' => null
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'OpenGraph image deleted successfully!'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No OpenGraph image found to delete.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error deleting OpenGraph image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateSlug(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'language_id' => 'required|exists:languages,id',
                'page_id' => 'nullable|exists:pages,id',
            ]);

            return response()->json(['success' => true, 'slug' => '']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating slug: ' . $e->getMessage()
            ], 500);
        }
    }
}
