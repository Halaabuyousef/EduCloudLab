<div style="max-height: 360px; overflow:auto;">
    @forelse($notifications as $n)
    @php
    $isUnread = is_null($n->read_at);
    $title = data_get($n->data, 'title', 'Notification');
    $body = data_get($n->data, 'body', '');
    $when = $n->created_at->diffForHumans();
    $id = $n->id;
    @endphp
    <div class="p-3 border-bottom @if($isUnread) bg-light @endif">
        <div class="d-flex justify-content-between align-items-start">
            <div class="me-2">
                <div class="fw-semibold">{{ $title }}</div>
                @if($body)<div class="text-muted small">{{ $body }}</div>@endif
                <div class="text-secondary small mt-1">{{ $when }}</div>
            </div>
            <form method="POST" action="{{ route('admin.notifications.read',$id) }}">
                @csrf
                <button class="btn btn-sm btn-outline-secondary">مقروء</button>
            </form>
        </div>
    </div>
    @empty
    <div class="p-3 text-center text-muted">لا توجد إشعارات</div>
    @endforelse
</div>

<a class="dropdown-item text-center py-2" href="{{ route('admin.notifications.index') }}">
    عرض الكل
</a>