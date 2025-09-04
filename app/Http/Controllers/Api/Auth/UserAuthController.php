<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as PasswordRule;

class UserAuthController extends Controller
{

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', Password::min(8), 'confirmed'], // requires password_confirmation
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // أرسل إشعار تحقق البريد
        event(new Registered($user));
        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Registered. Verification email sent.',
        ], 201);
    }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $cred['email'])->first();
        if (! $user || ! Hash::check($cred['password'], $user->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 422);
        }

        $tokenName = $cred['device_name'] ?? ($request->userAgent() ?: 'api-token') . '-' . ($request->ip() ?: 'local');

        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'token_type' => 'Bearer',
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'email_verified' => ! is_null($user->email_verified_at),
            ],
        ]);
    }

    public function logout(Request $request)
    {
      
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => true, 'message' => 'Logged out']);
    }

    public function whoami(Request $request)
    {
        $u = $request->user();


        return response()->json([
            'success' => true,
            'data' => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'email_verified' => !is_null($u->email_verified_at),
                'roles' => method_exists($u, 'getRoleNames') ? $u->getRoleNames() : [],
            ],
        ]);
    }

    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['success' => true, 'message' => 'Already verified']);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['success' => true, 'message' => 'Verification email re-sent']);
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['success' => false, 'message' => 'Invalid verification link'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['success' => true, 'message' => 'Email already verified']);
        }

        // التحقق من توقيع الرابط
        if (! URL::hasValidSignature($request)) {
            return response()->json(['success' => false, 'message' => 'Invalid/expired signature'], 400);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['success' => true, 'message' => 'Email verified successfully']);
    }
    public function showProfile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'location' => $user->location,
                'bio'   => $user->bio,
                
                'image' => $user->image ? Storage::url($user->image) : null,
            ],
        ]);
    }


    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'          => ['sometimes', 'string', 'max:255'],
            'email'         => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'         => ['sometimes', 'nullable', 'string', 'max:50'],
            'location'      => ['sometimes', 'nullable', 'string', 'max:255'],
            'bio'           => ['sometimes', 'nullable', 'string', 'max:2000'],
            'image'         => ['sometimes', 'file', 'image', 'max:2048'],
            'avatar'        => ['sometimes', 'file', 'image', 'max:2048'],
            'remove_image' => ['sometimes', 'boolean'],
        ]);

        $emailChanged = isset($data['email']) && $data['email'] !== $user->email;

        $user->fill(collect($data)->only([
            'name',
            'email',
            'phone',
            'location',
            'bio'
        ])->toArray());

        // حذف الصورة لو طُلب
        if ($request->boolean('remove_image') && $user->image) {
            if (Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $user->image = null;
        }

        // رفع صورة جديدة (image أو avatar)
        $file = $request->file('image') ?: $request->file('avatar');
        if ($file) {
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $path = $file->store('profiles', 'public');
            $user->image = $path;
        }

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated.',
            'data'    => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'location'  => $user->location,
                'bio'       => $user->bio,
                'image_url' => $user->image ? Storage::url($user->image) : null,
            ],
        ]);

        Log::info('has image?', [$request->hasFile('image'), $request->file('image')?->getClientOriginalName()]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password'      => ['required', 'string'],
            'password'              => ['required', 'confirmed', PasswordRule::min(8)],
            // بديل لـ confirmed: مرّر password_confirmation
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = $request->user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'FCM token updated successfully'
        ]);
    }
}
