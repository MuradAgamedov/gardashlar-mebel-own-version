<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Subscriber\BulkDeleteSubscriberRequest;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Interfaces\Services\Admin\SubscriberServiceInterface;

class SubscriberController extends Controller
{
    public function __construct(
        protected SubscriberServiceInterface $subscriberService
    ) {
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subscriber::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('email', 'like', "%{$search}%");
        }

        // Order by created_at desc
        $query->orderBy('created_at', 'desc');

        $subscribers = $query->paginate(15);

        return view('admin.subscribers.index', compact('subscribers'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $subscriber = $this->subscriberService->findOrFail($id);
            $subscriber->delete();

            return redirect()->route('admin.subscribers.index')
                ->with('success', __('Subscriber deleted successfully!'));
        } catch (\Exception $e) {
            return redirect()->route('admin.subscribers.index')
                ->with('error', __('Error deleting subscriber: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Bulk delete subscribers
     */
    public function bulkDelete(BulkDeleteSubscriberRequest $request)
    {
        $request->validated();

        try {
            $deletedCount = Subscriber::whereIn('id', $request->subscriber_ids)->delete();

            return redirect()->route('admin.subscribers.index')
                ->with('success', __("Successfully deleted :count subscriber(s)!", ['count' => $deletedCount]));
        } catch (\Exception $e) {
            return redirect()->route('admin.subscribers.index')
                ->with('error', __('Error deleting subscribers: :error', ['error' => $e->getMessage()]));
        }
    }
}
