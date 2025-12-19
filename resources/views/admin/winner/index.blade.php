@extends('admin.layouts.app')

@section('title', 'Management Pemenang')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
                <h6 class="card-title mb-0">Management Pemenang</h6>
            </div>
            <div class="card-body">
                <!-- Filter by Event -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <form method="GET" action="{{ route('admin.winner.index') }}" id="filterForm">
                            <div class="mb-3">
                                <label for="event_id" class="form-label">Filter by Event</label>
                                <select name="event_id" id="event_id" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">Semua Event</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                            {{ $event->nm_event }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                @if($winners->count() > 0)
                    <div class="table-responsive">
                        <table id="winnerTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Event</th>
                                    <th>Nama Peserta</th>
                                    <th>Nomor Kupon</th>
                                    <th>Nomor HP</th>
                                    <th>Hadiah</th>
                                    <th>Tanggal Diundi</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($winners as $winner)
                                    <tr>
                                        <td>{{ ($winners->currentPage() - 1) * $winners->perPage() + $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $winner->event->nm_event ?? '-' }}</strong>
                                        </td>
                                        <td>{{ $winner->participant->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $winner->participant->coupon_number ?? '-' }}</span>
                                        </td>
                                        <td>{{ $winner->participant->phone ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ $winner->prize_name ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if($winner->drawn_at)
                                                {{ $winner->drawn_at->format('d/m/Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger btn-delete-winner" 
                                                    data-id="{{ $winner->id }}"
                                                    data-name="{{ $winner->participant->name ?? 'Pemenang' }}"
                                                    title="Hapus">
                                                <i class="fi fi-rr-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $winners->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fi fi-rr-trophy" style="font-size: 48px; color: #ccc;"></i>
                        <h5 class="mt-3">Belum ada pemenang</h5>
                        <p class="text-muted">Pemenang akan muncul setelah pengundian dilakukan</p>
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
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#winnerTable').DataTable({
        responsive: true,
        order: [[6, 'desc']], // Sort by Drawn At desc
        paging: false, // Disable pagination karena kita pakai Laravel pagination
        searching: true,
        language: {
            processing: "Loading...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ entri",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
            infoFiltered: "(difilter dari _MAX_ total entri)",
            paginate: {
                next: "<i class='fi fi-rr-angle-right'></i>",
                previous: "<i class='fi fi-rr-angle-left'></i>"
            }
        }
    });

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
            heading: type === 'success' ? 'Berhasil' : 'Error',
            text: message,
            position: 'top-right',
            loaderBg: type === 'success' ? '#5ba035' : '#bf441d',
            icon: type,
            hideAfter: 3000,
            stack: 5
        });
    }

    // Handle delete with SweetAlert2 and AJAX
    $(document).on('click', '.btn-delete-winner', function(e) {
        e.preventDefault();
        const winnerId = $(this).data('id');
        const winnerName = $(this).data('name');
        const row = $(this).closest('tr');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Pemenang "${winnerName}" akan dihapus dan status peserta akan diubah menjadi bukan pemenang.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Delete via AJAX
                axios.delete(`/admin/winner/${winnerId}`)
                    .then(response => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Remove row from table
                            row.fadeOut(300, function() {
                                $(this).remove();
                                // Reload page if no more rows
                                if ($('#winnerTable tbody tr').length === 0) {
                                    location.reload();
                                }
                            });
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: error.response?.data?.message || 'Gagal menghapus pemenang.',
                            confirmButtonText: 'OK'
                        });
                    });
            }
        });
    });
});
</script>
@endpush

