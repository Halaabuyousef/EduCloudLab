@extends('master')
@section('content')
<section class="confirmation-section">
    <div class="confirmation-container">
        <div class="confirmation-icon">
            <i class="fas fa-paper-plane"></i>
        </div>

        <div class="confirmation-header">
            <h1>تم إرسال رابط التحقق</h1>
        </div>

        <div class="confirmation-message">
            <p>لقد أرسلنا رابط تحقق إلى بريدك الإلكتروني <strong>example@email.com</strong>. يرجى التحقق من صندوق الوارد الخاص بك.</p>
            <p>إذا لم تجد البريد الإلكتروني، يرجى التحقق من مجلد الرسائل غير المرغوب فيها (Spam) أو إعادة إرسال الرابط.</p>
        </div>

        <button class="btn-home">
            <i class="fas fa-home"></i> العودة إلى الصفحة الرئيسية
        </button>

        <div class="resend-link">
            لم تصلك الرسالة؟ <a href="#">إعادة إرسال رابط التحقق</a>
        </div>
    </div>
</section>
@stop