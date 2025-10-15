<?php
namespace App\Repositories;

use App\Models\IndividualProjectOrder;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\DB;

class IndividualProjectOrderRepository extends BaseRepository
{
    public function __construct(public IndividualProjectOrder $individualProjectOrder)
    {
    }

    public function getIndividualProjectOrders($request)
    {
        $query = $this->individualProjectOrder->with('translates.language');
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        return $query->ordered()->get();
    }

    public function createIndividualProjectOrder(array $data, array $translates)
    {
        return DB::transaction(function () use ($data, $translates) {
            $individualProjectOrder = $this->individualProjectOrder->create([
                'status' => $data['status'] ?? true
            ]);

            foreach ($translates as $translate) {
                $individualProjectOrder->translates()->create([
                    'language_id' => $translate['language_id'],
                    'title' => $translate['title'],
                    'description' => $translate['description'],
                ]);
            }

            return $individualProjectOrder;
        });
    }

    public function updateIndividualProjectOrder(IndividualProjectOrder $individualProjectOrder, array $data, array $translates)
    {
        return DB::transaction(function () use ($individualProjectOrder, $data, $translates) {
            $individualProjectOrder->update([
                'status' => $data['status'] ?? true
            ]);

            $individualProjectOrder->translates()->delete();

            foreach ($translates as $translate) {
                $individualProjectOrder->translates()->create([
                    'language_id' => $translate['language_id'],
                    'title' => $translate['title'],
                    'description' => $translate['description'],
                ]);
            }

            return $individualProjectOrder;
        });
    }

    public function deleteIndividualProjectOrder(IndividualProjectOrder $individualProjectOrder)
    {
        return DB::transaction(function () use ($individualProjectOrder) {
            $individualProjectOrder->translates()->delete();
            return $individualProjectOrder->delete();
        });
    }

    public function reorderIndividualProjectOrders(array $order)
    {
        return DB::transaction(function () use ($order) {
            foreach ($order as $index => $id) {
                $this->individualProjectOrder->where('id', $id)->update(['order' => $index + 1]);
            }
            return true;
        });
    }

    public function deleteSelected(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            $this->individualProjectOrder->whereIn('id', $ids)
                ->get()
                ->each(function ($item) {
                    $item->translates()->delete();
                    $item->delete();
                });
            return count($ids);
        });
    }
}
