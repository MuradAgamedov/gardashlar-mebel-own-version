<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialNetwork;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SocialNetworkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $socialNetworks = SocialNetwork::ordered()->get();
        return view('admin.social-networks.index', compact('socialNetworks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $types = SocialNetwork::getTypes();
        return view('admin.social-networks.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'url' => 'required|url|max:500',
            'order' => 'nullable|integer|min:0',
            'status' => 'boolean'
        ]);

        try {
            SocialNetwork::create([
                'type' => $request->type,
                'name' => $request->name,
                'icon' => $request->icon,
                'url' => $request->url,
                'order' => $request->order ?? 0,
                'status' => $request->has('status')
            ]);

            return redirect()->route('admin.social-networks.index')
                ->with('success', 'Social network created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating social network. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SocialNetwork $socialNetwork): View
    {
        $types = SocialNetwork::getTypes();
        return view('admin.social-networks.edit', compact('socialNetwork', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SocialNetwork $socialNetwork): RedirectResponse
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'url' => 'required|url|max:500',
            'order' => 'nullable|integer|min:0',
            'status' => 'boolean'
        ]);

        try {
            $socialNetwork->update([
                'type' => $request->type,
                'name' => $request->name,
                'icon' => $request->icon,
                'url' => $request->url,
                'order' => $request->order ?? 0,
                'status' => $request->has('status')
            ]);

            return redirect()->route('admin.social-networks.index')
                ->with('success', 'Social network updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating social network. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocialNetwork $socialNetwork): RedirectResponse
    {
        try {
            $socialNetwork->delete();
            return redirect()->route('admin.social-networks.index')
                ->with('success', 'Social network deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting social network. Please try again.');
        }
    }

    /**
     * Reorder social networks
     */
    public function reorder(Request $request): RedirectResponse
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'integer|exists:social_networks,id'
        ]);

        try {
            foreach ($request->orders as $order => $id) {
                SocialNetwork::where('id', $id)->update(['order' => $order]);
            }

            return redirect()->route('admin.social-networks.index')
                ->with('success', 'Social networks reordered successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error reordering social networks. Please try again.');
        }
    }

    /**
     * Toggle status
     */
    public function toggleStatus(SocialNetwork $socialNetwork): RedirectResponse
    {
        try {
            $socialNetwork->update(['status' => !$socialNetwork->status]);
            
            $status = $socialNetwork->status ? 'enabled' : 'disabled';
            return redirect()->route('admin.social-networks.index')
                ->with('success', "Social network {$status} successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating social network status. Please try again.');
        }
    }
}
