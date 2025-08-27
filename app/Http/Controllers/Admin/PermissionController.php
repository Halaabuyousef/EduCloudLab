<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $guard = $request->get('guard', 'admin');

        $permissions = Permission::where('guard_name', $guard)
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.permissions.index', compact('permissions', 'guard'));
    }

    public function create(Request $request)
    {
        $guard = $request->get('guard', 'admin');
        return view('admin.permissions.create', compact('guard'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:190',
            'guard_name' => 'required|in:web,admin,supervisor',
        ]);

        Permission::firstOrCreate($data);

        // مهم لمسح كاش الحزمة بعد التعديل
        app()['cache']->forget('spatie.permission.cache');

        return redirect()->route('admin.permissions.index', ['guard' => $data['guard_name']])
            ->with('msg', 'Permission created');
    }

    public function edit(Permission $permission)
    {
        // نمنع تغيير الـ guard هنا للحفاظ على الاتساق
        $guard = $permission->guard_name;
        return view('admin.permissions.edit', compact('permission', 'guard'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
        ]);

        $permission->update(['name' => $data['name']]);

        app()['cache']->forget('spatie.permission.cache');

        return redirect()->route('admin.permissions.index', ['guard' => $permission->guard_name])
            ->with('msg', 'Permission updated');
    }

    public function destroy(Permission $permission)
    {
        $guard = $permission->guard_name;
        $permission->delete();

        app()['cache']->forget('spatie.permission.cache');

        return redirect()->route('admin.permissions.index', ['guard' => $guard])
            ->with('msg', 'Permission deleted');
    }
}
