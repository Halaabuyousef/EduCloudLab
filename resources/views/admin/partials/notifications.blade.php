@php
$badge = $adminUnreadCount ?? 0;
$items = $adminLatestNotifications ?? collect();
@endphp

<li class="nav-item dropdown">
    <a class="nav-link position-relative" data-bs-toggle="dropdown" href="#" aria-expanded="false">
        <i class="fas fa-bell"></i>
        @if($badge > 0)
        <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            {{ $badge }}
        </span>
        @else
        <span id="notif-badge" class="d-none"></span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 360px;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <strong>الإشعارات</strong>
            <form method="POST" action="{{ route('admin.notifications.read_all') }}">
                @csrf
                <button class="btn btn-sm btn-light">تعليم الكل كمقروء</button>
            </form>
        </div>

        <div style="max-height: 360px; overflow:auto;">
            @forelse($items as $n)
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

        <a class="dropdown-item text-center py-2" href="{{ route('admin.notifications.index') }}">عرض الكل</a>
    </div>
</li>

{{-- Polling خفيف لتحديث العداد --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const badgeEl = document.getElementById('notif-badge');
        const url = "{{ route('admin.notifications.badge') }}";

        async function refreshBadge() {
            try {
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                const c = Number(data.count || 0);
                if (!badgeEl) return;
                if (c > 0) {
                    badgeEl.classList.remove('d-none');
                    badgeEl.textContent = c;
                } else {
                    badgeEl.classList.add('d-none');
                }
            } catch (e) {
                /* ignore */ }
        }

        // حدّث كل 30 ثانية
        setInterval(refreshBadge, 30000);
    });
</script>