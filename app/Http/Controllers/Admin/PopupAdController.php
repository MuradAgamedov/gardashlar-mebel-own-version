<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\PopupAd;
use App\Repositories\PopupAdRepository;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\PopupAd\StorePopupAdRequest;
use App\Http\Requests\Admin\PopupAd\UpdatePopupAdRequest;

class PopupAdController extends Controller
{
    public function __construct(public PopupAdRepository $repository)
    {
    }

    public function index(Request $request)
    {
        $ads = $this->repository->getPopupAds($request);

        return view('admin.popup_ads.index', compact('ads'));
    }

    public function create()
    {
        $languages = Language::getActiveLanguages();
        return view('admin.popup_ads.create', compact('languages'));
    }

    public function store(StorePopupAdRequest $request)
    {
        try {
            $this->repository->createPopupAd(
                [
                    'image'  => $request->image,
                    'order'  => $request->order ?? 0,
                    'status' => $request->boolean('status', true),
                ],
                $request->translations
            );

            return redirect()->route('admin.popup-ads.index')
                ->with('success', 'PopupAd created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating popup ad: ' . $e->getMessage());
        }
    }

    public function edit(PopupAd $popupAd)
    {
        $popupAd->load(['translations.language']);
        $languages = Language::getActiveLanguages();

        return view('admin.popup_ads.edit', compact('popupAd', 'languages'));
    }

    public function update(Request $request, PopupAd $popupAd)
    {
        dd(1);
        try {
            $this->repository->updatePopupAd(
                $popupAd,
                [
                    'image'  => $request->image,
                    'status' => $request->boolean('status', true),
                ],
                $request->translations
            );

            return redirect()->route('admin.popup-ads.index')
                ->with('success', 'PopupAd updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating popup ad: ' . $e->getMessage());
        }
    }

    public function destroy(PopupAd $popupAd)
    {
        try {
            $this->repository->deletePopupAd($popupAd);

            return redirect()->route('admin.popup-ads.index')
                ->with('success', 'PopupAd deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting popup ad: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
        ]);

        try {
            $this->repository->reorder($request->items);

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reordering: ' . $e->getMessage()
            ], 500);
        }
    }
}
