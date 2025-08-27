<div class="table-responsive">
    <table class="table table-row-dashed table-striped align-middle mb-0">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Experiment</th>
                <th>User</th>

                @if(!empty($showSupervisor))
                <th>Supervisor</th>
                <th>Assigned?</th>
                @endif

                <th>Start</th>
                <th>End</th>
                <th>Status</th>
                <th class="text-nowrap">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($rows as $r)
            <tr>
                {{-- ID --}}
                <td>{{ $r->id }}</td>

                {{-- Experiment --}}
                <td>{{ $r->experiment?->title ?? '—' }}</td>

                {{-- User --}}
                <td>
                    {{ $r->user?->name ?? '—' }}
                    <small class="text-muted">({{ $r->user?->email }})</small>
                </td>

                {{-- Supervisor --}}
                @if(!empty($showSupervisor))
                <td>{{ $r->user?->supervisor?->name ?? '—' }}</td>
                <td>
                    @if($r->user?->supervisor_id)
                    <span class="badge bg-info text-dark">Yes</span>
                    @else
                    <span class="badge bg-secondary">No</span>
                    @endif
                </td>
                @endif

                {{-- Start / End --}}
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

                {{-- Actions --}}
                <td class="text-nowrap">
                    {{-- Edit --}}
                    <button class="btn btn-icon btn-primary btn-sm edit-resv"
                        data-bs-toggle="modal"
                        data-bs-target="#edit-modal"
                        data-update-url="{{ route('admin.reservations.update',$r) }}"
                        data-experiment_id="{{ $r->experiment_id }}"
                        data-user_id="{{ $r->user_id }}"
                        data-start_time="{{ $r->start_time }}"
                        data-end_time="{{ $r->end_time }}"
                        data-status="{{ $r->status }}"
                        data-notes="{{ $r->notes }}">
                        <i class="fas fa-edit"></i>
                    </button>

                    {{-- Postpone --}}
                    @if(!isset($finishedTable))
                    <button class="btn btn-icon btn-light-warning btn-sm postpone-resv"
                        data-bs-toggle="modal"
                        data-bs-target="#postpone-modal"
                        data-postpone-url="{{ route('admin.reservations.postpone',$r) }}">
                        <i class="fas fa-clock"></i>
                    </button>
                    @endif

                    {{-- Complete --}}
                    @if($r->status !== 'completed')
                    <form class="d-inline" method="POST" action="{{ route('admin.reservations.updateStatus',$r) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button class="btn btn-icon btn-light-success btn-sm" title="Mark as completed">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    @endif

                    {{-- Cancel --}}
                    @if($r->status !== 'cancelled')
                    <form class="d-inline" method="POST"
                        action="{{ route('admin.reservations.updateStatus',$r) }}"
                        onsubmit="return confirm('Cancel this reservation?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button class="btn btn-icon btn-light-danger btn-sm" title="Cancel">
                            <i class="fas fa-ban"></i>
                        </button>
                    </form>
                    @endif

                    {{-- Delete --}}
                    <form class="d-inline" method="POST"
                        action="{{ route('admin.reservations.destroy',$r) }}"
                        onsubmit="return confirm('Delete permanently?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-icon btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ !empty($showSupervisor) ? 9 : 7 }}" class="text-center py-8">
                    No data
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>