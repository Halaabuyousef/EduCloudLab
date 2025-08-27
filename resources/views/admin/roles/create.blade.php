@extends('admin.app')

@section('title','Create Role')

@section('content')
<div class="container py-6">
    <h2>Create Role ({{ ucfirst($guard) }})</h2>

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        <input type="hidden" name="guard_name" value="{{ $guard }}">

        <div class="mb-3">
            <label class="form-label">Role Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Permissions</label> <br>
            <label><input type="checkbox" id="check_all"> All Permissions</label>
            <ul style="column-count: 2" class="list-unstyled">
                @foreach($permissions as $perm)
                <div class="form-check">
                    <input type="checkbox"
                        name="abilities[]"
                        value="{{ $perm->name }}" {{-- هنا الاسم مش id --}}
                        id="perm{{ $perm->id }}"
                        class="form-check-input">
                    <label for="perm{{ $perm->id }}" class="form-check-label">{{ $perm->name }}</label>
                </div>
                @endforeach

            </ul>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('admin.roles.index',['guard'=>$guard]) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
@section('js')

<script>
    $('#check_all').change(function() {

        $('ul input[type=checkbox]').prop('checked', this.checked)

    })
</script>

@endsection