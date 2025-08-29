@extends('admin.app')
@section('title','Reservations')

@section('content')
<div class="container-xxl py-6">
  
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h2 class="mb-0">Reservations</h2>
        <div class="d-flex gap-3">
            <form method="GET" class="d-flex gap-2">
                <input name="q" value="{{ $q }}" class="form-control form-control-solid" placeholder="Search: user / experiment">
                <button class="btn btn-light">Search</button>
            </form>
            <button class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#add-modal">
                <i class="fas fa-plus me-2"></i>Add Reservation
            </button>
        </div>
    </div>

    @if(session('msg'))
    <div id="alertBox" class="alert alert-{{ session('type') }} alert-dismissible fade show">
        {{ session('msg') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        setTimeout(() => {
            let el = document.getElementById('alertBox');
            if (el) new bootstrap.Alert(el).close();
        }, 1800);
    </script>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 ps-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Table 1: Upcoming (active) --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="card-title fw-bold">Upcoming Reservations</h3>
        </div>
        <div class="card-body p-0">
            @include('reservations._table', ['rows'=>$current, 'showSupervisor'=>true])
        </div>
        <div class="card-footer">{{ $current->links('pagination::bootstrap-5') }}</div>
    </div>

    {{-- Table 2: Booked (pending / postponed) --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="card-title fw-bold">Booked Reservations</h3>
        </div>
        <div class="card-body p-0">
            @include('reservations._table', ['rows'=>$booked, 'showSupervisor'=>true])
        </div>
        <div class="card-footer">{{ $booked->links('pagination::bootstrap-5') }}</div>
    </div>

    {{-- Table 3: Postponed --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="card-title fw-bold">Postponed Reservations</h3>
        </div>
        <div class="card-body p-0">
            @include('reservations._table', ['rows'=>$postponed, 'showSupervisor'=>true])
        </div>
        <div class="card-footer">{{ $postponed->links('pagination::bootstrap-5') }}</div>
    </div>

    {{-- Table 4: Completed / Cancelled --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title fw-bold">Completed / Cancelled</h3>
        </div>
        <div class="card-body p-0">
            @include('reservations._table', ['rows'=>$finished, 'showSupervisor'=>true, 'finishedTable'=>true])
        </div>
        <div class="card-footer">{{ $finished->links('pagination::bootstrap-5') }}</div>
    </div>
</div>

{{-- Modal: Add --}}
<div class="modal fade" id="add-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.reservations.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('reservations._form', ['isEdit'=>false])
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Edit --}}
<div class="modal fade" id="edit-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="edit_form" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('reservations._form', ['isEdit'=>true])
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Postpone --}}
<div class="modal fade" id="postpone-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="postpone_form" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Postpone Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">New start</label>
                        <input type="datetime-local" name="new_start_at" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New end</label>
                        <input type="datetime-local" name="new_end_at" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason (optional)</label>
                        <textarea name="reason" rows="3" class="form-control" placeholder="Reason for postponing"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Postpone</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Fill Edit modal (fixed data-* attributes + names)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-resv');
        if (!btn) return;

        const form = document.getElementById('edit_form');
        form.action = btn.dataset.updateUrl;

        const map = {
            'experiment_id': 'experiment_id',
            'user_id': 'user_id',
            'start_time': 'start_time',
            'end_time': 'end_time',
          
            'notes': 'notes'
        };

        Object.entries(map).forEach(([dataKey, inputName]) => {
            const el = form.querySelector(`[name="${inputName}"]`);
            if (!el) return;

            let val = btn.dataset[dataKey] || '';
            if (dataKey === 'start_time' || dataKey === 'end_time') {
                // normalize to yyyy-mm-ddTHH:MM for datetime-local
                val = val.replace(' ', 'T').slice(0, 16);
            }
            el.value = val;
        });
    });

    // Fill Postpone modal
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.postpone-resv');
        if (!btn) return;
        const form = document.getElementById('postpone_form');
        form.action = btn.dataset.postponeUrl;
        form.reset();
    });
</script>
@endsection