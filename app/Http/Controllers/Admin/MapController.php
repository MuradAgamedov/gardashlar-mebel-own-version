<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Map\ReorderMapRequest;

use App\Http\Requests\Admin\Map\StoreMapRequest;

use App\Http\Controllers\Controller;
use App\Models\Map;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Interfaces\Services\Admin\MapServiceInterface;

class MapController extends Controller
{
    public function __construct(protected MapServiceInterface $mapService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $maps = $this->mapService->getMaps();
        return view('admin.maps.index', compact('maps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.maps.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMapRequest $request)
    {
        $request->validated();

        try {
            Map::create([
                'map' => trim($request->map),
                'status' => $request->input('status', false),
            ]);

            return redirect()->route('admin.maps.index')
                ->with('success', __('Map created successfully.'));

        } catch (\Exception $e) {
            Log::error('Error creating map: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', __('Error creating map. Please try again.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Map $map)
    {
        return view('admin.maps.edit', compact('map'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Map $map)
    {
        $request->validated();

        try {
            $map->update([
                'map' => trim($request->map),
                'status' => $request->input('status', false),
            ]);

            return redirect()->route('admin.maps.index')
                ->with('success', __('Map updated successfully.'));

        } catch (\Exception $e) {
            Log::error('Error updating map: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', __('Error updating map. Please try again.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Map $map)
    {
        try {
            $map->delete();
            return redirect()->route('admin.maps.index')
                ->with('success', __('Map deleted successfully.'));
        } catch (\Exception $e) {
            Log::error('Error deleting map: ' . $e->getMessage());
            return back()->with('error', __('Error deleting map. Please try again.'));
        }
    }

    /**
     * Reorder maps
     */
    public function reorder(ReorderMapRequest $request)
    {
        $request->validated();

        try {
            foreach ($request->items as $index => $id) {
                Map::where('id', $id)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error reordering maps: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('Error reordering maps')], 500);
        }
    }
}
