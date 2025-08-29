@extends('admin.app') {{-- or your layout, e.g. admin.app/web.app --}}
@section('title', __('Contact Us'))

@section('content')
<div class="container py-6">
    {{-- Hero --}}
    <div class="card border-0 mb-6 shadow-sm"
        style="background:linear-gradient(135deg,#6246EA 0%,#6EC1E4 100%);">
        <div class="card-body p-6 p-lg-10 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="text-white mb-1">{{ __('Contact Us') }}</h2>
                <p class="text-white-50 mb-0">{{ __('We’d love to hear from you. Send us a message below.') }}</p>
            </div>
            <i class="fas fa-envelope-open-text text-white-75 fs-1"></i>
        </div>
    </div>

    {{-- Alerts (fallback if SweetAlert is blocked) --}}
    @if (session('msg'))
    <div id="alertBox" class="alert alert-{{ session('type','success') }} alert-dismissible fade show" role="alert">
        {{ session('msg') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
    @endif

    {{-- Contact Form --}}
    <div class="card shadow-sm">
        <div class="card-body p-6">
            <form method="POST" action="{{ route('contact.store') }}" novalidate>
                @csrf

                {{-- Honeypot (must remain empty) --}}
                <input type="text" name="website" value="" style="display:none" tabindex="-1" autocomplete="off">

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Full name') }}</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror" required maxlength="120">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" required maxlength="190">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('Subject') }} <span class="text-muted">({{ __('optional') }})</span></label>
                        <input type="text" name="subject" value="{{ old('subject') }}"
                            class="form-control @error('subject') is-invalid @enderror" maxlength="190">
                        @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('Message') }}</label>
                        <textarea name="message" rows="6"
                            class="form-control @error('message') is-invalid @enderror"
                            required minlength="10" maxlength="5000">{{ old('message') }}</textarea>
                        @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>{{ __('Send Message') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
@if (session('msg'))
<!-- <script>
    // انتظر تحميل الصفحة ثم اعرض التنبيه
    window.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: @json(session('type', 'success')),
            title: @json(__('Thank you!')),
            text: @json(session('msg')),
            confirmButtonText: @json(__('OK'))
        });
    });
</script> -->
@endif
@endsection