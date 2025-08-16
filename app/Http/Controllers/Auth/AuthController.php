<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\VerifyEmailNotifcation;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\Notifications\VerifyEmail;

class AuthController extends Controller
{
    // function indexLogin(Request $request)
    // {
    //     $guard = $request->route('guard');
    //     return view('auth.login', compact('guard'));
    // }

    // function login(Request $request)
    // {
    //     $guard = $request->route('guard');
    //     $data = $request->validate([
    //         'email' => ['required', 'email'],
    //         'password' => ['required', 'string', 'min:8']
    //     ]);

    //     if (Auth::guard($guard)->attempt($data, $request->filled('remember'))) {
    //         return redirect()->route($guard . '.dashboard');
    //     }
    //     return redirect()->back();
    // }

    // function indexRegister(Request $request)
    // {
    //     $guard = $request->route('guard');
    //     return view('auth.register', compact('guard'));
    // }

    // function register(Request $request)
    // {

    //     $guard = $request->route('guard');


    //     $provider = config("auth.guards.$guard.provider");
    //     $modelClass = config("auth.providers.$provider.model");


    //     /* email_verified_at		*/
    //     $token = Str::random();
    //     $user = $modelClass::create([
    //         'name' => $request->fullname,
    //         'email' => $request->email,
    //         'password' => bcrypt($request->password),
    //         'verification_token' => $token,
    //         'verification_token_sent_at' => now(),
    //     ]);


    //     // $user->notify(new VerifyEmailNotification($token, $guard));

    //     return redirect()->route($guard . '.indexLogin');
    // }


    function dashboard(Request $request)
    {
        $guard = $request->route('guard');
        return view($guard . '.dashboard');
    }
}
