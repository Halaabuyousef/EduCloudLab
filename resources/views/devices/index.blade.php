@extends('admin.app')

@section('title','Devices')

@section('content')
<div class="container py-6">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h2 class="mb-0">All Devices</h2>
        <div class="d-flex gap-3">
            <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#add-modal">
                <i class="fas fa-plus me-2"></i>Add Device
            </button>
            <a href="{{ route('admin.devices.trash') }}" class="btn btn-light-danger">
                <i class="fas fa-trash me-2"></i>Trashed Devices
            </a>
        </div>
    </div>

    {{-- Session Message --}}
    @if (session('msg'))
    <div id="alertBox" class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert"
        style="background-color: #dcd0f7; color: #000;">
        {{ session('msg') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <script>
        setTimeout(function() {
            let alertEl = document.getElementById('alertBox');
            if (alertEl) {
                let alert = new bootstrap.Alert(alertEl);
                alert.close();
            }
        }, 3000);
    </script>
    @endif

    {{-- Devices Table --}}
    <div class="card">
        <div class="card-body">
            <table id="kt_datatable_example_1" class="table table-row-bordered gy-5 align-middle">
                <thead>
                    <tr class="fw-bold fs-6 text-muted">
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Experiment</th>
                        <th>Status</th>
                        <th>Last Update</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($devices as $device)
                    <tr>
                        <td>{{ $device->id }}</td>
                        <td>
                            @if($device->image)
                            <img src="{{ asset('images/devices/'.$device->image) }}" width="50" height="50" class="rounded" alt="">
                            @endif
                        </td>
                        <td>{{ $device->name }}</td>
                        <td>{{ $device->type }}</td>
                        <td>{{ $device->experiment ? $device->experiment->title : '-' }}</td>
                        <td>
                            <span class="badge {{ $device->status==='online'?'badge-success':'badge-secondary' }}">
                                {{ $device->status }}
                            </span>
                        </td>
                        <td>{{ $device->last_update ? $device->last_update->format('Y-m-d H:i') : '-' }}</td>
                        <td class="text-nowrap">
                            {{-- Edit Button --}}
                            <button type="button" class="btn btn-icon btn-primary btn-sm edit-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#edit-modal"
                                data-update-url="{{ route('admin.devices.update',$device->id) }}"
                                data-name="{{ $device->name }}"
                                data-type="{{ $device->type }}"
                                data-status="{{ $device->status }}"
                                data-experiment_id="{{ $device->experiment_id }}"
                                data-image="{{ $device->image ? asset('images/devices/'.$device->image) : '' }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- Delete --}}
                            <form class="d-inline" method="POST" action="{{ route('admin.devices.destroy',$device->id) }}">
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

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $devices->links() }}
            </div>
        </div>
    </div>
</div>

{{-- ADD Modal --}}
<div class="modal fade" id="add-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="add_form" action="{{ route('admin.devices.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <input type="text" name="type" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="online">online</option>
                            <option value="offline">offline</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Experiment</label>
                        <select name="experiment_id" class="form-select">
                            <option value="">--Select Experiment--</option>
                            @foreach($experiments as $exp)
                            <option value="{{ $exp->id }}">{{ $exp->title }}</option>
                            @endforeach
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

{{-- EDIT Modal --}}
<div class="modal fade" id="edit-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit_form" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <input type="text" name="type" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="online">online</option>
                            <option value="offline">offline</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Experiment</label>
                        <select name="experiment_id" class="form-select">
                            <option value="">--Select Experiment--</option>
                            @foreach($experiments as $exp)
                            <option value="{{ $exp->id }}">{{ $exp->title }}</option>
                            @endforeach
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
    // Fill EDIT modal
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-btn');
        if (!btn) return;

        const form = document.getElementById('edit_form');
        form.action = btn.dataset.updateUrl;

        form.querySelector('input[name="name"]').value = btn.dataset.name || '';
        form.querySelector('input[name="type"]').value = btn.dataset.type || '';
        form.querySelector('select[name="status"]').value = btn.dataset.status || 'offline';
        form.querySelector('select[name="experiment_id"]').value = btn.dataset.experiment_id || '';

        const preview = document.getElementById('edit_preview');
        if (btn.dataset.image) {
            preview.src = btn.dataset.image;
            preview.style.display = 'inline-block';
        } else {
            preview.removeAttribute('src');
            preview.style.display = 'none';
        }

        form.querySelector('input[name="image"]').value = '';
    });

    // Reset EDIT modal on close
    document.getElementById('edit-modal').addEventListener('hidden.bs.modal', function() {
        const f = document.getElementById('edit_form');
        f.reset();
        const p = document.getElementById('edit_preview');
        p.removeAttribute('src');
        p.style.display = 'none';
    });

    // DataTables
    document.addEventListener('DOMContentLoaded', function() {
        $('#kt_datatable_example_1').DataTable({
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