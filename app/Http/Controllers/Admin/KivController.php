<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Kiv\ReorderKivRequest;

use App\Http\Controllers\Controller;
use App\Models\Kiv;
use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\KivServiceInterface;
use App\Http\Requests\Admin\Kiv\StoreKivRequest;
use App\Http\Requests\Admin\Kiv\UpdateKivRequest;

class KivController extends Controller
{
    public function __construct(protected ImageService $imageService, protected KivServiceInterface $kivService)
    {
}

    public function index(Request $request)
    {
        $kivs = $this->kivService->getKivs($request);
        $categories = $this->kivService->getCategories();
        return view('admin.kivs.index', compact('kivs', 'categories'));
    }

    public function create()
    {
        $categories = $this->kivService->getCategories();
        return view('admin.kivs.create', compact('categories'));
    }

    public function store(StoreKivRequest $request)
    {
        try {
            $this->kivService->createKiv([
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
        $categories = $this->kivService->getCategories();
        return view('admin.kivs.edit', compact('kiv', 'categories'));
    }

    public function update(UpdateKivRequest $request, Kiv $kiv)
    {
        try {
            $this->kivService->updateKiv($kiv, [
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
            $this->kivService->deleteKiv($kiv);
            return redirect()->route('admin.kivs.index')
                ->with('success', 'KIV deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting KIV: ' . $e->getMessage());
        }
    }

    public function reorder(ReorderKivRequest $request)
    {
        $request->validated();

        try {
            $this->kivService->reorderKivs($request->kivs);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 