<style>
.contacts-dd .list-group-item{ transition: background-color .15s ease; }
.contacts-dd .list-group-item:hover{ background-color:#f8f9fa; }
.contacts-dd .from{ font-weight:600; }
.contacts-dd .subject{ font-size:.9rem; }
.contacts-dd .time{ font-size:.8rem; color:#6c757d; }
.contacts-dd .badge-new{ background:#ffe08a; color:#5c4400; }
</style>

<ul class="list-group list-group-flush contacts-dd" style="min-width: 340px;">
    @forelse($items as $it)
    <li class="list-group-item d-flex justify-content-between align-items-start">
        <div class="me-2 flex-grow-1">
            <div class="from text-truncate">
                <i class="fas fa-user-circle me-1"></i>
                <a href="{{ route('admin.contacts.show',$it) }}" class="text-decoration-none">
                    {{ $it->name }}
                </a>
                <small class="text-muted"> &lt;{{ $it->email }}&gt;</small>
            </div>
            <div class="subject text-muted text-truncate" title="{{ $it->subject ?: '—' }}">
                <i class="fas fa-tag me-1"></i>{{ $it->subject ?: '—' }}
            </div>
            <div class="time">
                <i class="far fa-clock me-1"></i>{{ $it->created_at->diffForHumans() }}
            </div>
        </div>
        <span class="badge badge-new">New</span>
    </li>
    @empty
    <li class="list-group-item text-center text-muted">No new messages</li>
    @endforelse
    <li class="list-group-item text-center">
        <a class="btn btn-sm btn-light-primary" href="{{ route('admin.contacts.index') }}">
            <i class="fas fa-inbox me-1"></i>Open inbox
        </a>
    </li>
</ul>
