@extends('admin.layouts.app')

@section('title', 'Penyelenggara Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
                <h6 class="card-title mb-0">Penyelenggara Management</h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success waves-effect waves-light" id="btnImport" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fi fi-rr-upload me-1"></i> Import
                    </button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" id="btnAdd" data-bs-toggle="modal" data-bs-target="#opdModal">
                        <i class="fi fi-rr-plus me-1"></i> Tambah Penyelenggara
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="opdTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Penyelenggara</th>
                                <th>Singkatan</th>
                                <th>Nomor HP</th>
                                <th>Created At</th>
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

<!-- Modal -->
<div class="modal fade" id="opdModal" tabindex="-1" aria-labelledby="opdModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="opdModalLabel">Tambah Penyelenggara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="opdForm">
                <div class="modal-body">
                    <input type="hidden" id="opd_id" name="id">
                    <div class="mb-3">
                        <label for="nama_penyelenggara" class="form-label">Nama Penyelenggara <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_penyelenggara" name="nama_penyelenggara" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="singkatan" class="form-label">Singkatan</label>
                        <input type="text" class="form-control" id="singkatan" name="singkatan">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="nomor_hp" class="form-label">Nomor HP</label>
                        <input type="text" class="form-control" id="nomor_hp" name="nomor_hp">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Penyelenggara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="importForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Excel File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls" required>
                        <small class="text-muted">Format: .xlsx atau .xls (Max: 2MB)</small>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fi fi-rr-info me-1"></i>
                        <strong>Download Template:</strong>
                        <a href="{{ route('admin.opd.downloadTemplate') }}" class="btn btn-sm btn-outline-primary ms-2">
                            <i class="fi fi-rr-download me-1"></i> Download Template
                        </a>
                    </div>
                    <div class="alert alert-warning">
                        <small>
                            <strong>Kolom yang diperlukan:</strong><br>
                            - nama_penyelenggara (required)<br>
                            - singkatan (optional)<br>
                            - nomor_hp (optional)
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
    let isEdit = false;

    // Setup CSRF token for Axios
    axios.defaults.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

    // Initialize DataTable
    table = $('#opdTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.opd.data') }}",
            type: 'POST',
            data: function(d) {
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_penyelenggara', name: 'nama_penyelenggara' },
            { data: 'singkatan', name: 'singkatan' },
            { data: 'nomor_hp', name: 'nomor_hp' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[4, 'desc']],
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

    // Reset form
    function resetForm() {
        $('#opdForm')[0].reset();
        $('#opd_id').val('');
        $('#opdModalLabel').text('Tambah Penyelenggara');
        $('.invalid-feedback').text('');
        $('.form-control').removeClass('is-invalid');
        isEdit = false;
    }

    // Initialize Bootstrap tooltips
    function initTooltips() {
        var existingTooltips = document.querySelectorAll('.btn-edit, .btn-delete');
        existingTooltips.forEach(function(el) {
            var tooltip = bootstrap.Tooltip.getInstance(el);
            if (tooltip) {
                tooltip.dispose();
            }
        });
        
        var editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(function(btn) {
            new bootstrap.Tooltip(btn, {
                placement: 'top',
                title: 'Edit'
            });
        });
        
        var deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(function(btn) {
            new bootstrap.Tooltip(btn, {
                placement: 'top',
                title: 'Delete'
            });
        });
    }
    
    initTooltips();

    // Open modal for add
    $('#btnAdd').on('click', function() {
        resetForm();
        $('#opdModal').modal('show');
    });

    // Open modal for edit
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        isEdit = true;
        resetForm();
        $('#opdModalLabel').text('Edit Penyelenggara');

        axios.get(`{{ url('admin/opd') }}/${id}`)
            .then(function(response) {
                const opd = response.data.data;
                $('#opd_id').val(opd.id);
                $('#nama_penyelenggara').val(opd.nama_penyelenggara);
                $('#singkatan').val(opd.singkatan);
                $('#nomor_hp').val(opd.nomor_hp);
                $('#opdModal').modal('show');
            })
            .catch(function(error) {
                showToast('error', 'Gagal memuat data Penyelenggara');
            });
    });

    // Handle form submit
    $('#opdForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#opd_id').val();
        const url = id ? `{{ url('admin/opd') }}/${id}` : "{{ route('admin.opd.store') }}";
        const method = id ? 'put' : 'post';

        const formData = new FormData();
        formData.append('nama_penyelenggara', $('#nama_penyelenggara').val());
        formData.append('singkatan', $('#singkatan').val() || '');
        formData.append('nomor_hp', $('#nomor_hp').val() || '');
        
        if (method === 'put') {
            formData.append('_method', 'PUT');
        }

        axios({
            method: method === 'put' ? 'post' : method,
            url: url,
            data: formData,
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(function(response) {
            $('#opdModal').modal('hide');
            table.ajax.reload(function() {
                table.page('first').draw('page');
            }, false);
            showToast('success', response.data.message);
            resetForm();
            setTimeout(function() {
                initTooltips();
            }, 100);
        })
        .catch(function(error) {
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                
                $.each(errors, function(key, value) {
                    const input = $(`#${key}`);
                    input.addClass('is-invalid');
                    input.siblings('.invalid-feedback').text(value[0]);
                });
            } else {
                showToast('error', error.response?.data?.message || 'An error occurred');
            }
        });
    });

    // Handle delete
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`{{ url('admin/opd') }}/${id}`)
                    .then(function(response) {
                        table.ajax.reload(null, false);
                        showToast('success', response.data.message);
                        setTimeout(function() {
                            initTooltips();
                        }, 100);
                    })
                    .catch(function(error) {
                        showToast('error', error.response?.data?.message || 'Gagal menghapus Penyelenggara');
                    });
            }
        });
    });

    // Reset form when modal is closed
    $('#opdModal').on('hidden.bs.modal', function() {
        resetForm();
    });

    // Handle import form submit
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        axios({
            method: 'post',
            url: "{{ route('admin.opd.import') }}",
            data: formData,
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(function(response) {
            $('#importModal').modal('hide');
            table.ajax.reload(function() {
                table.page('first').draw('page');
            }, false);
            
            let message = response.data.message;
            if (response.data.errors && response.data.errors.length > 0) {
                message += '\n\nErrors:\n' + response.data.errors.join('\n');
            }
            
            showToast('success', message);
            $('#importForm')[0].reset();
        })
        .catch(function(error) {
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                $('.invalid-feedback').text('');
                $('#file').removeClass('is-invalid');
                
                if (errors.file) {
                    $('#file').addClass('is-invalid');
                    $('#file').siblings('.invalid-feedback').text(errors.file[0]);
                }
            } else {
                showToast('error', error.response?.data?.message || 'Import failed');
            }
        });
    });
});
</script>
@endpush

