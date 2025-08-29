
@extends('admin.app')
@section('title','تفاصيل الحجز')

@section('content')
<div class="container py-6">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">تفاصيل الحجز #{{ $reservation->id }}</h4>

            <dl class="row">
                <dt class="col-sm-3">التجربة</dt>
                <dd class="col-sm-9">{{ $reservation->experiment->title }}</dd>

                <dt class="col-sm-3">الطالب</dt>
                <dd class="col-sm-9">{{ $reservation->user->name }} ({{ $reservation->user->email }})</dd>

                <dt class="col-sm-3">الوقت</dt>
                <dd class="col-sm-9">{{ $reservation->starts_at }} → {{ $reservation->ends_at }}</dd>

                <dt class="col-sm-3">الحالة</dt>
                <dd class="col-sm-9">{{ $reservation->status }}</dd>
            </dl>

            <a href="{{ route('admin.reservations.index') }}" class="btn btn-light-primary">رجوع للقائمة</a>
            <a href="{{ route('admin.reservations.edit', $reservation) }}" class="btn btn-primary">تعديل</a>
        </div>
    </div>
</div>
@endsection