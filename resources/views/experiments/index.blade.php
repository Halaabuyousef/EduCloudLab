@extends('admin.app')

@section('title','Experiments')

@section('content')
<div class="container py-6">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h2 class="mb-0">All Experiments</h2>
        <div class="d-flex gap-3">
            <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#add-modal">
                <i class="fas fa-plus me-2"></i>Add Experiment
            </button>
            <a href="{{ route('admin.experiments.trash') }}" class="btn btn-light-danger">
                <i class="fas fa-trash me-2"></i>Trashed Experiments
            </a>
        </div>
    </div>
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

    <div class="card">
        <div class="card-body">
            <table id="kt_datatable_example_1" class="table table-row-bordered gy-5 align-middle">
                <thead>
                    <tr class="fw-bold fs-6 text-muted">
                        <th>ID</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($experiments as $row)
                    <tr>
                        <td>{{ $row->id }}</td>
                        <td>
                            @if($row->image)
                            <img src="{{ asset('images/'.$row->image) }}" width="50" height="50" class="rounded" alt="">
                            @endif
                        </td>
                        <td>{{ $row->title }}</td>
                        <td>{{ $row->description }}</td>
                        <td>
                            <span class="badge
                {{ $row->status==='available'?'badge-success':($row->status==='in_use'?'badge-warning':'badge-secondary') }}">
                                {{ $row->status }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            {{-- EDIT button: fills modal via data-* --}}
                            <button type="button"
                                class="btn btn-icon btn-primary btn-sm edit-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#edit-modal"
                                data-update-url="{{ route('admin.experiments.update',$row->id) }}"
                                data-title="{{ $row->title }}"
                                data-description="{{ $row->description }}"
                                data-device_id="{{ $row->device_id }}"
                                data-status="{{ $row->status }}"
                                data-image="{{ $row->image ? asset('images/'.$row->image) : '' }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- DELETE --}}
                            <form class="d-inline" method="POST" action="{{ route('admin.experiments.destroy',$row->id) }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-icon btn-danger btn-sm" onclick="return confirm('Delete?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- If you want pagination from $experiments --}}
            <div class="mt-4">
                {{ $experiments->links() }}
            </div>
        </div>
    </div>
</div>

{{-- ADD modal --}}
<div class="modal fade" id="add-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="add_form" action="{{ route('admin.experiments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Experiment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control">
                    </div>



                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="available">available</option>
                            <option value="in_use">in_use</option>
                            <option value="maintenance">maintenance</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT modal (no $experiment here) --}}
<div class="modal fade" id="edit-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit_form" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Experiment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Devices</label>
                        <select name="device_ids[]" class="form-select" multiple>
                            @foreach($devices as $dev)
                            <option value="{{ $dev->id }}"
                                {{ isset($experiment) && $experiment->devices->contains($dev->id) ? 'selected' : '' }}>
                                {{ $dev->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="available">available</option>
                            <option value="in_use">in_use</option>
                            <option value="maintenance">maintenance</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Image (optional)</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <img id="edit_preview" src="" style="display:none;width:60px;height:60px;border-radius:6px;">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
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
@endsection