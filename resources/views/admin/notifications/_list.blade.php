{{-- Header --}}
<div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
    <h6 class="mb-0 fw-semibold">Notifications</h6>
    <form method="POST" action="{{ route('admin.notifications.read_all') }}">
        @csrf
        <button class="btn btn-sm btn-light-primary">
            <i class="fas fa-check-double me-1"></i> Mark all as read
        </button>
    </form>
</div>

{{-- List --}}
<div class="px-2" style="max-height: 360px; overflow:auto;">
    @forelse($notifications as $n)
    @php
    $unread = is_null($n->read_at);
    $title = data_get($n->data,'title','Notification');
    $body = data_get($n->data,'body','');
    $when = $n->created_at->diffForHumans();
    @endphp

    <div class="d-flex align-items-start gap-3 rounded-3 px-3 py-3 border-bottom @if($unread) bg-light @endif">
        {{-- unread dot / icon --}}
        <div class="pt-1">
            <span class="bullet bullet-dot @if($unread) bg-primary @else bg-secondary @endif"></span>
        </div>

        {{-- content --}}
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-semibold">{{ $title }}</div>
                    @if($body)<div class="text-muted fs-7 mt-1">{{ $body }}</div>@endif
                </div>

                {{-- small action --}}
                <form method="POST" action="{{ route('admin.notifications.read',$n->id) }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary">Mark</button>
                </form>
            </div>
            <div class="text-secondary fs-8 mt-1">{{ $when }}</div>
        </div>
    </div>
    @empty
    <div class="text-center text-muted py-4">No notifications</div>
    @endforelse
</div>

{{-- Footer --}}
<a class="d-block text-center fw-semibold py-3" href="{{ route('admin.notifications.index') }}">
    View all
</a>