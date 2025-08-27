@extends('admin.app')
@section('title','Create Permission')
@section('content')
<div class="container py-6">
    <h2>Create Permission ({{ ucfirst($guard) }})</h2>
    <form method="POST" action="{{ route('admin.permissions.store') }}">
        @csrf
        <input type="hidden" name="guard_name" value="{{ $guard }}">

        <div class="mb-3">
            <label class="form-label">Permission Name</label>
            <input type="text" class="form-control" name="name" placeholder="e.g. reservations.approve" required>
        </div>

        <button class="btn btn-primary">Save</button>
        <a class="btn btn-secondary" href="{{ route('admin.permissions.index',['guard'=>$guard]) }}">Cancel</a>
    </form>
</div>
@endsection