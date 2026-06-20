<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

use App\Interfaces\Services\Admin\UserServiceInterface;

class UserController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {
    }


    public function index()
    {
        $users = User::where("role", "!=", "admin")->paginate(50);
        return view('admin.users.index', compact('users'));
    }

    public function destroy($id)
    {
        $user = $this->userService->findOrFail($id);
        $user->delete();

        return redirect()->route('admin.registered-users')->with('success', __('User deleted successfully.'));
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }
}
