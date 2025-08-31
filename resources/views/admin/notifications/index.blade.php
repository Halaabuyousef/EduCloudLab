@extends('admin.app')
@section('title','Notifications')
@section('css')
<style>
    .bullet {
        display: inline-block;
        width: .5rem;
        height: .5rem;
        border-radius: 50%;
    }

    .fs-8 {
        font-size: .78rem;
    }

    .fs-7 {
        font-size: .9rem;
    }
</style>
@endsection
@section('content')
<div class="container-xxl py-6">

    {{-- page header --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="mb-0">Notifications</h2>
        <form method="POST" action="{{ route('admin.notifications.read_all') }}">
            @csrf
            <button class="btn btn-light-primary">
                <i class="fas fa-check-double me-2"></i> Mark All as Read
            </button>
        </form>
    </div>

    {{-- filter pills --}}
    <div class="d-flex gap-2 flex-wrap mb-4">
        <a class="btn btn-outline-secondary @if($filter==='all') active @endif"
            href="{{ route('admin.notifications.index',['filter'=>'all']) }}">All</a>

        <a class="btn btn-outline-secondary @if($filter==='unread') active @endif"
            href="{{ route('admin.notifications.index',['filter'=>'unread']) }}">
            Unread <span class="badge bg-primary ms-1">{{ $unreadCount }}</span>
        </a>

        <a class="btn btn-outline-secondary @if($filter==='read') active @endif"
            href="{{ route('admin.notifications.index',['filter'=>'read']) }}">Read</a>
    </div>

    <div class="card">
        <div class="card-body p-0">

            @forelse($notifications as $n)
            @php
            $unread = is_null($n->read_at);
            $title = data_get($n->data,'title','Notification');
            $body = data_get($n->data,'body','');
            $url = data_get($n->data,'url');
            @endphp

            <div class="px-4 py-4 border-bottom @if($unread) bg-light @endif">
                <div class="d-flex align-items-start gap-3">
                    {{-- left: status dot --}}
                    <div class="pt-1">
                        <span class="bullet bullet-dot @if($unread) bg-primary @else bg-secondary @endif"></span>
                    </div>

                    {{-- middle: content --}}
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="pe-3">
                                <div class="fw-semibold">{{ $title }}</div>
                                @if($body)<div class="text-muted fs-7 mt-1">{{ $body }}</div>@endif
                                <div class="text-secondary fs-8 mt-1">{{ $n->created_at->format('Y-m-d H:i') }}</div>
                                @if($url)
                                <a href="{{ $url }}" class="fs-8 fw-semibold mt-2 d-inline-block">Open link</a>
                                @endif
                            </div>

                            {{-- right: action(s) --}}
                            <div class="d-flex gap-2">
                                @if($unread)
                                <form method="POST" action="{{ route('admin.notifications.read',$n->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-light-primary">
                                        <i class="fas fa-check me-1"></i> Mark as Read
                                    </button>
                                </form>
                                @else
                                <span class="badge bg-secondary align-self-start">Read</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="py-6 text-center text-muted">No notifications found</div>
            @endforelse

        </div>
    </div>

    <div class="mt-3">
        {{ $notifications->withQueryString()->links() }}
    </div>

</div>
@endsection