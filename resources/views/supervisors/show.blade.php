@extends('admin.app')
@section('title','Supervisor Details')
@section('css')

<style>
    /* Card look */
    .hero-card {
        --hero-start: #300beb43;
        --hero-end: #65c3ecce;
        background: linear-gradient(135deg, var(--hero-start) 0%, var(--hero-end) 100%);
        border-radius: 16px;
        overflow: hidden;
    }

    /* Avatar */
    .hero-avatar {
        width: 80px;
        height: 80px;
        flex: 0 0 80px;
    }

    .hero-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Initials bubble when no photo */
    .hero-initials {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .14);
        color: #fff;
        font-weight: 700;
        font-size: 1.25rem;
    }

    /* Bio: لا تتمدد وتكسر الكلمات الطويلة */
    .hero-bio {
        max-width: 100%;
        word-break: break-word;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* عدد السطور المسموح بها */
        -webkit-box-orient: vertical;
    }

    /* Actions on mobile: كل زر بسطر */
    @media (max-width: 991.98px) {
        .hero-card .btn {
            width: 100%;
        }
    }
</style>

@endsection
@section('content')
<div class="container-xxl py-2">

    {{-- ============ Hero Header ============ --}}
    @php
    use Illuminate\Support\Str;
    $img = $supervisor->image ? Storage::url($supervisor->image) : null;
    @endphp

    <div class="card hero-card border-0 mb-6 shadow-sm">
        <div class="card-body p-5 p-lg-8">
            <div class="row g-4 align-items-center">
                {{-- Left: avatar + info --}}
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-4">
                        <div class="hero-avatar">
                            @if($img)
                            <img src="{{ $img }}" class="rounded-circle" alt="Supervisor photo">
                            @else
                            <div class="hero-initials rounded-circle">
                                {{ Str::upper(Str::substr($supervisor->name,0,2)) }}
                            </div>
                            @endif
                        </div>

                        <div class="text-white">
                            <h2 class="mb-1 fw-bold">{{ $supervisor->name }}</h2>

                            <div class="small opacity-75 d-flex flex-wrap gap-2">
                                <span>{{ $supervisor->email }}</span>
                                <span class="d-none d-md-inline mx-2">•</span>
                                <span>
                                    {{ $supervisor->phone ?? '—' }}
                                    @if($supervisor->country) &nbsp;•&nbsp; {{ $supervisor->country }} @endif
                                </span>
                            </div>

                            @if($supervisor->university)
                            <span class="badge bg-white text-dark mt-2">
                                {{ $supervisor->university->name }}
                            </span>
                            @endif
                        </div>
                    </div>

                    @if($supervisor->bio)
                    <p class="hero-bio text-white-75 mt-3 mb-0">
                        {{ $supervisor->bio }}
                    </p>
                    @endif
                </div>

                {{-- Right: actions --}}
                <div class="col-lg-4 text-lg-end">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        <a href="{{ route('admin.supervisors.index') }}" class="btn btn-light">Back</a>

                        <button class="btn btn-light-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#editSupervisorModal">
                            Edit Details
                        </button>

                        <form method="POST"
                            action="{{ route('admin.supervisors.destroy',$supervisor) }}"
                            onsubmit="return confirm('Delete this supervisor permanently?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-light-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



    @if(session('msg'))
    <div id="alertBox" class="alert alert-{{ session('type') }} alert-dismissible fade show shadow-sm" role="alert">
        {{ session('msg') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger shadow-sm">
        <ul class="mb-0 ps-4">
            @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    <div class="row g-4 mb-6">
        <div class="col-sm-6 col-lg-4">
            <div class="card card-flush h-100 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted">Members</div>
                        <div class="fs-2hx fw-bold">{{ $members->total() }}</div>
                    </div>
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-light-primary">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add member --}}
    <div class="card mb-6 border-0 shadow-sm">
        <div class="card-header">
            <h3 class="card-title fw-bold">Add Member</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.supervisors.attach',$supervisor) }}" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-8">
                    <label class="form-label">Choose an independent user</label>
                    <select id="member_id" name="user_id" class="form-select"
                        data-control="select2" data-placeholder="Search by name/email" required>
                        <option value=""></option>
                        @foreach($independents as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary w-100">Add</button>
                </div>
            </form>
        </div>
    </div>
  
    {{-- Members list --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h3 class="card-title fw-bold">Members</h3>
        </div>
        <div class="card-body p-0">
            @if($members->count())
            <div class="list-group list-group-flush">
                @foreach($members as $m)
                <div class="list-group-item py-4 px-6 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-4">
                        <div class="symbol symbol-45px">
                            <div class="symbol-label bg-light text-gray-700 fw-bold rounded-circle">
                                {{ Str::upper(Str::substr($m->name,0,1)) }}
                            </div>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $m->name }}</div>
                            <div class="text-muted fs-7">{{ $m->email }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.supervisors.detach',[$supervisor,$m]) }}"
                        onsubmit="return confirm('Remove {{ $m->name }} from this supervisor?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-light-danger">
                            <i class="fas fa-user-minus me-1"></i> Remove
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-6 text-center text-muted">No members yet.</div>
            @endif
        </div>
        <div class="card-footer">
            {{ $members->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>

{{-- ========= Modal: Edit supervisor ========= --}}
<div class="modal fade" id="editSupervisorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.supervisors.update',$supervisor) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Supervisor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input name="name" class="form-control" value="{{ $supervisor->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input name="email" type="email" class="form-control" value="{{ $supervisor->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password (leave empty to keep)</label>
                            <input name="password" type="password" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input name="phone" class="form-control" value="{{ $supervisor->phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <input name="country" class="form-control" value="{{ $supervisor->country }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">University</label>
                            <select name="university_id" class="form-select">
                                <option value="">— Select University —</option>
                                @foreach(\App\Models\University::orderBy('name')->get(['id','name']) as $uni)
                                <option value="{{ $uni->id }}" @selected($supervisor->university_id==$uni->id)>
                                    {{ $uni->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" rows="3" class="form-control">{{ $supervisor->bio }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Image (optional)</label>
                            <input type="file" name="image" class="form-control">
                            @if($img)
                            <img src="{{ $img }}" class="mt-2 rounded" style="width:60px;height:60px;">
                            @endif
                        </div>
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
{{-- Select2 (search inside select) --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('member_id');
        if (el) {
            $(el).select2({
                width: '100%',
                placeholder: $(el).data('placeholder') || 'Search...',
            });
        }

        // Auto-close flash
        setTimeout(() => {
            const a = document.getElementById('alertBox');
            if (a) new bootstrap.Alert(a).close();
        }, 1800);
    });
</script>

{{-- Light tweaks --}}
<style>
    .symbol-label {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .list-group-item {
        transition: .15s ease;
    }

    .list-group-item:hover {
        background: #fafafa;
    }
</style>
@endsection