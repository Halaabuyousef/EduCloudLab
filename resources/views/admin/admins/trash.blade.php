@extends('admin.app')

@section('title','Trashed Admins')

@section('content')
<div class="container py-6">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h2 class="mb-0">Trashed Admins</h2>

        <div class="d-flex gap-3">
            <a href="{{ route('admin.admins.index') }}" class="btn btn-light">
                ← Back to All
            </a>
        </div>
    </div>

    {{-- Flash --}}
    @if (session('msg'))
    <div id="alertBox" class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
        {{ session('msg') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        setTimeout(function() {
            const el = document.getElementById('alertBox');
            if (el)(new bootstrap.Alert(el)).close();
        }, 1000);
    </script>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-row-bordered gy-5 align-middle">
                <thead>
                    <tr class="fw-bold fs-6 text-muted">
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                    <tr>
                        <td>{{ $admin->id }}</td>
                        <td>
                            @if($admin->image)
                            <img src="{{ asset('storage/'.$admin->image) }}" width="42" height="42" class="rounded-circle" alt="avatar">
                            @else
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center"
                                style="width:42px;height:42px;">
                                <i class="fas fa-user text-muted"></i>
                            </div>
                            @endif
                        </td>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ $admin->deleted_at->format('d M Y H:i') }}</td>
                        <td>
                            {{-- Restore --}}
                            <form action="{{ route('admin.admins.restore',$admin->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success">Restore</button>
                            </form>

                            {{-- Force Delete --}}
                            <form action="{{ route('admin.admins.forceDelete',$admin->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Delete permanently?')" class="btn btn-sm btn-danger">Delete Permanently</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Trash is empty</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection