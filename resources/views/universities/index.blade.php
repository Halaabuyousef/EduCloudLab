@extends('admin.app')
@section('title','Universities')

@section('content')
<div class="container py-6">

    <div class="d-flex justify-content-between align-items-center mb-6">
        <h2 class="mb-0">Universities</h2>
        <div class="d-flex gap-3">
            <form method="GET" class="d-flex gap-2">
                <input name="q" value="{{ $q }}" class="form-control form-control-solid" placeholder="Search by name">
                <button class="btn btn-light">Search</button>
            </form>
            <button class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#add-modal">
                <i class="fas fa-plus me-2"></i>Add University
            </button>
        </div>
    </div>

    @if(session('msg'))
    <div id="alertBox" class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
        {{ session('msg') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        setTimeout(() => {
            let el = document.getElementById('alertBox');
            if (el) {
                new bootstrap.Alert(el).close();
            }
        }, 1800);
    </script>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-bordered align-middle">
                    <thead>
                        <tr class="fw-bold fs-6 text-muted">
                            <th>#</th>
                            <th>Name</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Supervisors Count</th>
                            <th class="text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($universities as $u)
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td class="fw-semibold">{{ $u->name }}</td>
                            <td class="text-center">
                                <span class="badge {{ $u->status==='active'?'badge-success':'badge-secondary' }}">
                                    {{ $u->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light-primary">{{ $u->supervisors_count }}</span>
                            </td>
                            <td class="text-nowrap">
                                {{-- Edit --}}
                                <button class="btn btn-icon btn-primary btn-sm edit-btn"
                                    data-bs-toggle="modal" data-bs-target="#edit-modal"
                                    data-update-url="{{ route('admin.universities.update',$u) }}"
                                    data-name="{{ $u->name }}" data-status="{{ $u->status }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- Delete --}}
                                <form class="d-inline" method="POST" action="{{ route('admin.universities.destroy',$u) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-icon btn-danger btn-sm"
                                        onclick="return confirm('Delete university? It may be linked to supervisors.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-8">No data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $universities->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- ADD modal --}}
<div class="modal fade" id="add-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.universities.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add University</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="active">active</option>
                            <option value="inactive">inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT modal --}}
<div class="modal fade" id="edit-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit_form" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit University</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="active">active</option>
                            <option value="inactive">inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-btn');
        if (!btn) return;
        const form = document.getElementById('edit_form');
        form.action = btn.dataset.updateUrl;
        form.querySelector('[name="name"]').value = btn.dataset.name || '';
        form.querySelector('[name="status"]').value = btn.dataset.status || 'active';
    });
</script>
@endsection