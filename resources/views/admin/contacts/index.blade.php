@extends('admin.app')
@section('title','Contact Messages')

@section('content')
<div class="container py-6">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h2 class="mb-0">Contact Messages</h2>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Received</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $m)
                    <tr>
                        <td>{{ $m->id }}</td>
                        <td>{{ $m->name }}</td>
                        <td><a href="mailto:{{ $m->email }}">{{ $m->email }}</a></td>
                        <td>{{ $m->subject ?: '—' }}</td>
                        <td>{{ $m->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">No messages yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">{{ $messages->links() }}</div>
    </div>
</div>
@endsection