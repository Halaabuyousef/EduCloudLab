<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{


    public function index()
    {
        $admins = Admin::paginate(10);
        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email',
            'password' => 'required|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('admins', 'public'); // storage/app/public/admins
        }

        \App\Models\Admin::create($data);

        return back()->with(['msg' => 'Admin created successfully', 'type' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $admin = \App\Models\Admin::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('image')) {
            // حذف القديمة إن وجدت
            if ($admin->image && Storage::disk('public')->exists($admin->image)) {
                Storage::disk('public')->delete($admin->image);
            }
            $data['image'] = $request->file('image')->store('admins', 'public');
        }

        $admin->update($data);

        return back()->with(['msg' => 'Admin updated successfully', 'type' => 'success']);
    }

    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.admins.edit', compact('admin'));
    }

 

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();
        return redirect()->route('admin.admins.index')->with('msg', 'Admin moved to trash')->with('type','warning');
    }



    public function trash()
    {
        $admins = \App\Models\Admin::onlyTrashed()->paginate(10);
        return view('admin.admins.trash', compact('admins'));
    }

    public function restore($id)
    {
        $admin = \App\Models\Admin::onlyTrashed()->findOrFail($id);
        $admin->restore();
        return redirect()->route('admin.admins.trash')
            ->with('msg', 'Admin restored successfully!')
            ->with('type', 'success');
    }

    public function forceDelete($id)
    {
        $admin = \App\Models\Admin::onlyTrashed()->findOrFail($id);
        $admin->forceDelete();
        return redirect()->route('admin.admins.trash')
            ->with('msg', 'Admin permanently deleted!')
            ->with('type', 'danger');
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function profile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    public function profile_update(Request $request)
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($admin->id)],
            'file'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // لو أرسلت من form عادية
        ]);

        // لو وصل ملف من الفورم (غير Dropzone)
        if ($request->hasFile('file')) {
            if ($admin->image && Storage::disk('public')->exists($admin->image)) {
                Storage::disk('public')->delete($admin->image);
            }
            $path = $request->file('file')->store('images', 'public'); // استخدم disk=public
            $admin->image = $path;
        }

        $admin->name  = $data['name'];
        if (!empty($data['email'])) {
            $admin->email = $data['email'];
        }
        $admin->save();

        return back()->with(['msg' => 'Profile updated', 'type' => 'success']);
    }

    // استدعاؤها من Dropzone
    public function profile_image(Request $request)
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($admin->image && Storage::disk('public')->exists($admin->image)) {
            Storage::disk('public')->delete($admin->image);
        }

        $path = $request->file('file')->store('images', 'public'); // public
        $admin->update(['image' => $path]);

        return response()->json([
            'success' => true,
            'url'     => asset('storage/' . $path),  // عرض عبر storage link
        ]);
    }

    public function profile_password()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile_password', compact('admin'));
    }

    public function profile_password_update(Request $request)
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'old_password' => ['required'],
            'password'     => ['required', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->old_password, $admin->password)) {
            return back()->with(['msg' => 'Old password does not match', 'type' => 'danger']);
        }

        $admin->update(['password' => Hash::make($request->password)]);

        return back()->with(['msg' => 'Password updated', 'type' => 'success']);
    }
}
