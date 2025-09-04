<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        
        ]);

   
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

      
        $user->sendEmailVerificationNotification();

 
        return response()->json([
            'success' => true,
            'message' => 'Registered successfully. Please verify your email.',
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = \App\Models\User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        // اجعل الاسم اختياري
        $deviceName = $data['device_name'] ?? ($request->userAgent() ?: 'Web App');

        // Sanctum token:
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames() : [],
            ],
        ]);
    }
    public function whoami(Request $request)
    {
        $u = $request->user();
        return response()->json([
            'success' => true,
            'data'    => [
                'id'    => $u->id,
                'name'  => $u->name,
                'email' => $u->email,
                'roles' => $u->getRoleNames(),  
                'permissions' => $u->getAllPermissions()->pluck('name'),
            ]
        ]);
    }
    // بيانات المستخدم الحالي
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $request->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out',
            'data'    => null,
        ]);
    }


    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices',
            'data'    => null,
        ]);
    }

    public function abilities(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => [
                'roles'          => $user->getRoleNames()->values(),
                'permissions'    => $user->getAllPermissions()->pluck('name')->values(),
                'token_abilities' => $user->currentAccessToken()?->abilities ?? [],
            ],
        ]);
    }
}