<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;

use App\Models\Supervisor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        
        $filter = $request->string('filter')->toString();   // '', 'attached', 'independent'
        $q      = $request->string('q')->toString();

        $usersQuery = User::with('supervisor:id,name')
            ->when($filter === 'attached', fn($qq) => $qq->whereNotNull('supervisor_id'))
            ->when($filter === 'independent', fn($qq) => $qq->whereNull('supervisor_id'))
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%");
                });
            })
            ->latest('id');

        
        $users = $usersQuery->paginate(10, ['*'], 'users_page');

       
        $supervisors = Supervisor::with(['users:id,name,email,supervisor_id'])
            ->withCount('users')
            ->latest('id')
            ->paginate(10, ['*'], 'sup_page');

        return view('users.index', compact('users', 'supervisors', 'filter', 'q'));
    }
    // function getdata(Request $request)
    // {
    //     // name , email , password
    //     // المستخدم : احمد
    //     $users = User::query();
    //     return DataTables::of($users)
    //         ->addColumn('editname', function ($user) {
    //             return "<span style='color:red'>$user->name</span> ";
    //         })

    //         ->addColumn('action', function ($qur) {
    //             $data_attr = ' ';
    //             $data_attr .= 'data-id="' . $qur->id . '" ';
    //             $data_attr .= 'data-name="' . $qur->name . '" ';
    //             $data_attr .= 'data-email="' . $qur->email . '" ';
    //             $data_attr .= 'data-password="' . $qur->password . '" ';

    //             $action = '';
    //             $action .= '<div class="d-flex align-items-center gap-3 fs-6">';

    //             $action .= '<a ' . $data_attr . '  data-bs-toggle="modal" data-bs-target="#updateModal" class="text-warning update_btn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="Edit info" aria-label="Edit">تعديل</a>';

    //             $action .= '     <a data-id="' . $qur->id . '"  data-url="/admin/user/delete" class="text-danger delete_btn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="Delete" aria-label="Delete">حذف</a>';

    //             $action .= '</div>';

    //             return $action;
    //         })
    //         ->rawColumns(['editname', 'action'])
    //         ->make(true);
    // }

    public function attachSupervisor(Request $request, User $user)
    {
        $data = $request->validate([
            'supervisor_id' => ['required', 'exists:supervisors,id'],
        ]);
        $user->supervisor_id = $data['supervisor_id'];
        $user->save();

        return back()->with('ok', 'The user has been linked to the supervisor.');
    }

    public function detachSupervisor(User $user)
    {
        $user->supervisor_id = null;
        $user->save();
        return back()->with('ok', 'The user has been unlinked from the supervisor.');
    }
}
