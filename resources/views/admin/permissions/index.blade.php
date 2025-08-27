@extends('admin.app')
@section('title','Permissions')
@section('content')
<div class="container py-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Permissions ({{ ucfirst($guard) }})</h2>
        <a href="{{ route('admin.permissions.create',['guard'=>$guard]) }}" class="btn btn-primary">Add Permission</a>
    </div>

    <ul class="nav nav-tabs mb-3">
        @foreach (['admin'=>'Admins','supervisor'=>'Supervisors','web'=>'Students'] as $g => $label)
        <li class="nav-item">
            <a class="nav-link {{ $guard===$g ? 'active' : '' }}"
                href="{{ route('admin.permissions.index',['guard'=>$g]) }}">{{ $label }}</a>
        </li>
        @endforeach
    </ul>

    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Guard</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($permissions as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->name }}</td>
                <td><span class="badge bg-secondary">{{ $p->guard_name }}</span></td>
                <td>
                    <a class="btn btn-sm btn-info" href="{{ route('admin.permissions.edit',$p) }}">Edit</a>
                    <form action="{{ route('admin.permissions.destroy',$p) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this permission?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">No permissions for this guard.</td>
            </tr>
            @endforelse
            
        </tbody>
    </table>

    {{ $permissions->appends(['guard'=>$guard])->links() }}
</div>
@endsection