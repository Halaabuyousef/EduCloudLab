{{-- resources/views/admin/about.blade.php --}}
@extends('admin.app')
@section('title','About Us')

@section('content')
<div class="container-xxl py-6">

  <div class="text-center mb-5">
    <h2 class="fw-bold">About Us</h2>
  </div>


  <div class="card mb-4">
    <div class="card-body">
      <h4 class="fw-bold mb-2">Who we are</h4>
      <p class="text-muted">To create a world where knowledge is available to all…</p>
    </div>
  </div>
  {{-- Vision/Mission ثابتة --}}
  <div class="card mb-4">
    <div class="card-body">
      <h4 class="fw-bold mb-2">Our Vision</h4>
      <p class="text-muted">To create a world where knowledge is available to all…</p>
    </div>
  </div>

  <div class="card mb-5">
    <div class="card-body">
      <h4 class="fw-bold mb-2">Our Mission</h4>
      <p class="text-muted">Delivering innovative and easy-to-use digital tools…</p>
    </div>
  </div>
  @php
  $admin = Auth::guard('admin')->user();
  $avatarUrl = $admin?->image ? asset('storage/'.$admin->image) : null;

  // أحرف بديلة عند عدم وجود صورة
  $parts = preg_split('/\s+/', trim($admin->name ?? ''));
  $initial = mb_strtoupper(mb_substr($parts[0] ?? '',0,1) . mb_substr($parts[1] ?? '',0,1));
  @endphp


  {{-- Team from DB --}}
<div class="card">
  <div class="card-body">
    <h4 class="fw-bold mb-4">Meet Our Team</h4>

    <div class="row g-3">
      @foreach($admins as $member)
      @php
      // مسار الصورة لكل عضو
      $memberAvatar = $member->image ? asset('storage/'.$member->image) : null;

      // أحرف بديلة لكل عضو
      $parts = preg_split('/\s+/', trim($member->name ?? ''));
      $memberInitials = mb_strtoupper(
      (mb_substr($parts[0] ?? '', 0, 1)) . (mb_substr($parts[1] ?? '', 0, 1))
      );

      // المسمّى (لو بتستعمل Spatie roles استبدل السطر التالي بما بعده)
      $memberRole = $member->role ?? 'Admin';
      // $memberRole = method_exists($member,'getRoleNames') ? $member->getRoleNames()->implode(', ') : ($member->role ?? 'Admin');
      @endphp

      <div class="col-md-6 col-lg-4">
        <div class="d-flex align-items-center p-3 bg-light rounded-3 shadow-sm gap-3">
          @if($memberAvatar)
          <img src="{{ $memberAvatar }}" class="rounded-circle" style="width:50px;height:50px;object-fit:cover;" alt="avatar">
          @else
          <div class="rounded-circle bg-white border text-muted fw-bold d-flex align-items-center justify-content-center"
            style="width:50px;height:50px;">
            {{ $memberInitials }}
          </div>
          @endif

          <div>
            <div class="fw-semibold">{{ $member->name }}</div>
            <div class="text-muted small">{{ $memberRole }}</div>
            <div class="text-muted small">{{ $member->email }}</div>
          </div>
        </div>
      </div>
      @endforeach
    </div>

  </div>
</div>
</div>
@endsection