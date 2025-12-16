@extends('admin.layouts.app')

@section('title', 'Event Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
                <h6 class="card-title mb-0">Event Management</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.event.create') }}" class="btn btn-primary waves-effect waves-light">
                        <i class="fi fi-rr-plus me-1"></i> Add Event
                    </a>
                </div>
            </div>
            <div class="card-body">

                @if($events->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Event</th>
                                    <th>OPD</th>
                                    <th>Status</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $index => $event)
                                    <tr>
                                        <td>{{ $events->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $event->nm_event }}</strong>
                                            @if($event->deskripsi)
                                                <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($event->deskripsi, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $event->opd->nama_instansi ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $event->status_badge_class }} rounded-pill px-2 py-1">
                                                {{ $event->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($event->tgl_mulai)
                                                {{ $event->tgl_mulai->format('d/m/Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($event->tgl_selesai)
                                                {{ $event->tgl_selesai->format('d/m/Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $event->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.event.show', $event) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="fi fi-rr-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.event.edit', $event) }}" class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="fi fi-rr-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.event.destroy', $event) }}" method="POST" class="d-inline delete-form" id="deleteForm{{ $event->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete-event" data-id="{{ $event->id }}" title="Delete">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $events->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fi fi-rr-calendar-x" style="font-size: 48px; color: #ccc;"></i>
                        <h5 class="mt-3">Belum ada event</h5>
                        <p class="text-muted">Mulai dengan membuat event pertama Anda</p>
                        <a href="{{ route('admin.event.create') }}" class="btn btn-primary mt-2">
                            <i class="fi fi-rr-plus me-1"></i> Buat Event Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
<script>
$(document).ready(function() {
    // Show toast notification for session messages
    @if(session('success'))
        showToast('success', '{{ session('success') }}');
    @endif

    @if(session('error'))
        showToast('error', '{{ session('error') }}');
    @endif

    // Show toast notification function
    function showToast(type, message) {
        $.toast({
            heading: type === 'success' ? 'Success' : 'Error',
            text: message,
            position: 'top-right',
            loaderBg: type === 'success' ? '#5ba035' : '#bf441d',
            icon: type,
            hideAfter: 3000,
            stack: 5
        });
    }

    // Handle delete with SweetAlert2
    $(document).on('click', '.btn-delete-event', function(e) {
        e.preventDefault();
        const eventId = $(this).data('id');
        const form = $('#deleteForm' + eventId);
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form secara langsung
                form[0].submit();
            }
        });
    });
});
</script>
@endpush

