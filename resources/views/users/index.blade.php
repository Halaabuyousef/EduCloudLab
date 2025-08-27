@extends('admin.app')

@section('title','Users')

@section('css')
<style>
    .modal .btn {
        min-width: 100px;
    }

    /* Make table cells vertically centered */
    .table th,
    .table td {
        vertical-align: middle;
    }

    .min-w-60px {
        min-width: 60px
    }

    .min-w-120px {
        min-width: 120px
    }

    .min-w-200px {
        min-width: 200px
    }

    .min-w-220px {
        min-width: 220px
    }

    .min-w-240px {
        min-width: 240px
    }

    .min-w-420px {
        min-width: 420px
    }
</style>
@endsection

@section('content')

@if (session('msg'))
<div id="alertBox"
    class="alert alert-{{ session('type') }} alert-dismissible fade show"
    role="alert"
    style="background-color:#dcd0f7;color:#000;">
    {{ session('msg') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<script>
    setTimeout(function() {
        let el = document.getElementById('alertBox');
        if (el) new bootstrap.Alert(el).close();
    }, 2000);
</script>
@endif

<div class="card mb-8">
    <div class="card-header align-items-center">
        <h3 class="card-title fw-bold">Users</h3>

        <div class="card-toolbar">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-auto">
                    <input name="q" value="{{ $q }}" class="form-control form-control-solid" placeholder="Search by name/email">
                </div>
                <div class="col-auto">
                    <select name="filter" class="form-select form-select-solid" onchange="this.form.submit()">
                        <option value="" {{ $filter==''?'selected':'' }}>All</option>
                        <option value="attached" {{ $filter=='attached'?'selected':'' }}>Attached to Supervisor</option>
                        <option value="independent" {{ $filter=='independent'?'selected':'' }}>Independent</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" type="submit">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card-body py-0">
        <div class="table-responsive">
            <table class="table table-row-dashed align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="text-center">Type</th>
                        <th>Supervisor</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td>{{ $u->id }}</td>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td class="text-center">
                            @if($u->supervisor_id)
                            <span class="badge badge-light-info">Attached</span>
                            @else
                            <span class="badge badge-light-secondary">Independent</span>
                            @endif
                        </td>
                        <td>
                            @if($u->supervisor_id)
                            <a href="{{ route('admin.supervisors.show', $u->supervisor_id) }}">{{ $u->supervisor?->name }}</a>
                            @else
                            —
                            @endif
                        </td>
                        <td class="text-center">
                            @if($u->supervisor_id)
                            <div class="btn-group">
                                <button class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#attach-{{ $u->id }}">
                                    Change Supervisor
                                </button>
                                <form method="POST" action="{{ route('admin.users.detachSupervisor', $u) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-light-danger">Detach</button>
                                </form>
                            </div>
                            @else
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#attach-{{ $u->id }}">
                                Assign Supervisor
                            </button>
                            @endif
                        </td>
                    </tr>

                    {{-- Supervisor selection modal (per user as in your original) --}}
                    <div class="modal fade" id="attach-{{ $u->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-md modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admin.users.attachSupervisor', $u) }}">
                                    @csrf @method('PATCH')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Assign Supervisor to {{ $u->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="form-label">Select supervisor</label>
                                        <select name="supervisor_id" class="form-select w-100" required>
                                            <option value="">— Select —</option>
                                            @foreach($supervisors as $s)
                                            <option value="{{ $s->id }}" {{ $u->supervisor_id===$s->id ? 'selected' : '' }}>
                                                {{ $s->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
                                        <button class="btn btn-primary" type="submit">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10">No data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@section('js')
{{-- No extra JS needed here --}}
@endsection