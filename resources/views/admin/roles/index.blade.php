@extends('admin.app')

@section('title','Roles')

@section('content')
<div class="container py-6">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h2 class="mb-0">Roles ({{ ucfirst($guard) }})</h2>
        <div class="d-flex gap-3">
            <a href="{{ route('admin.roles.create',['guard'=>$guard]) }}" class="btn btn-light-primary">
                <i class="fas fa-plus me-2"></i>Add Role
            </a>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $guard=='admin' ? 'active' : '' }}" href="{{ route('admin.roles.index',['guard'=>'admin']) }}">Admins</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $guard=='supervisor' ? 'active' : '' }}" href="{{ route('admin.roles.index',['guard'=>'supervisor']) }}">Supervisors</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $guard=='web' ? 'active' : '' }}" href="{{ route('admin.roles.index',['guard'=>'web']) }}">Students</a>
        </li>
    </ul>

    {{-- Roles Table --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Permissions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ $role->name }}</td>
                <td>
                    @foreach($role->permissions as $perm)
                    <span class="badge bg-secondary">{{ $perm->name }}</span>
                    @endforeach
                </td>
                <td>
                    <a href="{{ route('admin.roles.edit',['role'=>$role->id,'guard'=>$guard]) }}" class="btn btn-sm btn-info">Edit</a>
                    <form action="{{ route('admin.roles.destroy',['role'=>$role->id,'guard'=>$guard]) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this role?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">No roles found for this guard.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $roles->links() }}
</div>
@endsection