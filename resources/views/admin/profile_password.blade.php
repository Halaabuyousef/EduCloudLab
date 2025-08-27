@extends('admin.app')
<!-- @section('css')

<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css"
    @endsection -->
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


            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    <span class="kt-portlet__head-icon">
                        <i class="fas fa-user"></i>
                    </span>

                    Update Your Password
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            @if (session('msg'))
            <div class="aleart aleart-{{ session('type') }}">
                {{ session('msg') }}
            </div>
            @endif
            <form action="{{ route('admin.profile_password.edit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Old Password</label>
                    <input type="password" name="old_password" class="form-control" />

                    @error('old_password')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>New Password</label>
                    <input type="password" name="password" class="form-control" />
                    @error('password')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" />
                </div>


                <button class="btn btn-success"><i class="fas fa-save"></i> Update</button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')

@endsection