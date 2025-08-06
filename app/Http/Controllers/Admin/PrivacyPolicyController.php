<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use App\Models\PrivacyPolicyTranslate;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PrivacyPolicyController extends Controller
{
    /**
     * Show the form for editing the privacy policy.
     */
    public function edit()
    {
        $privacyPolicy = PrivacyPolicy::with('translates')->first();
        
        if (!$privacyPolicy) {
            $privacyPolicy = PrivacyPolicy::create();
        }
        
        $languages = Language::getActiveLanguages();
        
        return view('admin.privacy-policy.edit', compact('privacyPolicy', 'languages'));
    }

    /**
     * Update the privacy policy.
     */
    public function update(Request $request)
    {
        // Debug: Check what we're receiving
        \Log::info('Privacy Policy Update Request:', $request->all());
        \Log::info('Request Method:', ['method' => $request->method()]);
        \Log::info('Request URL:', ['url' => $request->url()]);
        \Log::info('CSRF Token:', ['token' => $request->input('_token')]);
        
        $request->validate([
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.content' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $privacyPolicy = PrivacyPolicy::first();
            
            if (!$privacyPolicy) {
                $privacyPolicy = PrivacyPolicy::create();
            }

            \Log::info('Privacy Policy ID:', ['id' => $privacyPolicy->id]);

            // Update translates
            foreach ($request->input('translates') as $translate) {
                \Log::info('Processing translate:', $translate);
                
                $result = PrivacyPolicyTranslate::updateOrCreate(
                    [
                        'privacy_policy_id' => $privacyPolicy->id,
                        'language_id' => $translate['language_id']
                    ],
                    [
                        'content' => $translate['content'],
                    ]
                );
                
                \Log::info('Translate result:', ['result' => $result->toArray()]);
            }

            DB::commit();
            \Log::info('Privacy Policy updated successfully');

            // Clear cache for all languages
            $languages = Language::getActiveLanguages();
            foreach ($languages as $language) {
                Cache::forget("privacy_policy_{$language->id}");
            }

            return redirect()->route('admin.privacy-policy.edit')
                ->with('success', 'Gizlilik siyasəti uğurla yeniləndi.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Privacy Policy update error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }
} 