@extends('admin.layouts.app')

@section('title', 'Peserta Event: ' . $event->nm_event)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
                <div>
                    <h6 class="card-title mb-1">Peserta Event: {{ $event->nm_event }}</h6>
                    <p class="text-muted mb-0 small">Kelola data peserta untuk pengundian</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.event.show', $event) }}" class="btn btn-secondary waves-effect waves-light">
                        <i class="fi fi-rr-arrow-left me-1"></i> Kembali ke Event
                    </a>
                    <button type="button" class="btn btn-danger waves-effect waves-light" id="btnClear" onclick="confirmClear()">
                        <i class="fi fi-rr-trash me-1"></i> Hapus Semua
                    </button>
                    <button type="button" class="btn btn-success waves-effect waves-light" id="btnImport" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fi fi-rr-upload me-1"></i> Import Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="participantsTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Kupon</th>
                                <th>Nama</th>
                                <th>No. HP</th>
                                <th>Status</th>
                                <th>Diimport Pada</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="importForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">File Excel <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls" required>
                        <small class="text-muted">Format: .xlsx atau .xls (Max: 5MB)</small>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fi fi-rr-info me-1"></i>
                        <strong>Download Template:</strong>
                        <a href="{{ route('admin.event.participants.template') }}" class="btn btn-sm btn-outline-primary ms-2">
                            <i class="fi fi-rr-download me-1"></i> Download Template
                        </a>
                    </div>
                    <div class="alert alert-warning">
                        <small>
                            <strong>Kolom yang diperlukan:</strong><br>
                            - nomor_kupon (wajib, unik per event)<br>
                            - nama (wajib)<br>
                            - no_hp (opsional)<br>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fi fi-rr-upload me-1"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
<script>
$(document).ready(function() {
    let table;

    // Setup CSRF token for Axios
    axios.defaults.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

    // Initialize DataTable
    table = $('#participantsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.event.participants.data', $event->id) }}",
            type: 'POST',
            data: function(d) {
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'coupon_number', name: 'coupon_number' },
            { data: 'name', name: 'name' },
            { data: 'phone', name: 'phone' },
            { data: 'is_winner', name: 'is_winner' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[5, 'desc']],
        pageLength: 10,
        pagingType: "simple",
        language: {
            processing: "Loading...",
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                next: "<i class='fi fi-rr-angle-right'></i>",
                previous: "<i class='fi fi-rr-angle-left'></i>"
            }
        }
    });

    // Show toast notification
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

    // Handle import form submit
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const btnSubmit = $(this).find('button[type="submit"]');
        const originalText = btnSubmit.html();
        btnSubmit.html('<i class="fi fi-rr-spinner fi-spin me-1"></i> Importing...').prop('disabled', true);
        
        const formData = new FormData(this);
        
        axios({
            method: 'post',
            url: "{{ route('admin.event.participants.import', $event->id) }}",
            data: formData,
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(function(response) {
            $('#importModal').modal('hide');
            table.ajax.reload();
            
            let message = response.data.message;
            if (response.data.errors && response.data.errors.length > 0) {
                console.error(response.data.errors);
                message += ' Cek console untuk detail error.';
            }
            
            showToast('success', message);
            $('#importForm')[0].reset();
        })
        .catch(function(error) {
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                $('.invalid-feedback').text('');
                $('#file').removeClass('is-invalid');
                
                $.each(errors, function(key, value) {
                    const input = $(`#${key}`);
                    if(input.length) {
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(value[0]);
                    } else {
                         showToast('error', value[0]);
                    }
                });
            } else {
                showToast('error', error.response?.data?.message || 'Import failed');
            }
        })
        .finally(function() {
            btnSubmit.html(originalText).prop('disabled', false);
        });
    });

    // Handle delete single
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Peserta?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`{{ url('admin/event/participants') }}/${id}`)
                    .then(function(response) {
                        table.ajax.reload(null, false);
                        showToast('success', response.data.message);
                    })
                    .catch(function(error) {
                        showToast('error', error.response?.data?.message || 'Gagal menghapus peserta');
                    });
            }
        });
    });
});
    
// Handle clear all
function confirmClear() {
    Swal.fire({
        title: 'Hapus SEMUA Peserta?',
        text: "PERINGATAN: Semua data peserta dalam event ini akan dihapus permanen! Tindakan ini tidak dapat dibatalkan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, HAPUS SEMUA!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete("{{ route('admin.event.participants.clear', $event->id) }}")
                .then(function(response) {
                    $('#participantsTable').DataTable().ajax.reload();
                    $.toast({
                        heading: 'Success',
                        text: response.data.message,
                        position: 'top-right',
                        loaderBg: '#5ba035',
                        icon: 'success',
                        hideAfter: 3000,
                        stack: 5
                    });
                })
                .catch(function(error) {
                    $.toast({
                        heading: 'Error',
                        text: error.response?.data?.message || 'Gagal menghapus data',
                        position: 'top-right',
                        loaderBg: '#bf441d',
                        icon: 'error',
                        hideAfter: 3000,
                        stack: 5
                    });
                });
        }
    });
}
</script>
@endpush
