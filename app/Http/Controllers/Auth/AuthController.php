<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use App\Services\SocialiteService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    function indexLogin(Request $request)
    {
        $guard = $request->route('guard');
        return view($guard.'.login', compact('guard'));
    }

    function login(Request $request)
    {
        $guard = $request->route('guard');

        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        if (Auth::guard($guard)->attempt($data, $request->filled('remember'))) {
            // ✅ إذا الأدمن يرجعه ع الداشبورد تبع الأدمن
            if ($guard === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            // باقي الguards 
            return redirect()->route("{$guard}.dashboard");
        }
        return redirect()->back()->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }
   

    function indexRegister(Request $request)
    {
        $guard = $request->route('guard');
        return view('auth.register', compact('guard'));
    }

    function register(Request $request)
    {
        $guard = $request->route('guard');
        $provider = config("auth.guards.$guard.provider");
        $modelClass = config("auth.providers.$provider.model");
        $token = Str::random();

        DB::beginTransaction();

        try {
            $user = $modelClass::create([
                'fullname' => $request->fullname,
                'username' => $request->fullname,
                'phone' => '059252525',
                'country' => 'gaza',
                'email' => $request->email,
                'password' => $request->password,
                'verification_token' => $token,
                'verification_token_sent_at' => now(),
            ]);

            $user->notify(new VerifyEmailNotification($token, $guard));

            DB::commit();
            return redirect()->route('con');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ أثناء تسجيل المستخدم: ' . $e->getMessage());

            return back()->with('error', 'حدث خطأ أثناء إرسال بريد التحقق، الرجاء المحاولة لاحقًا.');
        }
    }


    function dashboard(Request $request)
    {
        $guard = $request->route('guard');
        return view($guard . '.dashboard', compact('guard'));
    }

    public function indexForgetPassword(Request $request)
    {
        $guard = $request->route('guard');
        return view('auth.forget-password', compact('guard'));
    }

    public function forgetPassword(Request $request)
    {
        $guard = $request->route('guard');
        $request->validate(['email' => 'required|email']);


        $broker = $this->getPasswordBroker($guard);

        $status = Password::broker($broker)->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token = null)
    {
        $guard = $request->route('guard');
        $email = $request->query('email');

        return view('auth.reset-password', compact('guard', 'token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $guard = $request->route('guard');
        $broker = $this->getPasswordBroker($guard);

        $status = Password::broker($broker)->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route("$guard.login")->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    protected function getPasswordBroker($guard)
    {
        return match ($guard) {
            'admin' => 'admins',
            'supervisor' => 'supervisors',
            default => 'users',
        };
    }


    // function logout(Request $request)
    // {
    //     $guard = $request->route('guard');
    //     Auth::guard($guard)->logout();
       
    // }
    public function logout(Request $request)
    {
        $guard = $request->route('guard');
        
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($guard .'.login');
    }
}
