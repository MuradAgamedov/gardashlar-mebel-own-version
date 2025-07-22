<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyTranslate;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $companies = Company::with('translates.language')->ordered()->get();
        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        $languages = Language::getActiveLanguages();
        return view('admin.companies.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.card_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.card_image_alt_text' => 'nullable|string|max:255',
            'translates.*.inner_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.inner_image_alt_text' => 'nullable|string|max:255',
            'translates.*.description' => 'required|string',
            'status' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $company = Company::create([
                'status' => $request->input('status', true)
            ]);

            foreach ($request->input('translates') as $translate) {
                $cardImagePath = $this->imageService->upload($request->file("translates.{$translate['language_id']}.card_image"), 'companies/card');
                $innerImagePath = $this->imageService->upload($request->file("translates.{$translate['language_id']}.inner_image"), 'companies/inner');

                CompanyTranslate::create([
                    'company_id' => $company->id,
                    'language_id' => $translate['language_id'],
                    'title' => $translate['title'],
                    'card_image' => $cardImagePath,
                    'card_image_alt_text' => $translate['card_image_alt_text'] ?? null,
                    'inner_image' => $innerImagePath,
                    'inner_image_alt_text' => $translate['inner_image_alt_text'] ?? null,
                    'description' => $translate['description'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.companies.index')
                ->with('success', 'Kompaniya uğurla əlavə edildi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function edit(Company $company)
    {
        $company->load('translates');
        $languages = Language::getActiveLanguages();
        return view('admin.companies.edit', compact('company', 'languages'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.card_image_alt_text' => 'nullable|string|max:255',
            'translates.*.inner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.inner_image_alt_text' => 'nullable|string|max:255',
            'translates.*.description' => 'required|string',
            'status' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $company->status = $request->input('status', true);
            $company->save();

            foreach ($request->input('translates') as $translate) {
                $translateData = [
                    'title' => $translate['title'],
                    'card_image_alt_text' => $translate['card_image_alt_text'] ?? null,
                    'inner_image_alt_text' => $translate['inner_image_alt_text'] ?? null,
                    'description' => $translate['description'],
                ];

                // Handle card image
                if ($request->hasFile("translates.{$translate['language_id']}.card_image")) {
                    $existingTranslate = $company->translates->where('language_id', $translate['language_id'])->first();
                    if ($existingTranslate && $existingTranslate->card_image) {
                        $this->imageService->delete($existingTranslate->card_image);
                    }
                    $translateData['card_image'] = $this->imageService->upload($request->file("translates.{$translate['language_id']}.card_image"), 'companies/card');
                }

                // Handle inner image
                if ($request->hasFile("translates.{$translate['language_id']}.inner_image")) {
                    $existingTranslate = $company->translates->where('language_id', $translate['language_id'])->first();
                    if ($existingTranslate && $existingTranslate->inner_image) {
                        $this->imageService->delete($existingTranslate->inner_image);
                    }
                    $translateData['inner_image'] = $this->imageService->upload($request->file("translates.{$translate['language_id']}.inner_image"), 'companies/inner');
                }

                CompanyTranslate::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'language_id' => $translate['language_id']
                    ],
                    $translateData
                );
            }

            DB::commit();

            return redirect()->route('admin.companies.index')
                ->with('success', 'Kompaniya uğurla yeniləndi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function destroy(Company $company)
    {
        try {
            // Delete all images
            foreach ($company->translates as $translate) {
                if ($translate->card_image) {
                    $this->imageService->delete($translate->card_image);
                }
                if ($translate->inner_image) {
                    $this->imageService->delete($translate->inner_image);
                }
            }

            $company->delete();

            return redirect()->route('admin.companies.index')
                ->with('success', 'Kompaniya uğurla silindi.');

        } catch (\Exception $e) {
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:companies,id'
        ]);

        try {
            foreach ($request->input('order') as $index => $id) {
                Company::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
