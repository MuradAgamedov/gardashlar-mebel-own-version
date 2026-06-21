<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\BecomeAInvestorFormRepositoryInterface;
use App\Models\BecomeAInvestorForm;
use App\Repositories\BaseRepository;

class BecomeAInvestorFormRepository extends BaseRepository implements BecomeAInvestorFormRepositoryInterface
{
    public function __construct(BecomeAInvestorForm $model)
    {
        parent::__construct($model);
    }

    public function getForms($request)
    {
        $query = $this->model->query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function deleteForm($form)
    {
        return $form->delete();
    }

    public function markAsRead($form)
    {
        return $form->markAsRead();
    }

    public function deleteSelected(array $ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }
}
