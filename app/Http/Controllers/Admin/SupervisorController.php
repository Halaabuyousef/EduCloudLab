<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class SupervisorController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $supervisors = Supervisor::withCount('users')
            ->when($q, fn($qq) => $qq->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")->orWhere('email', 'like', "%$q%");
            }))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $universities = \App\Models\University::orderBy('name')->get(['id', 'name']); // لتعبئة السليكت
        return view('supervisors.index', compact('supervisors', 'q', 'universities'));
    }



    public function create()
    {
        return view('supervisors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:190'],
            'email'    => ['required', 'email', 'unique:supervisors,email'],
            'password' => ['required', 'min:6'],
            'phone'    => ['nullable', 'string', 'max:50'],
            'country'  => ['nullable', 'string', 'max:120'],
            'bio'      => ['nullable', 'string'],
            'university_id' => ['nullable', 'exists:universities,id'],
        ]);
        $data['password'] = Hash::make($data['password']);

        $sup = Supervisor::create($data);
        return redirect()->route('admin.supervisors.show', $sup)->with('ok', 'تم إنشاء المشرف.');
    }

    public function show(Supervisor $supervisor)
    {
        $members = User::where('supervisor_id', $supervisor->id)
            ->latest('id')->paginate(10)->withQueryString();

        $independents = User::whereNull('supervisor_id')
            ->orderBy('name')->get(['id', 'name', 'email']); // لاختيار ربطهم بالمشرف

        return view('supervisors.show', compact('supervisor', 'members', 'independents'));
    }

    public function edit(Supervisor $supervisor)
    {
        return view('supervisors.edit', compact('supervisor'));
    }

    public function update(Request $request, Supervisor $supervisor)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:190'],
            'email'    => ['required', 'email', Rule::unique('supervisors', 'email')->ignore($supervisor->id)],
            'password' => ['nullable', 'min:6'],
            'phone'    => ['nullable', 'string', 'max:50'],
            'country'  => ['nullable', 'string', 'max:120'],
            'bio'      => ['nullable', 'string'],
            'university_id' => ['nullable', 'exists:universities,id'],
        ]);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $supervisor->update($data);
        return redirect()->route('admin.supervisors.show', $supervisor)->with('ok', 'تم تحديث بيانات المشرف.');
    }

    public function destroy(Supervisor $supervisor)
    {
        // إن فعّلت SoftDeletes استبدلها بـ $supervisor->delete();
        $supervisor->delete();
        return redirect()->route('admin.supervisors.index')->with('ok', 'تم حذف المشرف.');
    }

    // ربط مستخدم مستقل بهذا المشرف
    public function attachMember(Request $request, Supervisor $supervisor)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::where('id', $data['user_id'])->whereNull('supervisor_id')->firstOrFail();
        $user->supervisor_id = $supervisor->id;
        $user->save();

        return back()->with('ok', 'تم إضافة العضو للمشرف.');
    }

    // إزالة عضو من هذا المشرف (يصير مستقلاً)
    public function detachMember(Supervisor $supervisor, User $user)
    {
        abort_unless($user->supervisor_id === $supervisor->id, 403);
        $user->supervisor_id = null;
        $user->save();

        return back()->with('ok', 'تم إزالة العضو من المشرف.');
    }
}
