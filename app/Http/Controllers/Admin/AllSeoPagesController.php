<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AllSeoPages\UpdateAllSeoPagesRequest;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\AllSeoPagesServiceInterface;
use Illuminate\Http\Request;

class AllSeoPagesController extends Controller
{
    protected $allSeoPagesRepository;

    public function __construct(protected AllSeoPagesServiceInterface $allSeoPagesService)
    {
        $this->allSeoPagesRepository = $allSeoPagesRepository;
    }

    /**
     * Show the form for editing the SEO pages settings.
     */
    public function edit()
    {
        $seoPages = $this->allSeoPagesRepository->get();
        
        return view('admin.all-seo-pages.edit', compact('seoPages'));
    }

    /**
     * Update the SEO pages settings in storage.
     */
    public function update(UpdateAllSeoPagesRequest $request)
    {
        $request->validated();

        try {
            $this->allSeoPagesRepository->update($request->all());

            return redirect()->route('admin.all-seo-pages.edit')
                ->with('success', __('SEO Pages settings updated successfully!'));
        } catch (\Exception $e) {
            return redirect()->route('admin.all-seo-pages.edit')
                ->with('error', __('An error occurred while updating SEO Pages settings: :error', ['error' => $e->getMessage()]));
        }
    }
}
