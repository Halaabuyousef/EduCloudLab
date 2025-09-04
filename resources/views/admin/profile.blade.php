@extends('admin.app')

@section('content')
<div class="card col-12">
    <div class="card-body">

        <div class="kt-portlet__body">
            @if(session('msg'))
            <div class="alert alert-{{ session('type','success') }}">{{ session('msg') }}</div>
            @endif

            <div class="admin_img_wrapper mb-3 text-center">
                @if($admin->image)
                <img class="admin_img img-thumbnail rounded-circle"
                    style="max-width:160px; height:160px; object-fit:cover;"
                    src="{{ asset('storage/'.$admin->image) }}" alt="Pic" />
                @endif
            </div>


            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" value="{{ old('name',$admin->name) }}"
                        class="form-control @error('name') is-invalid @enderror" />
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Email </label>
                    <input type="email" name="email" value="{{ old('email',$admin->email) }}"
                        class="form-control @error('email') is-invalid @enderror" />
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>


                {{-- Dropzone (رفع سريع) --}}
                <div class="mb-3">
                    <label class="form-label">Upload avatar via Dropzone</label>
                    <div class="dropzone" id="dropimage"></div>
                </div>
                <button class="btn btn-success"><i class="fas fa-save"></i> Update</button>
            </form>
            <div class="menu-item px-5">
                <span class="kt-portlet__head-icon">

                </span>
                <a href="{{ route('admin.profile_password.edit') }}" class="menu-link px-5"> <i class="fas fa-lock"></i>  Change Password</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />


<script>
    Dropzone.autoDiscover = false;

    let myDropzone = new Dropzone("#dropimage", {
        url: "{{ route('admin.profile_image') }}",
        method: "post",
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        paramName: "file",
        maxFiles: 1,
        acceptedFiles: "image/*",
        // اختياري: شكل الرسالة الافتراضية
        dictDefaultMessage: "Drop image here or click to upload",
        success: function(file, res) {
            if (res.success) {
                // حدّث الصورة الكبيرة في صفحة البروفايل
                const big = document.querySelector('.admin_img');
                if (big) big.src = res.url;

                // حدّث صورة النافبار (انظر النقطة 2)
                const nav = document.getElementById('navbarAvatar');
                if (nav) nav.src = res.url;

                // امسح المعاينة بعد 3 ثواني
                setTimeout(() => {
                    // الطريقة الأولى: إزالة الملف من Dropzone (تمسح المعاينة)
                    myDropzone.removeFile(file);

                    // أو لمسح كل المعاينات إن ظهرت عدة مرات:
                    // myDropzone.removeAllFiles(true);
                }, 3000);
            }
        }
    });
</script>

@endsection