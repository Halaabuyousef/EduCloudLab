<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Supervisor;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\URL;
class SupervisorAuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:supervisors,email'],
            'password'              => ['required', Password::min(8), 'confirmed'],
        ]);

        $sup = Supervisor::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($sup));
        $sup->sendEmailVerificationNotification();

        return response()->json(['success' => true, 'message' => 'Registered. Verification email sent.'], 201);
    }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $sup = Supervisor::where('email', $cred['email'])->first();
        if (! $sup || ! Hash::check($cred['password'], $sup->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 422);
        }

        $tokenName = $cred['device_name'] ?? ($request->userAgent() ?: 'api-token') . '-' . ($request->ip() ?: 'local');
        $token = $sup->createToken($tokenName)->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'token_type' => 'Bearer',
            'data' => [
                'id' => $sup->id,
                'name' => $sup->name,
                'email' => $sup->email,
                'email_verified' => !is_null($sup->email_verified_at),
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
        $u = $request->user();
        if ($u->hasVerifiedEmail()) {
            return response()->json(['success' => true, 'message' => 'Already verified']);
        }
        $u->sendEmailVerificationNotification();
        return response()->json(['success' => true, 'message' => 'Verification email re-sent']);
    }

    public function verify(Request $request, $id, $hash)
    {
        $u = Supervisor::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($u->getEmailForVerification()))) {
            return response()->json(['success' => false, 'message' => 'Invalid verification link'], 400);
        }
        if ($u->hasVerifiedEmail()) {
            return response()->json(['success' => true, 'message' => 'Email already verified']);
        }
        if (! URL::hasValidSignature($request)) {
            return response()->json(['success' => false, 'message' => 'Invalid/expired signature'], 400);
        }

        $u->markEmailAsVerified();
        event(new Verified($u));

        return response()->json(['success' => true, 'message' => 'Email verified successfully']);
    }
}
