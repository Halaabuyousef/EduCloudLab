@extends('admin.app')
@section('title','Supervisors')

@section('content')
<div class="container py-6">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h2 class="mb-0">Supervisors</h2>
        <div class="d-flex gap-3">
            <form method="GET" class="d-flex gap-2">
                <input name="q" value="{{ $q }}" class="form-control form-control-solid" placeholder="Search name/email">
                <button class="btn btn-light-dark">Search</button>
            </form>

            <form method="GET" class="d-flex gap-2">
                <select name="university_id" class="form-select form-select-solid" onchange="this.form.submit()">
                    <option value="">All Universities</option>
                    @foreach($universities as $uni)
                    <option value="{{ $uni->id }}" {{ request('university_id')==$uni->id?'selected':'' }}>
                        {{ $uni->name }}
                    </option>
                    @endforeach
                </select>
                <input name="q" value="{{ $q }}" class="form-control form-control-solid" placeholder="Search">
                <button class="btn btn-light">Filter</button>
            </form>

            <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#add-modal">
                <i class="fas fa-plus me-2"></i> Add Supervisor
            </button>
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
        }, 1000);
    </script>
    @endif

    <div class="card">
        <div class="card-body p-5">
            <div class="table-responsive">
                <table class="table table-row-bordered align-middle">
                    <thead>
                        <tr class="fw-bold fs-6 text-muted">
                            <th>#</th>
                            {{-- <th>Image</th> --}}
                            <th>Name</th>
                            <th>Email</th>
                            <th>University</th>
                            <th>Phone</th>
                            <th class="text-center">Members</th>
                            <th class="text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supervisors as $s)
                        <tr>
                            <td>{{ $s->id }}</td>

                            {{--
                                <td>
                                    @php $img = $s->image ? Storage::url($s->image) : null; @endphp
                                    @if($img)
                                        <img src="{{ $img }}" width="42" height="42" class="rounded-circle" alt="">
                            @else
                            <div class="symbol symbol-40px"><span class="symbol-label bg-light text-muted">—</span></div>
                            @endif
                            </td>
                            --}}

                            <td class="fw-semibold">{{ $s->name }}</td>
                            <td>{{ $s->email }}</td>
                            <td>{{ $s->university->name ?? '—' }}</td>
                            <td>{{ $s->phone ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge badge-light-primary">{{ $s->users_count }}</span>
                            </td>
                            <td class="text-nowrap">
                                {{-- EDIT --}}
                                <button
                                    type="button"
                                    class="btn btn-icon btn-primary btn-sm edit-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#edit-modal"
                                    data-update-url="{{ route('admin.supervisors.update',$s) }}"
                                    data-name="{{ $s->name }}"
                                    data-email="{{ $s->email }}"
                                    data-phone="{{ $s->phone }}"
                                    data-country="{{ $s->country }}"
                                    data-bio="{{ $s->bio }}"
                                    {{-- NOTE: if you want image preview, make sure $img is defined --}}
                                    data-image="{{ isset($img) ? $img : '' }}"
                                    data-university_id="{{ $s->university_id }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- DELETE --}}
                                <form class="d-inline" method="POST" action="{{ route('admin.supervisors.destroy',$s) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-icon btn-danger btn-sm" onclick="return confirm('Delete supervisor?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                                {{-- Members page (optional) --}}
                                <a class="btn btn-icon btn-light btn-sm" href="{{ route('admin.supervisors.show',$s) }}" title="Members">
                                    <i class="fas fa-users"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-8">No Data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $supervisors->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- ADD modal --}}
<div class="modal fade" id="add-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="add_form" action="{{ route('admin.supervisors.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Supervisor</h5>
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
                        <label class="form-label">University (optional)</label>
                        <select name="university_id" class="form-select">
                            <option value="">— Select University —</option>
                            @foreach(\App\Models\University::orderBy('name')->get(['id','name']) as $uni)
                            <option value="{{ $uni->id }}">{{ $uni->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" rows="3" class="form-control"></textarea>
                    </div>
                    {{-- <div class="mb-3">
                        <label class="form-label">Image (optional)</label>
                        <input type="file" name="image" class="form-control">
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT modal --}}
<div class="modal fade" id="edit-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit_form" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Supervisor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3"><label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3"><label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">University (optional)</label>
                        <select name="university_id" class="form-select">
                            <option value="">— Select University —</option>
                            @foreach(\App\Models\University::orderBy('name')->get(['id','name']) as $uni)
                            <option value="{{ $uni->id }}">{{ $uni->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3"><label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control">
                    </div>
                    <div class="mb-3"><label class="form-label">Bio</label>
                        <textarea name="bio" rows="3" class="form-control"></textarea>
                    </div>
                    {{-- <div class="mb-2">
                        <label class="form-label">Image (optional)</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <img id="edit_preview" src="" class="mt-2" style="display:none;width:60px;height:60px;border-radius:6px;"> --}}
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
    // Fill the edit modal from table buttons
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-btn');
        if (!btn) return;

        const form = document.getElementById('edit_form');
        form.action = btn.dataset.updateUrl;

        form.querySelector('[name="name"]').value = btn.dataset.name || '';
        form.querySelector('[name="email"]').value = btn.dataset.email || '';
        form.querySelector('[name="university_id"]').value = btn.dataset.university_id || '';
        form.querySelector('[name="phone"]').value = btn.dataset.phone || '';
        form.querySelector('[name="country"]').value = btn.dataset.country || '';
        form.querySelector('[name="bio"]').value = btn.dataset.bio || '';
        form.querySelector('[name="password"]').value = '';

        const preview = document.getElementById('edit_preview');
        if (preview) {
            if (btn.dataset.image) {
                preview.src = btn.dataset.image;
                preview.style.display = 'inline-block';
            } else {
                preview.removeAttribute('src');
                preview.style.display = 'none';
            }
        }
        const imgInput = form.querySelector('input[name="image"]');
        if (imgInput) imgInput.value = '';
    });

    // Reset modal on close
    document.getElementById('edit-modal').addEventListener('hidden.bs.modal', function() {
        const f = document.getElementById('edit_form');
        f.reset();
        const p = document.getElementById('edit_preview');
        if (p) {
            p.removeAttribute('src');
            p.style.display = 'none';
        }
    });
</script>
@endsection