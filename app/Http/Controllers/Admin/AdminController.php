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
