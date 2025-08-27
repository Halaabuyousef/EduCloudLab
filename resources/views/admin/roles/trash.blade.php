@extends('admin.app')

@section('title','Trashed Roles')

@section('content')
<div class="container py-6">
    <h2>Trashed Roles ({{ ucfirst($guard) }})</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Deleted At</th>
                <th>Restore</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ $role->name }}</td>
                <td>{{ $role->deleted_at }}</td>
                <td>
                    <form action="{{ route('admin.roles.restore',['role'=>$role->id,'guard'=>$guard]) }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-success">Restore</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">No trashed roles.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection