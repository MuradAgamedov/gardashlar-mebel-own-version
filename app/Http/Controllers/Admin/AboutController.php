<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\AboutTranslate;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AboutController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $abouts = About::with('translates.language')->ordered()->get();

        return view('admin.abouts.index', compact('abouts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::getActiveLanguages();
        return view('admin.abouts.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'date' => 'nullable|string|max:255',
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.description' => 'required|string',
            'translates.*.image_alt_text' => 'nullable|string|max:255',
            'status' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Upload image
            $imagePath = $this->imageService->upload($request->file('image'), 'abouts');

            // Create about
            $about = About::create([
                'image' => $imagePath,
                'date' => $request->input('date'),
                'status' => $request->input('status', true)
            ]);

            // Create translates
            foreach ($request->input('translates') as $translate) {
                AboutTranslate::create([
                    'about_id' => $about->id,
                    'language_id' => $translate['language_id'],
                    'title' => $translate['title'],
                    'description' => $translate['description'],
                    'image_alt_text' => $translate['image_alt_text'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.abouts.index')
                ->with('success', 'Haqqımızda məlumatı uğurla əlavə edildi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(About $about)
    {
        $about->load('translates');
        $languages = Language::getActiveLanguages();
        return view('admin.abouts.edit', compact('about', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, About $about)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'date' => 'nullable|string|max:255',
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.description' => 'required|string',
            'translates.*.image_alt_text' => 'nullable|string|max:255',
            'status' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($about->image) {
                    $this->imageService->delete($about->image);
                }
                // Upload new image
                $imagePath = $this->imageService->upload($request->file('image'), 'abouts');
                $about->image = $imagePath;
            }

            $about->date = $request->input('date');
            $about->status = $request->input('status', true);
            $about->save();

            // Update translates
            foreach ($request->input('translates') as $translate) {
                AboutTranslate::updateOrCreate(
                    [
                        'about_id' => $about->id,
                        'language_id' => $translate['language_id']
                    ],
                    [
                        'title' => $translate['title'],
                        'description' => $translate['description'],
                        'image_alt_text' => $translate['image_alt_text'] ?? null,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.abouts.index')
                ->with('success', 'Haqqımızda məlumatı uğurla yeniləndi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(About $about)
    {
        try {
            // Delete image
            if ($about->image) {
                $this->imageService->delete($about->image);
            }
            
            $about->delete();
            
            return redirect()->route('admin.abouts.index')
                ->with('success', 'Haqqımızda məlumatı uğurla silindi.');

        } catch (\Exception $e) {
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Reorder about items
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:abouts,id'
        ]);

        try {
            foreach ($request->input('order') as $index => $id) {
                About::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
} 