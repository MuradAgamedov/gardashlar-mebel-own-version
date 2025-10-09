<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\RefundPolicyRepository;
use Illuminate\Http\Request;

class RefundPolicyController extends Controller
{
    protected $repository;

    public function __construct(RefundPolicyRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display the specified resource.
     */
    public function edit()
    {
        $refundPolicy = $this->repository->getRefundPolicyWithTranslations();
        $languages = $this->repository->getActiveLanguages();
        
        return view('admin.refund-policy.edit', compact('refundPolicy', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $languages = $this->repository->getActiveLanguages();
        
        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.content" => 'required|string|max:5000',
            ], [
                "translations.{$language->id}.content.required" => "Content is required for {$language->lang_code}.",
                "translations.{$language->id}.content.max" => "Content may not be greater than 5000 characters for {$language->lang_code}.",
            ]);
        }

        try {
            $this->repository->updateRefundPolicy($request->input('translations', []));
            
            return redirect()->back()
                ->with('success', 'Refund Policy updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating refund policy: ' . $e->getMessage())
                ->withInput();
        }
    }
}
