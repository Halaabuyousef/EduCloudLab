@extends('master')
@section('title' , )
@section('content')
<section class="login-section">
    <div class="login-container">
        <div class="login-illustration">
            <h2>مرحبًا بعودتك!</h2>
            <p>سجل دخولك الآن للوصول إلى آلاف المشاريع والفرص الاستثنائية في أكبر منصة عمل حر عربية</p>
            <img src="https://cdn.dribbble.com/users/1577044/screenshots/6539426/authentication.png" alt="تسجيل الدخول">
        </div>

        <div class="login-form-container">
            <div class="login-header">
                <h1>تسجيل الدخول</h1>
                <p>أدخل بيانات حسابك للوصول إلى لوحة التحكم</p>
            </div>

            <form id="loginForm" method="POST" action="{{ route($guard.'.login.submit') }}">
                @csrf
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input name="email" type="email" id="email" class="form-control" placeholder="ادخل بريدك الإلكتروني" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> كلمة المرور</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input name="password" type="password" id="password" class="form-control" placeholder="ادخل كلمة المرور" required>
                    </div>
                </div>

                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember">
                        <label for="remember">تذكرني</label>
                    </div>

                    <a href="#" class="forgot-password">هل نسيت كلمة المرور؟</a>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                </button>

                <div class="social-login">
                    <p>أو سجل الدخول باستخدام</p>
                    <div class="social-buttons">
                        <div class="social-btn google">
                            <i class="fab fa-google"></i>
                        </div>
                        <div class="social-btn facebook">
                            <i class="fab fa-facebook-f"></i>
                        </div>
                        <div class="social-btn twitter">
                            <i class="fab fa-twitter"></i>
                        </div>
                    </div>
                </div>

                <div class="signup-link">
                    @if ($guard != 'admin')
                    ليس لديك حساب؟ <a href="{{ route($guard.'.register') }}">إنشاء حساب جديد</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</section>

@endsection