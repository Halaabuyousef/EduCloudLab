<table class="table align-middle mb-0">
    <thead>
        <tr>
            <th>#</th>
            <th>Experiment</th>
            <th>User</th>
            <th>From → To</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rows as $reservation)
        @php
        $rs = $reservation->runtime_status; // accessor من الموديل
        $badge = match($rs) {
        'active' => 'badge bg-success-subtle text-success',
        'pending' => 'badge bg-warning-subtle text-warning',
        'completed' => 'badge bg-primary-subtle text-primary',
        'cancelled' => 'badge bg-danger-subtle text-danger',
        'postponed' => 'badge bg-secondary-subtle text-secondary',
        default => 'badge bg-light text-muted',
        };
        @endphp
        <tr>
            <td>#{{ $reservation->id }}</td>
            <td>{{ $reservation->experiment->title ?? '-' }}</td>
            <td>{{ $reservation->user->name ?? '-' }}</td>
            <td>
                {{ optional($reservation->start_time)->format('Y-m-d H:i') }} →
                {{ optional($reservation->end_time)->format('Y-m-d H:i') }}
            </td>
            <td><span class="{{ $badge }}">{{ $rs }}</span></td>
            <td class="text-end">
                <a href="{{ route('admin.reservations.show', $reservation) }}" class="btn btn-sm btn-light">
                    View
                </a>

                {{-- Edit --}}
                <button type="button"
                    class="btn btn-sm btn-primary edit-resv"
                    data-bs-toggle="modal" data-bs-target="#edit-modal"
                    data-update-url="{{ route('admin.reservations.update', $reservation) }}"
                    data-experiment_id="{{ $reservation->experiment_id }}"
                    data-user_id="{{ $reservation->user_id }}"
                    data-start_time="{{ optional($reservation->start_time)->format('Y-m-d H:i:s') }}"
                    data-end_time="{{ optional($reservation->end_time)->format('Y-m-d H:i:s') }}"
                    data-notes="{{ $reservation->notes }}">
                    Edit
                </button>

                {{-- Postpone --}}
                <button type="button"
                    class="btn btn-sm btn-warning postpone-resv"
                    data-bs-toggle="modal" data-bs-target="#postpone-modal"
                    data-postpone-url="{{ route('admin.reservations.postpone', $reservation) }}">
                    Postpone
                </button>

                {{-- Delete --}}
                <form action="{{ route('admin.reservations.destroy', $reservation) }}"
                    method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-light-danger"
                        onclick="return confirm('Delete reservation #{{ $reservation->id }}?')">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-5">No rows</td>
        </tr>
        @endforelse
    </tbody>
</table>