<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Repositories\ImageRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\Image\StoreImageRequest;
use App\Http\Requests\Admin\Image\UpdateImageRequest;

class ImageController extends Controller
{
    public function __construct(
        public ImageRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index()
    {
        $images = $this->repository->getImages();
        return view('admin.images.index', compact('images'));
    }

    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.images.create', compact('languages'));
    }

    public function store(StoreImageRequest $request)
    {
        try {
            $this->repository->createImage(
                ['keyword' => $request->input('keyword')],
                $request->translations,
                $request->file('image')
            );

            return redirect()->route('admin.images.index')
                ->with('success', 'Image created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating image. Please try again.');
        }
    }

    public function edit(Image $image)
    {
        $languages = $this->languageRepository->getAllLanguages();
        $translations = $image->translates->keyBy('language_id');
        return view('admin.images.edit', compact('image', 'languages', 'translations'));
    }

    public function update(UpdateImageRequest $request, Image $image)
    {
        try {
            $this->repository->updateImage(
                $image,
                ['keyword' => $request->input('keyword')],
                $request->translations,
                $request->file('image')
            );

            return redirect()->route('admin.images.index')
                ->with('success', 'Image updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating image. Please try again.');
        }
    }

    public function destroy(Image $image)
    {
        try {
            $this->repository->deleteImage($image);
            return redirect()->route('admin.images.index')
                ->with('success', 'Image deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting image. Please try again.');
        }
    }
}
