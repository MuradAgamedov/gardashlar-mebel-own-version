<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AllSeoPages\UpdateAllSeoPagesRequest;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\AllSeoPagesServiceInterface;
use Illuminate\Http\Request;

class AllSeoPagesController extends Controller
{
    public function __construct(protected AllSeoPagesServiceInterface $allSeoPagesService)
    {
    }

    /**
     * Show the form for editing the SEO pages settings.
     */
    public function edit()
    {
        $seoPages = $this->allSeoPagesService->first();
        if (!$seoPages) {
            $seoPages = $this->allSeoPagesService->create([
                'is_indexed' => true,
                'is_followed' => true,
                'rolette_actions' => false,
                'popup_ads_show' => false,
                'page_header' => null,
                'page_footer' => null,
            ]);
        }
        
        return view('admin.all-seo-pages.edit', compact('seoPages'));
    }

    /**
     * Update the SEO pages settings in storage.
     */
    public function update(UpdateAllSeoPagesRequest $request)
    {
        $validated = $request->validated();

        try {
            $seoPages = $this->allSeoPagesService->first();

            $data = [
                'is_indexed'      => (bool) ($validated['is_indexed'] ?? false),
                'is_followed'     => (bool) ($validated['is_followed'] ?? false),
                'rolette_actions' => (bool) ($validated['rolette_actions'] ?? false),
                'popup_ads_show'  => (bool) ($validated['popup_ads_show'] ?? false),
                'page_header'     => $validated['page_header'] ?? null,
                'page_footer'     => $validated['page_footer'] ?? null,
            ];

            if (!$seoPages) {
                $this->allSeoPagesService->create($data);
            } else {
                $this->allSeoPagesService->update($seoPages->id, $data);
            }

            return redirect()->route('admin.all-seo-pages.edit')
                ->with('success', __('SEO Pages settings updated successfully!'));
        } catch (\Exception $e) {
            return redirect()->route('admin.all-seo-pages.edit')
                ->with('error', __('An error occurred while updating SEO Pages settings: :error', ['error' => $e->getMessage()]));
        }
    }
}
