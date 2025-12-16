@extends('admin.layouts.app')

@section('title', 'Users Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
                <h6 class="card-title mb-0">Users Management</h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success waves-effect waves-light" id="btnImport"
                        data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fi fi-rr-upload me-1"></i> Import
                    </button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" id="btnAdd"
                        data-bs-toggle="modal" data-bs-target="#userModal">
                        <i class="fi fi-rr-plus me-1"></i> Add User
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>OPD</th>
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
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="user_id" name="id">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger"
                                id="passwordRequired">*</span></label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="text-muted" id="passwordHint">Leave blank if you don't want to change
                            password</small>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select Role</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="opd_id" class="form-label">OPD</label>
                        <select class="form-select" id="opd_id" name="opd_id">
                            <option value="">Select OPD (Optional)</option>
                        </select>
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
                <h5 class="modal-title" id="importModalLabel">Import Users</h5>
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
                    <div class="mb-3">
                        <label for="default_opd_id" class="form-label">Default OPD</label>
                        <select class="form-select" id="default_opd_id" name="default_opd_id">
                            <option value="">Select OPD (Optional)</option>
                            @foreach(\App\Models\Opd::orderBy('nama_instansi')->get() as $opd)
                            <option value="{{ $opd->id }}">{{ $opd->nama_instansi }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">OPD yang akan digunakan jika tidak ada di Excel</small>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fi fi-rr-info me-1"></i>
                        <strong>Download Template:</strong>
                        <a href="{{ route('admin.users.downloadTemplate') }}"
                            class="btn btn-sm btn-outline-primary ms-2">
                            <i class="fi fi-rr-download me-1"></i> Download Template
                        </a>
                    </div>
                    <div class="alert alert-warning">
                        <small>
                            <strong>Kolom yang diperlukan:</strong><br>
                            - name (required)<br>
                            - email (required)<br>
                            - password (required, min 8 characters)<br>
                            - role (required, harus: admin_opd - gunakan dropdown di Excel)<br>
                            - opd (optional, pilih dari dropdown di Excel. Akan menggunakan Default OPD jika kosong)
                        </small>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <strong>Catatan:</strong><br>
                            - Template Excel memiliki 2 sheet: "Data Users" (untuk import) dan "Daftar OPD"
                            (referensi)<br>
                            - Kolom "role" memiliki dropdown dengan pilihan: admin_opd<br>
                            - Kolom "opd" memiliki dropdown yang mengambil data dari sheet "Daftar OPD"<br>
                            - Hanya import sheet "Data Users" saja
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
    let table;
    let isEdit = false;

    // Initialize Bootstrap tooltips
    function initTooltips() {
        // Destroy existing tooltips first
        var existingTooltips = document.querySelectorAll('.btn-edit, .btn-delete');
        existingTooltips.forEach(function(el) {
            var tooltip = bootstrap.Tooltip.getInstance(el);
            if (tooltip) {
                tooltip.dispose();
            }
        });
        
        // Initialize new tooltips for edit and delete buttons
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
    
    // Initialize tooltips on page load
    initTooltips();

    // Initialize Select2 for role field with AJAX
    function initSelect2Role(selectedValue = null, selectedText = null) {
        if ($('#role').hasClass('select2-hidden-accessible')) {
            $('#role').select2('destroy');
        }
        
        // Clear existing options
        $('#role').empty();
        
        // Add placeholder
        $('#role').append('<option value="">Select Role</option>');
        
        // If we have a selected value, add it to DOM first
        if (selectedValue && selectedText) {
            $('#role').append(new Option(selectedText, selectedValue, true, true));
        }
        
        // Initialize Select2
        $('#role').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select Role',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#userModal'), // Important for modal
            ajax: {
                url: "{{ route('admin.users.roles') }}",
                type: 'GET',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term || '', // search term
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: false
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            templateResult: function(role) {
                if (role.loading) {
                    return 'Loading...';
                }
                return role.text;
            },
            templateSelection: function(role) {
                return role.text || role.id;
            }
        });
        
        // Set value after Select2 is fully initialized
        if (selectedValue) {
            // Function to set the value
            const setValue = function() {
                // Ensure option exists in DOM
                let $option = $('#role').find('option[value="' + selectedValue + '"]');
                if ($option.length === 0) {
                    // Add option if it doesn't exist (before placeholder)
                    $('#role option[value=""]').after(new Option(selectedText, selectedValue, true, true));
                }
                
                // Set value
                $('#role').val(selectedValue);
                
                // Trigger change to update Select2 display
                $('#role').trigger('change');
            };
            
            // Try setting value multiple times to ensure it works
            setTimeout(setValue, 100);
            setTimeout(setValue, 300);
            
            // Also set when Select2 is opened (as backup)
            $('#role').one('select2:open', setValue);
        }
    }

    // Initialize Select2 for OPD field with AJAX
    function initSelect2Opd(selectedValue = null, selectedText = null) {
        if ($('#opd_id').hasClass('select2-hidden-accessible')) {
            $('#opd_id').select2('destroy');
        }
        
        // Clear existing options
        $('#opd_id').empty();
        
        // Add placeholder
        $('#opd_id').append('<option value="">Select OPD (Optional)</option>');
        
        // If we have a selected value, add it to DOM first
        if (selectedValue && selectedText) {
            $('#opd_id').append(new Option(selectedText, selectedValue, true, true));
        }
        
        // Initialize Select2
        $('#opd_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select OPD (Optional)',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#userModal'), // Important for modal
            ajax: {
                url: "{{ route('admin.users.opds') }}",
                type: 'GET',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term || '', // search term
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: false
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            templateResult: function(opd) {
                if (opd.loading) {
                    return 'Loading...';
                }
                return opd.text;
            },
            templateSelection: function(opd) {
                return opd.text || opd.id;
            }
        });
        
        // Set value after Select2 is fully initialized
        if (selectedValue) {
            // Function to set the value
            const setValue = function() {
                // Ensure option exists in DOM
                let $option = $('#opd_id').find('option[value="' + selectedValue + '"]');
                if ($option.length === 0) {
                    // Add option if it doesn't exist (before placeholder)
                    $('#opd_id option[value=""]').after(new Option(selectedText, selectedValue, true, true));
                }
                
                // Set value
                $('#opd_id').val(selectedValue);
                
                // Trigger change to update Select2 display
                $('#opd_id').trigger('change');
            };
            
            // Try setting value multiple times to ensure it works
            setTimeout(setValue, 100);
            setTimeout(setValue, 300);
            
            // Also set when Select2 is opened (as backup)
            $('#opd_id').one('select2:open', setValue);
        } else {
            setTimeout(function() {
                $('#opd_id').val(null).trigger('change');
            }, 100);
        }
    }

    // Initialize Select2 for both fields
    function initSelect2() {
        initSelect2Role();
        initSelect2Opd();
    }

    // Store edit data temporarily
    let editUserData = null;
    
    // Initialize Select2 when modal is shown
    $('#userModal').on('shown.bs.modal', function() {
        if (isEdit && editUserData) {
            // Initialize Select2 with selected values for edit mode
            const user = editUserData;
            const roleText = user.role.charAt(0).toUpperCase() + user.role.slice(1).replace('_', ' ');
            const opdText = (user.opd && user.opd.nama_instansi) ? user.opd.nama_instansi : null;
            const opdId = user.opd_id || null;
            
            initSelect2Role(user.role, roleText);
            initSelect2Opd(opdId, opdText);
            
            // Clear edit data after use
            editUserData = null;
        } else if (!isEdit) {
            // Initialize Select2 for add mode
            if (!$('#role').hasClass('select2-hidden-accessible')) {
                initSelect2Role();
            }
            if (!$('#opd_id').hasClass('select2-hidden-accessible')) {
                initSelect2Opd();
            }
        }
    });

    // Setup CSRF token for Axios
    axios.defaults.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

    // Initialize DataTable
    table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.users.data') }}",
            type: 'POST',
            data: function(d) {
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'role', name: 'role' },
            { data: 'opd', name: 'opd' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[5, 'desc']], // Order by created_at descending (newest first)
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
        $('#userForm')[0].reset();
        $('#user_id').val('');
        $('#userModalLabel').text('Add User');
        $('#passwordRequired').show();
        $('#passwordHint').hide();
        $('#password').prop('required', true);
        $('.invalid-feedback').text('');
        $('.form-control, .form-select').removeClass('is-invalid');
        // Only reset Select2 if not in edit mode
        if (!isEdit) {
            initSelect2(); // Reinitialize Select2 to reset
        }
        isEdit = false;
    }

    // Open modal for add
    $('#btnAdd').on('click', function() {
        resetForm();
        $('#userModal').modal('show');
    });

    // Open modal for edit
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        isEdit = true; // Set before resetForm to prevent Select2 reset
        
        // Destroy Select2 first to clear any old data
        if ($('#role').hasClass('select2-hidden-accessible')) {
            $('#role').select2('destroy');
        }
        if ($('#opd_id').hasClass('select2-hidden-accessible')) {
            $('#opd_id').select2('destroy');
        }
        
        resetForm();
        $('#userModalLabel').text('Edit User');
        $('#passwordRequired').hide();
        $('#passwordHint').show();
        $('#password').prop('required', false);
        
        // Clear previous edit data
        editUserData = null;

        axios.get(`{{ url('admin/users') }}/${id}`)
            .then(function(response) {
                const user = response.data.data;
                $('#user_id').val(user.id);
                $('#name').val(user.name);
                $('#email').val(user.email);
                
                // Store user data for use in shown.bs.modal event
                editUserData = user;
                
                // Show modal - Select2 will be initialized in shown.bs.modal event
                $('#userModal').modal('show');
            })
            .catch(function(error) {
                showToast('error', 'Failed to load user data');
                isEdit = false; // Reset flag on error
                editUserData = null;
            });
    });

    // Handle form submit
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#user_id').val();
        const url = id ? `{{ url('admin/users') }}/${id}` : "{{ route('admin.users.store') }}";
        const method = id ? 'put' : 'post';

        // Build form data manually to ensure all values are captured
        const formData = new FormData();
        formData.append('name', $('#name').val());
        formData.append('email', $('#email').val());
        formData.append('role', $('#role').val() || '');
        formData.append('opd_id', $('#opd_id').val() || '');
        
        // Only add password if it has a value
        const password = $('#password').val();
        if (password) {
            formData.append('password', password);
        }
        
        // For PUT method, Laravel requires method spoofing
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
            $('#userModal').modal('hide');
            // Reload and go to first page to show new/updated data at top
            table.ajax.reload(function() {
                table.page('first').draw('page');
            }, false);
            showToast('success', response.data.message);
            resetForm();
            // Re-initialize tooltips after table reload
            setTimeout(function() {
                initTooltips();
            }, 100);
        })
        .catch(function(error) {
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                $('.invalid-feedback').text('');
                $('.form-control, .form-select').removeClass('is-invalid');
                
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
                axios.delete(`{{ url('admin/users') }}/${id}`)
                    .then(function(response) {
                        table.ajax.reload(null, false); // Reload without resetting pagination
                        showToast('success', response.data.message);
                        // Re-initialize tooltips after table reload
                        setTimeout(function() {
                            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                                return new bootstrap.Tooltip(tooltipTriggerEl);
                            });
                        }, 100);
                    })
                    .catch(function(error) {
                        showToast('error', error.response?.data?.message || 'Failed to delete user');
                    });
            }
        });
    });

    // Reset form when modal is closed
    $('#userModal').on('hidden.bs.modal', function() {
        // Clear edit data
        editUserData = null;
        isEdit = false;
        resetForm();
    });

    // Handle import form submit
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        axios({
            method: 'post',
            url: "{{ route('admin.users.import') }}",
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
                $('#file, #default_opd_id').removeClass('is-invalid');
                
                $.each(errors, function(key, value) {
                    const input = $(`#${key}`);
                    input.addClass('is-invalid');
                    input.siblings('.invalid-feedback').text(value[0]);
                });
            } else {
                showToast('error', error.response?.data?.message || 'Import failed');
            }
        });
    });
});
</script>
@endpush