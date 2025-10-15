<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kiv;
use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Repositories\KivRepository;
use App\Http\Requests\Admin\Kiv\StoreKivRequest;
use App\Http\Requests\Admin\Kiv\UpdateKivRequest;

class KivController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService, public KivRepository $repository)
    {
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        $kivs = $this->repository->getKivs($request);
        $categories = $this->repository->getCategories();
        return view('admin.kivs.index', compact('kivs', 'categories'));
    }

    public function create()
    {
        $categories = $this->repository->getCategories();
        return view('admin.kivs.create', compact('categories'));
    }

    public function store(StoreKivRequest $request)
    {
        try {
            $this->repository->createKiv([
                'video_link' => $request->input('video_link'),
                'kiv_category_id' => $request->input('kiv_category_id'),
                'status' => $request->input('status'),
            ], $request->file('image'), $this->imageService);

            return redirect()->route('admin.kivs.index')
                ->with('success', 'KIV created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Kiv $kiv)
    {
        $kiv->load('category.translates');
        $categories = $this->repository->getCategories();
        return view('admin.kivs.edit', compact('kiv', 'categories'));
    }

    public function update(UpdateKivRequest $request, Kiv $kiv)
    {
        try {
            $this->repository->updateKiv($kiv, [
                'video_link' => $request->input('video_link'),
                'kiv_category_id' => $request->input('kiv_category_id'),
                'status' => $request->input('status'),
            ], $request->file('image'), $this->imageService);

            return redirect()->route('admin.kivs.index')
                ->with('success', 'KIV updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Kiv $kiv)
    {
        try {
            $this->repository->deleteKiv($kiv);
            return redirect()->route('admin.kivs.index')
                ->with('success', 'KIV deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting KIV: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'kivs' => 'required|array',
            'kivs.*' => 'required|integer|exists:kivs,id',
        ]);

        try {
            $this->repository->reorderKivs($request->kivs);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 