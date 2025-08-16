@extends('pages.app')

@section('content')
<section class="reset-section">
    <div class="reset-container">
        <div class="reset-header">
            <div class="reset-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h1>تعيين كلمة مرور جديدة</h1>
            <p>من فضلك أدخل كلمة مرور جديدة لحسابك. تأكد من أنها آمنة وسهلة التذكر</p>
        </div>

        <form id="resetForm" class="reset-form" method="POST" action="{{ route($guard.'.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> كلمة المرور الجديدة</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input name="password" type="password" id="password" class="form-control" placeholder="أدخل كلمة مرور جديدة" required>
                    <span class="password-toggle" id="passwordToggle">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>

                <div class="password-strength">
                    <div class="password-strength-meter" id="passwordStrength"></div>
                </div>

                <div class="password-rules">
                    <p>يجب أن تحتوي كلمة المرور على:</p>
                    <ul>
                        <li id="rule-length"><i class="fas fa-circle"></i> 8 أحرف على الأقل</li>
                        <li id="rule-uppercase"><i class="fas fa-circle"></i> حرف كبير واحد على الأقل</li>
                        <li id="rule-number"><i class="fas fa-circle"></i> رقم واحد على الأقل</li>
                        <li id="rule-special"><i class="fas fa-circle"></i> رمز خاص واحد على الأقل</li>
                    </ul>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword"><i class="fas fa-lock"></i> تأكيد كلمة المرور</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input name="password_confirmation" type="password" id="confirmPassword" class="form-control" placeholder="أعد إدخال كلمة المرور الجديدة" required>
                    <span class="password-toggle" id="confirmPasswordToggle">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div id="passwordMatch" class="password-rules" style="display:none;">
                    <p><i class="fas fa-check-circle valid"></i> كلمتا المرور متطابقتان</p>
                </div>
            </div>

            <button type="submit" class="btn-reset" id="resetBtn">
                <i class="fas fa-key"></i> تعيين كلمة المرور الجديدة
                <i class="fas fa-spinner fa-spin"></i>
                <i class="fas fa-check"></i>
            </button>

            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle"></i>
                <h3>تم تحديث كلمة المرور بنجاح!</h3>
                <p>تم تحديث كلمة المرور الخاصة بحسابك بنجاح. يمكنك الآن تسجيل الدخول باستخدام كلمة المرور الجديدة.</p>
            </div>
        </form>

        <div class="links-container">
            <a href="{{ route($guard.'.indexLogin') }}"><i class="fas fa-arrow-left"></i> العودة لتسجيل الدخول</a>
            @if ($guard != 'admin')
            ليس لديك حساب؟ <a href="{{ route($guard.'.register') }}">إنشاء حساب جديد</a>
            @endif
        </div>
    </div>
</section>
@endsection