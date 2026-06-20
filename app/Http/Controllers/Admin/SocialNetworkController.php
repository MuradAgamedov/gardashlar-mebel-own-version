<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SocialNetwork\ReorderSocialNetworkRequest;

use App\Http\Requests\Admin\SocialNetwork\StoreSocialNetworkRequest;

use App\Http\Controllers\Controller;
use App\Models\SocialNetwork;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use App\Interfaces\Services\Admin\SocialNetworkServiceInterface;
use App\Interfaces\Services\Admin\LanguageServiceInterface;

class SocialNetworkController extends Controller
{
    public function __construct(
        protected SocialNetworkServiceInterface $socialNetworkService,
        protected LanguageServiceInterface $languageService
    ) {
    }


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
    public function store(StoreSocialNetworkRequest $request): RedirectResponse
    {
        $request->validated();

        try {
            // Get the highest order and add 1 for new item
            $maxOrder = SocialNetwork::max('order') ?? 0;
            
            SocialNetwork::create([
                'type' => $request->type,
                'name' => $request->name,
                'icon' => $request->icon,
                'url' => $request->url,
                'order' => $maxOrder + 1,
                'status' => $request->has('status')
            ]);

            return redirect()->route('admin.social-networks.index')
                ->with('success', __('Social network created successfully.'));

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error creating social network: :error', ['error' => $e->getMessage()]));
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
        $request->validated();

        try {
            $socialNetwork->update([
                'type' => $request->type,
                'name' => $request->name,
                'icon' => $request->icon,
                'url' => $request->url,
                'status' => $request->has('status')
            ]);

            return redirect()->route('admin.social-networks.index')
                ->with('success', __('Social network updated successfully.'));

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error updating social network: :error', ['error' => $e->getMessage()]));
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
                ->with('success', __('Social network deleted successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting social network. Please try again.'));
        }
    }

    /**
     * Reorder social networks
     */
    public function reorder(ReorderSocialNetworkRequest $request): RedirectResponse
    {
        $request->validated();

        try {
            foreach ($request->orders as $order => $id) {
                SocialNetwork::where('id', $id)->update(['order' => $order]);
            }

            return redirect()->route('admin.social-networks.index')
                ->with('success', __('Social networks reordered successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Error reordering social networks. Please try again.'));
        }
    }

    /**
     * Toggle status
     */
    public function toggleStatus(SocialNetwork $socialNetwork): RedirectResponse
    {
        try {
            $socialNetwork->update(['status' => !$socialNetwork->status]);
            
            $status = $socialNetwork->status ? __('enabled') : __('disabled');
            return redirect()->route('admin.social-networks.index')
                ->with('success', __('Social network :status successfully.', ['status' => $status]));

        } catch (\Exception $e) {
            return back()->with('error', __('Error updating social network status. Please try again.'));
        }
    }
}
