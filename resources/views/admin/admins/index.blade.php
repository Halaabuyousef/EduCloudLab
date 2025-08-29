@extends('admin.app')

@section('title','Admins')

@section('content')
<div class="container py-6">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h2 class="mb-0">All Admins</h2>
        <div class="d-flex gap-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-modal">
                <i class="fas fa-plus me-2"></i>Add Admin
            </button>
            <a href="{{ route('admin.admins.trash') }}" class="btn btn-light-danger">
                <i class="fas fa-trash me-2"></i>Trashed Admins
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('msg'))
    <div id="alertBox"
        class="alert alert-{{ session('type') }} alert-dismissible fade show"
        role="alert"
        style="background-color: #dcd0f7; color: #000;">
        {{ session('msg') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        setTimeout(function() {
            let alertEl = document.getElementById('alertBox');
            if (alertEl) {
                // Bootstrap 5 way to close programmatically
                let alert = new bootstrap.Alert(alertEl);
                alert.close();
            }
        }, 2000);
    </script>
    @endif

    {{-- Admins Table --}}
    <div class="card">
        <div class="card-body">
            <table id="kt_datatable_example_1" class="table table-row-bordered gy-5 align-middle">
                <thead>
                    <tr class="fw-bold fs-6 text-muted">
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
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
                        <td>{{ $admin->created_at?->format('Y-m-d') }}</td>
                        <td class="text-nowrap">
                            {{-- Edit --}}
                            <button type="button"
                                class="btn btn-sm btn-warning edit-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#edit-modal"
                                data-update-url="{{ route('admin.admins.update', $admin->id) }}"
                                data-name="{{ $admin->name }}"
                                data-email="{{ $admin->email }}"
                                data-image="{{ $admin->image ? asset('storage/'.$admin->image) : '' }}">
                                Edit
                            </button>


                            {{-- Move to Trash (soft delete) --}}
                            <form action="{{ route('admin.admins.destroy',$admin->id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger delete-btn">
                                    Trash
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- If you want pagination from $experiments --}}
            <div class="mt-4">
                {{ $admins->links() }}
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="edit-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit_form" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $admin->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $admin->email }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password (leave blank to keep old)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Image (optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            @if($admin->image)
                            <img src="{{ asset('storage/'.$admin->image) }}" class="mt-2 rounded-circle" style="width:56px;height:56px;">
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




</div>
</div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="add-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="add_form" action="{{ route('admin.admins.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Fill EDIT modal from button data-*
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-btn');
        if (!btn) return;

        const form = document.getElementById('edit_form');
        form.action = btn.dataset.updateUrl; // set PUT URL

        form.querySelector('input[name="title"]').value = btn.dataset.title || '';
        form.querySelector('input[name="description"]').value = btn.dataset.description || '';
        form.querySelector('input[name="device_id"]').value = btn.dataset.device_id || '';
        form.querySelector('select[name="status"]').value = btn.dataset.status || 'available';

        const preview = document.getElementById('edit_preview');
        if (btn.dataset.image) {
            preview.src = btn.dataset.image;
            preview.style.display = 'inline-block';
        } else {
            preview.removeAttribute('src');
            preview.style.display = 'none';
        }

        form.querySelector('input[name="image"]').value = ''; // clear previous file
    });

    // (Optional) reset on close
    document.getElementById('edit-modal').addEventListener('hidden.bs.modal', function() {
        const f = document.getElementById('edit_form');
        f.reset();
        const p = document.getElementById('edit_preview');
        p.removeAttribute('src');
        p.style.display = 'none';
    });

    // DataTables basic (if you’re using it without Ajax)
    document.addEventListener('DOMContentLoaded', function() {
        const table = $('#kt_datatable_example_1').DataTable({
            responsive: true,
            dom: `<'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
            lengthMenu: [5, 10, 25, 50],
            pageLength: 10,
            order: [
                [0, 'desc']
            ]
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // عند الضغط على زر حذف
        document.querySelectorAll('.delete-btn').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const form = this.closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Move this admin to trash?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, move to trash',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
 
@endsection