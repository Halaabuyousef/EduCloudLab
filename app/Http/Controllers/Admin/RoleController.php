<?php

namespace App\Http\Controllers\Admin;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        // تقدر تمرر نوع الحارس كـ query ?guard=admin
        $guard = $request->get('guard', 'admin');

        $roles = Role::with('permissions')
            ->where('guard_name', $guard)
            ->latest('id')
            ->paginate(10);

        return view('admin.roles.index', compact('roles', 'guard'));
    }

    public function create(Request $request)
    {
        $guard = $request->get('guard', 'admin');

        // فقط الصلاحيات التابعة للحارس
        $permissions = Permission::where('guard_name', $guard)->get();

        return view('admin.roles.create', compact('permissions', 'guard'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:190',
            'guard_name' => 'required|in:web,admin,supervisor',
            'abilities'  => 'array'
        ]);

        $role = Role::create([
            'name'       => $request->name,
            'guard_name' => $request->guard_name,
        ]);

        if ($request->filled('abilities')) {
            $role->syncPermissions($request->abilities);
        }

        return redirect()->route('admin.roles.index', ['guard' => $request->guard_name])
            ->with('msg', 'Role created successfully');
    }

    public function edit($id, Request $request)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::where('guard_name', $role->guard_name)->get();

        return view('admin.roles.edit', compact('permissions', 'role'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required|string|max:190',
            'abilities' => 'array'
        ]);

        $role = Role::findOrFail($id);

        $role->update([
            'name' => $request->name,
        ]);

        $role->syncPermissions($request->abilities ?? []);

        return redirect()->route('admin.roles.index', ['guard' => $role->guard_name])
            ->with('msg', 'Role updated successfully');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $guard = $role->guard_name;
        $role->delete();

        return redirect()->route('admin.roles.index', ['guard' => $guard])
            ->with('msg', 'Role deleted successfully');
    }
}
