@extends('admin.app')
@section('title','Supervisor Details')

@section('content')
<div class="container py-6">

    <div class="d-flex justify-content-between align-items-center mb-6">
        <h2 class="mb-0">Supervisor Details</h2>
        <a href="{{ route('admin.supervisors.index') }}" class="btn btn-light">رجوع</a>
    </div>

    {{-- بيانات المشرف --}}
    <div class="card mb-6">
        <div class="card-body">
            <div class="d-flex align-items-center gap-4">


                <div>
                    <h4 class="mb-1">{{ $supervisor->name }}</h4>
                    <div class="text-muted">{{ $supervisor->email }}</div>
                    <div class="text-muted">{{ $supervisor->phone ?? '—' }} | {{ $supervisor->country ?? '—' }}</div>
                </div>
            </div>
            @if($supervisor->bio)
            <div class="mt-3">{{ $supervisor->bio }}</div>
            @endif
        </div>
    </div>

    {{-- إضافة مستخدم للمشرف --}}
    <div class="card mb-6">
        <div class="card-header">
            <h5 class="mb-0">Add a followed user</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.supervisors.attach',$supervisor) }}" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <select name="user_id" class="form-select" required>
                        <option value="">— اختر مستخدم مستقل —</option>
                        @foreach($independents as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>

  
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">User under supervisor</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد</th>
                            <th class="text-center">إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $m)
                        <tr>
                            <td>{{ $m->id }}</td>
                            <td>{{ $m->name }}</td>
                            <td>{{ $m->email }}</td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('admin.supervisors.detach',[$supervisor,$m]) }}"
                                    onsubmit="return confirm('إزالة المستخدم من هذا المشرف؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light-danger">إزالة</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-8">لا يوجد مستخدمون تابعون</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $members->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>
@endsection