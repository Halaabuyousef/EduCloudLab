{{-- resources/views/admin/contacts/show.blade.php --}}
@extends('admin.app')
@section('title','Message #'.$message->id)

@section('content')
<div class="container py-6">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h2 class="mb-0">Message #{{ $message->id }}</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-light">Back</a>
            @if(!$message->read_at)
            <form action="{{ route('admin.contacts.read',$message) }}" method="POST">
                @csrf @method('PATCH')
                <button class="btn btn-light-primary">Mark read</button>
            </form>
            @else
            <form action="{{ route('admin.contacts.unread',$message) }}" method="POST">
                @csrf @method('PATCH')
                <button class="btn btn-light">Mark unread</button>
            </form>
            @endif
            <form action="{{ route('admin.contacts.destroy',$message) }}" method="POST" onsubmit="return confirm('Delete this message?')">
                @csrf @method('DELETE')
                <button class="btn btn-light-danger">Delete</button>
            </form>
        </div>
    </div>

    @if(session('msg'))
    <div class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
        {{ session('msg') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-2"><strong>From:</strong> {{ $message->name }} &lt;<a href="mailto:{{ $message->email }}">{{ $message->email }}</a>&gt;</div>
                    <div class="mb-2"><strong>Subject:</strong> {{ $message->subject ?: '—' }}</div>
                    <div class="mb-2"><strong>Received:</strong> {{ $message->created_at->toDayDateTimeString() }}</div>
                    <div class="mb-2"><strong>Status:</strong>
                        @if($message->read_at)
                        <span class="badge bg-success">Read</span> <small class="text-muted">at {{ $message->read_at->toDayDateTimeString() }}</small>
                        @else
                        <span class="badge bg-warning text-dark">New</span>
                        @endif
                    </div>
                    <hr>
                    <div style="white-space: pre-wrap">{{ $message->message }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Meta</h5>
                    <div class="mb-1"><strong>IP:</strong> {{ $message->ip ?: '—' }}</div>
                    <div class="mb-1"><strong>User Agent:</strong> <small class="text-muted">{{ $message->user_agent ?: '—' }}</small></div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Quick Reply</h5>
                    <form method="POST" action="{{ route('admin.contacts.reply', $message) }}" class="vstack gap-3">
                        @csrf
                        <input class="form-control" name="subject" placeholder="Subject (optional)" value="{{ old('subject', 'Re: '.($message->subject ?: 'Your message')) }}">
                        <textarea class="form-control" name="body" rows="6" placeholder="Type your reply..." required>{{ old('body') }}</textarea>
                        <div class="text-end"><button class="btn btn-primary">Send Reply</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection