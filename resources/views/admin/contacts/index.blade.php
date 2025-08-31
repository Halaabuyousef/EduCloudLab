{{-- resources/views/admin/contacts/index.blade.php --}}
@extends('admin.app')
@section('title','Contact Messages')

@section('content')
<div class="container p-5">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h2 class="mb-0">
            Contact Messages
            @isset($newCount)
            <span class="badge bg-warning text-dark ms-2">New: {{ $newCount }}</span>
            @endisset
        </h2>
        <form method="GET" class="d-flex gap-2">
            <input name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Search: name/email/subject">
            <select name="read" class="form-select" style="width:auto">
                <option value="all" {{ ($read ?? 'all')==='all'?'selected':'' }}>All</option>
                <option value="new" {{ ($read ?? '')==='new'?'selected':'' }}>New</option>
                <option value="read" {{ ($read ?? '')==='read'?'selected':'' }}>Read</option>
            </select>
            <select name="per" class="form-select" style="width:auto">
                @foreach([15,25,50,100] as $n)
                <option value="{{ $n }}" {{ ($per ?? 15)===$n?'selected':'' }}>{{ $n }}</option>
                @endforeach
            </select>
            <button class="btn btn-light">Filter</button>
            <a class="btn btn-light-primary"
                href="{{ route('admin.contacts.export', request()->only(['read','from','to'])) }}">Export CSV</a>
        </form>
    </div>

    {{-- Alerts --}}
    @if (session('msg'))
    <div id="alertBox"
        class="alert alert-{{ session('type') }} alert-dismissible fade show"
        role="alert"
        style="background-color: #dcd0f7; color: #000;">
        {{ session('msg') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        setTimeout(function() {
            let alertEl = document.getElementById('alertBox');
            if (alertEl) {
                // Bootstrap 5 way to close programmatically
                let alert = new bootstrap.Alert(alertEl);
                alert.close();
            }
        }, 1000);
    </script>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive p-3">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Received</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $m)
                    <tr @class(['table-warning'=> is_null($m->read_at)])>
                        <td>{{ $m->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $m->name }}</div>
                            <a href="mailto:{{ $m->email }}" class="text-muted">{{ $m->email }}</a>
                        </td>
                        <td>{{ $m->subject ?: '—' }}</td>
                        <td>
                            @if($m->read_at)
                            <span class="badge bg-success">Read</span>
                            @else
                            <span class="badge bg-warning text-dark">New</span>
                            @endif
                        </td>
                        <td title="{{ $m->created_at }}">{{ $m->created_at->diffForHumans() }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-light-primary" href="{{ route('admin.contacts.show',$m) }}">Open</a>
                            @if(!$m->read_at)
                            <form action="{{ route('admin.contacts.read',$m) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-light">Mark read</button>
                            </form>
                            @else
                            <form action="{{ route('admin.contacts.unread',$m) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-light">Mark unread</button>
                            </form>
                            @endif
                            <form action="{{ route('admin.contacts.destroy',$m) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this message?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">No messages yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">{{ $messages->withQueryString()->links() }}</div>
    </div>
</div>
@endsection