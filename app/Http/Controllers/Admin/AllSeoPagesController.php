<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\AllSeoPagesRepository;
use Illuminate\Http\Request;

class AllSeoPagesController extends Controller
{
    protected $allSeoPagesRepository;

    public function __construct(AllSeoPagesRepository $allSeoPagesRepository)
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
    public function update(Request $request)
    {
        $request->validate([
            'is_indexed' => 'nullable|boolean',
            'is_followed' => 'nullable|boolean',
            'page_header' => 'nullable|string',
            'page_footer' => 'nullable|string',
        ]);

        try {
            $this->allSeoPagesRepository->update($request->all());

            return redirect()->route('admin.all-seo-pages.edit')
                ->with('success', 'SEO Pages settings updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.all-seo-pages.edit')
                ->with('error', 'An error occurred while updating SEO Pages settings: ' . $e->getMessage());
        }
    }
}
