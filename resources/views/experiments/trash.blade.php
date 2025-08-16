@extends('admin.app')

@section('title','Trashed Experiments')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container py-6">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h2 class="mb-0">Trashed Experiments</h2>
       
        <div class="d-flex gap-3">
            <a href="{{ route('admin.experiments.index') }}" class="btn btn-light">
                ← Back to All
            </a>
        </div>
    </div>

    {{-- Flash --}}
    @if (session('msg'))
    <div id="alertBox" class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
        {{ session('msg') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        setTimeout(function() {
            const el = document.getElementById('alertBox');
            if (el)(new bootstrap.Alert(el)).close();
        }, 3000);
    </script>
    @endif

    {{-- Table --}}
    <div class="card">
        <div class="card-body">
            <table id="trash_table" class="table table-row-bordered gy-5 align-middle">
                <thead>
                    <tr class="fw-bold fs-6 text-muted">
                        <th>ID</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Deleted At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($experiments as $row)
                    <tr data-id="{{ $row->id }}">
                        <td>{{ $row->id }}</td>

                        <td>
                            @if($row->image)
                            <img src="{{ asset('images/'.$row->image) }}" width="50" height="50" class="rounded" alt="">
                            @endif
                        </td>

                        <td>{{ $row->title }}</td>
                        <td>{{ $row->description }}</td>

                        <td>
                            <span class="badge {{ $row->status==='available'?'badge-success':($row->status==='in_use'?'badge-warning':'badge-secondary') }}">
                                {{ $row->status }}
                            </span>
                        </td>

                        <td>{{ $row->deleted_at }}</td>

                        {{-- الأزرار هنا داخل عمود الأكشنز --}}
                        <td class="text-end text-nowrap">
                            {{-- استرجاع --}}
                            <form class="d-inline" method="POST" action="{{ route('admin.experiments.restore', $row->id) }}">
                                @csrf
                                <button class="btn btn-icon btn-success btn-sm" title="Restore">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </form>

                            {{-- حذف نهائي --}}
                            <form class="d-inline" method="POST" action="{{ route('admin.experiments.forceDelete', $row->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-icon btn-danger btn-sm" title="Delete permanently"
                                    onclick="return confirm('Delete permanently? This cannot be undone.')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>


            <div class="mt-4">
                {{ $experiments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // DataTables (نفس إعدادات صفحة all)
    document.addEventListener('DOMContentLoaded', function() {
        $('#trash_table').DataTable({
            responsive: true,
            dom: `<'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
            lengthMenu: [5, 10, 25, 50],
            pageLength: 10,
            order: [
                [0, 'desc']
            ]
        });
    });

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Restore via AJAX
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.restore-btn');
        if (!btn) return;

        const tr = btn.closest('tr');
        if (!confirm('Restore this experiment?')) return;

        try {
            const res = await fetch(btn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.ok) {
                // احذف الصف من الجدول بدون ريفرش
                // (مهم: لو تستخدم DataTables، احذف عبر API ليُحدّث pagination)
                const table = $('#trash_table').DataTable();
                table.row(tr).remove().draw();
                showToast(data.message || 'Restored successfully');
            } else {
                showToast('Failed to restore');
            }
        } catch (err) {
            showToast('Error restoring item');
        }
    });

    // Force delete via AJAX
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.force-btn');
        if (!btn) return;

        const tr = btn.closest('tr');
        if (!confirm('Delete permanently? This cannot be undone.')) return;

        try {
            const res = await fetch(btn.dataset.url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.ok) {
                const table = $('#trash_table').DataTable();
                table.row(tr).remove().draw();
                showToast(data.message || 'Deleted permanently');
            } else {
                showToast('Failed to delete');
            }
        } catch (err) {
            showToast('Error deleting item');
        }
    });

    // Toast بسيطة (بدون مكتبات إضافية)
    function showToast(msg) {
        const el = document.createElement('div');
        el.className = 'toast align-items-center text-white bg-primary border-0 position-fixed top-0 end-0 m-3';
        el.setAttribute('role', 'alert');
        el.innerHTML = `<div class="d-flex"><div class="toast-body">${msg}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
        document.body.appendChild(el);
        const toast = new bootstrap.Toast(el, {
            delay: 2500
        });
        toast.show();
        el.addEventListener('hidden.bs.toast', () => el.remove());
    }
</script>

@endsection