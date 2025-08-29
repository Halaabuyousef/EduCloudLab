@extends('admin.app')

@section('title','EduCloudLab | Dashboard')

@section('content')
<div class="container py-0">

    {{-- Header --}}
    <div class="card border-0 mb-6 shadow-sm">
        <div class="card-body p-6 d-flex align-items-center justify-content-between flex-wrap gap-4">
            <div class="d-flex align-items-center gap-4">
                <div class="symbol symbol-70px">
                    <img src="{{ asset('assets/media/logos/logo.jpg') }}" alt="EduCloudLab" class="rounded-2" style="background:#0b1220;">
                </div>
                <div>
                    <h2 class="mb-1 fw-bold">EduCloudLab Admin Dashboard</h2>
                    <div class="text-muted">Welcome back, {{ auth('admin')->user()->name ?? 'Admin' }} —
                        <span>{{ now()->format('D, d M Y - H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExperimentModal">
                    <i class="fas fa-flask me-2"></i>Add Experiment
                </a>

                <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#add-modal">
                    <i class="fas fa-microchip me-2"></i>Add Device
                </button>

                <a href="{{ route('admin.reservations.index') }}" class="btn btn-light"><i class="fas fa-calendar-alt me-2"></i>Reservations</a>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-4 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['users'] ?? 0 }}</div>
                        <div class="text-muted">Users</div>
                    </div>
                    <i class="fas fa-users fs-2 text-primary"></i>
                </div>
                <a href="{{ route('admin.users.index') }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['supervisors'] ?? 0 }}</div>
                        <div class="text-muted">Supervisors</div>
                    </div>
                    <i class="fas fa-user-tie fs-2 text-primary"></i>
                </div>
                <a href="{{ route('admin.supervisors.index') }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['experiments'] ?? 0 }}</div>
                        <div class="text-muted">Experiments</div>
                    </div>
                    <i class="fas fa-flask fs-2 text-primary"></i>
                </div>
                <a href="{{ route('admin.experiments.index') }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['reservations_active'] ?? 0 }}</div>
                        <div class="text-muted">Active Reservations</div>
                    </div>
                    <i class="fas fa-calendar-check fs-2 text-primary"></i>
                </div>
                <a href="{{ route('admin.reservations.index') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-4 mb-6">
        <div class="col-xl-5 ">
            <div class="card shadow-sm h-100">
                <div class="card-header border-0">
                    <h5 class="mb-0 py-5">Experiments Status</h5>
                </div>
                <div class="card-body"><canvas id="expStatusChart" height="240"></canvas></div>
            </div>
        </div>
        <div class="col-xl-7">
            <div class="card shadow-sm h-100">
                <div class="card-header border-0">
                    <h5 class="mb-0 py-5">Reservations (Last 7 days)</h5>
                </div>
                <div class="card-body"><canvas id="reservationsTrendChart" height="240"></canvas></div>
            </div>
        </div>
    </div>

    {{-- Recent Activity + Health --}}
    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card shadow-sm h-100">
                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Reservations</h5>
                    <a href="{{ route('admin.reservations.index') }}" class="btn btn-sm btn-light">View all</a>
                </div>
                <div class="card-body p-5">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Experiment</th>
                                    <th>User</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentReservations as $r)
                                <tr>
                                    <td>{{ $r->id }}</td>
                                    <td>{{ $r->experiment->title ?? '-' }}</td>
                                    <td>{{ $r->user->name ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($r->start_time)->format('Y-m-d H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($r->end_time)->format('Y-m-d H:i') }}</td>

                                    {{-- Status --}}
                                    <td>
                                        @php $st = $r->status; @endphp
                                        <span class="badge
                            {{ $st === 'active'     ? 'bg-success'   :
                               ($st === 'postponed' ? 'bg-warning text-dark' :
                               ($st === 'completed' ? 'bg-primary'   :
                               ($st === 'pending'   ? 'bg-secondary' : 'bg-danger'))) }}">
                                            {{ ucfirst($st) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">No recent reservations</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card shadow-sm h-100">
                <div class="card-header border-0">
                    <h5 class="py-9">System Health</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Devices under maintenance
                            <span class="badge bg-warning text-dark">{{ $stats['devices_maintenance'] ?? 0 }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Experiments in use
                            <span class="badge bg-info text-dark">{{ $stats['experiments_in_use'] ?? 0 }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Available experiments
                            <span class="badge bg-success">{{ $stats['experiments_available'] ?? 0 }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    {{-- ADD Experiment Modal --}}
    <div class="modal fade" id="addExperimentModal" tabindex="-1" aria-labelledby="addExperimentLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.experiments.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addExperimentLabel">Add Experiment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="available">Available</option>
                                <option value="in_use">In Use</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Experiment</button>
                    </div>
                </form>
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
                        <select name="experiment_ids[]" class="form-select" multiple>
                            @foreach($experiments as $exp)
                            <option value="{{ $exp->id }}"
                                {{ isset($device) && $device->experiments->contains($exp->id) ? 'selected' : '' }}>
                                {{ $exp->title }}
                            </option>
                            @endforeach
                        </select>
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
</div>


@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ===== Experiments Status (Pie) =====
    const expStatusCtx = document.getElementById('expStatusChart').getContext('2d');
    const expStatus = JSON.parse(`{!! json_encode($expStatus ?? [
        "available"   => 0,
        "reserved"    => 0,
        "in_use"      => 0,
        "maintenance" => 0,
    ]) !!}`);

    new Chart(expStatusCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(expStatus).map(s => s.replace('_', ' ').toUpperCase()),
            datasets: [{
                data: Object.values(expStatus),
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // ===== Reservations Trend (Line) =====
    const resTrendCtx = document.getElementById('reservationsTrendChart').getContext('2d');
    const resTrend = JSON.parse(`{!! json_encode($reservationsTrend ?? [
        "labels" => [],
        "counts" => []
    ]) !!}`);

    new Chart(resTrendCtx, {
        type: 'line',
        data: {
            labels: resTrend.labels,
            datasets: [{
                label: 'Reservations',
                data: resTrend.counts,
                fill: false,
                tension: .35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
</script>

@endsection