@extends('admin.app')
@section('title','Notifications')

@section('content')
<div class="container py-6">

    @if(session('msg'))
    <div class="alert alert-{{ session('type','info') }} alert-dismissible fade show">
        {{ session('msg') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">الإشعارات</h2>
        <form method="POST" action="{{ route('admin.notifications.read_all') }}">
            @csrf
            <button class="btn btn-light-primary">
                <i class="fas fa-check-double me-2"></i> تعليم الكل كمقروء
            </button>
        </form>
    </div>

    <ul class="nav nav-pills gap-2 mb-4">
        <li class="nav-item">
            <a class="btn btn-outline-secondary @if($filter==='all') active @endif" href="{{ route('admin.notifications.index',['filter'=>'all']) }}">الكل</a>
        </li>
        <li class="nav-item">
            <a class="btn btn-outline-secondary @if($filter==='unread') active @endif" href="{{ route('admin.notifications.index',['filter'=>'unread']) }}">غير المقروءة ({{ $unreadCount }})</a>
        </li>
        <li class="nav-item">
            <a class="btn btn-outline-secondary @if($filter==='read') active @endif" href="{{ route('admin.notifications.index',['filter'=>'read']) }}">المقروءة</a>
        </li>
    </ul>

    <div class="card">
        <div class="card-body p-0">
            @forelse($notifications as $n)
            @php
            $isUnread = is_null($n->read_at);
            $title = data_get($n->data, 'title', 'Notification');
            $body = data_get($n->data, 'body', '');
            $url = data_get($n->data, 'url');
            @endphp

            <div class="p-3 border-bottom @if($isUnread) bg-light @endif">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="me-3">
                        <div class="fw-semibold">{{ $title }}</div>
                        @if($body)<div class="text-muted small">{{ $body }}</div>@endif
                        <div class="small text-secondary mt-1">{{ $n->created_at->format('Y-m-d H:i') }}</div>
                        @if($url)<a href="{{ $url }}" class="small">فتح الرابط</a>@endif
                    </div>
                    <form method="POST" action="{{ route('admin.notifications.read',$n->id) }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-secondary">مقروء</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">لا توجد إشعارات</div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">
        {{ $notifications->withQueryString()->links() }}
    </div>

</div>
@endsection