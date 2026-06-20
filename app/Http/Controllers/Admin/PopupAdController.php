<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PopupAd\ReorderPopupAdRequest;

use App\Http\Controllers\Controller;
use App\Models\PopupAd;
use App\Interfaces\Services\Admin\PopupAdServiceInterface;
use App\Http\Requests\Admin\PopupAd\StorePopupAdRequest;
use App\Http\Requests\Admin\PopupAd\UpdatePopupAdRequest;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

class PopupAdController extends Controller
{
    public function __construct(protected LanguageServiceInterface $languageService, protected PopupAdServiceInterface $popupAdService)
    {
    }

    public function index(Request $request)
    {
        $ads = $this->popupAdService->getPopupAds($request);
        return view('admin.popup_ads.index', compact('ads'));
    }

    public function create()
    {
        $languages = $this->languageService->getActiveLanguages();
        return view('admin.popup_ads.create', compact('languages'));
    }

    public function store(StorePopupAdRequest $request)
    {
        try {
            $this->popupAdService->createPopupAd(
                [
                    'image'  => $request->image,
                    'status' => $request->boolean('status', true),
                ],
                $request->translations
            );

            return redirect()->route('admin.popup-ads.index')
                ->with('success', __('PopupAd created successfully.'));

        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', __('Error creating popup ad: :error', ['error' => $e->getMessage()]));
        }
    }

    public function edit(PopupAd $popupAd)
    {
        $popupAd->load(['translations.language']);
        $languages = $this->languageService->getActiveLanguages();

        return view('admin.popup_ads.edit', compact('popupAd', 'languages'));
    }

    public function update(UpdatePopupAdRequest $request, PopupAd $popupAd)
    {
        try {
            $this->popupAdService->updatePopupAd(
                $popupAd,
                [
                    'image'  => $request->image,
                    'status' => $request->boolean('status', true),
                ],
                $request->translations
            );

            return redirect()->route('admin.popup-ads.index')
                ->with('success', __('PopupAd updated successfully.'));

        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', __('Error updating popup ad: :error', ['error' => $e->getMessage()]));
        }
    }

    public function destroy(PopupAd $popupAd)
    {
        try {
            $this->popupAdService->deletePopupAd($popupAd);

            return redirect()->route('admin.popup-ads.index')
                ->with('success', __('PopupAd deleted successfully.'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error deleting popup ad: :error', ['error' => $e->getMessage()]));
        }
    }

    public function reorder(ReorderPopupAdRequest $request)
    {
        $request->validated();

        try {
            $this->popupAdService->reorder($request->items);

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
