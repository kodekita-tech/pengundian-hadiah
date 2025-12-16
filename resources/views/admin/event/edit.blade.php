@extends('admin.layouts.app')

@section('title', 'Edit Event')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Edit Event</h6>
            </div>
            <div class="card-body">

                <form action="{{ route('admin.event.update', $event) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nm_event" class="form-label">Nama Event <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nm_event') is-invalid @enderror" 
                               id="nm_event" name="nm_event" value="{{ old('nm_event', $event->nm_event) }}" required>
                        @error('nm_event')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($canSelectOpd)
                    <div class="mb-3">
                        <label for="opd_id" class="form-label">OPD <span class="text-danger">*</span></label>
                        <select class="form-select @error('opd_id') is-invalid @enderror" 
                                id="opd_id" name="opd_id" required>
                            @php
                                $selectedOpdId = old('opd_id', $event->opd_id);
                            @endphp
                            @if($selectedOpdId)
                                @php
                                    $selectedOpd = \App\Models\Opd::find($selectedOpdId);
                                @endphp
                                @if($selectedOpd)
                                    <option value="{{ $selectedOpd->id }}" selected>
                                        {{ $selectedOpd->nama_instansi }}{{ $selectedOpd->singkatan ? ' (' . $selectedOpd->singkatan . ')' : '' }}
                                    </option>
                                @endif
                            @endif
                        </select>
                        @error('opd_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @else
                    @if(Auth::user()->opd_id)
                    <input type="hidden" name="opd_id" value="{{ Auth::user()->opd_id }}">
                    @else
                    <div class="alert alert-danger">
                        <i class="fi fi-rr-exclamation-triangle me-1"></i>
                        User tidak memiliki OPD yang terdaftar. Silakan hubungi administrator.
                    </div>
                    @endif
                    @endif

                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pendaftaran_dibuka" {{ old('status', $event->status) == 'pendaftaran_dibuka' ? 'selected' : '' }}>Pendaftaran Dibuka</option>
                            <option value="pendaftaran_ditutup" {{ old('status', $event->status) == 'pendaftaran_ditutup' ? 'selected' : '' }}>Pendaftaran Ditutup</option>
                            <option value="pengundian" {{ old('status', $event->status) == 'pengundian' ? 'selected' : '' }}>Pengundian</option>
                            <option value="selesai" {{ old('status', $event->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tgl_mulai" class="form-label">Tanggal & Jam Mulai Pendaftaran</label>
                            <input type="text" class="form-control @error('tgl_mulai') is-invalid @enderror" 
                                   id="tgl_mulai" name="tgl_mulai" 
                                   value="{{ old('tgl_mulai', $event->tgl_mulai ? $event->tgl_mulai->format('Y-m-d H:i') : '') }}" 
                                   placeholder="Pilih tanggal & jam">
                            @error('tgl_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tgl_selesai" class="form-label">Tanggal & Jam Tutup Pendaftaran</label>
                            <input type="text" class="form-control @error('tgl_selesai') is-invalid @enderror" 
                                   id="tgl_selesai" name="tgl_selesai" 
                                   value="{{ old('tgl_selesai', $event->tgl_selesai ? $event->tgl_selesai->format('Y-m-d H:i') : '') }}" 
                                   placeholder="Pilih tanggal & jam">
                            @error('tgl_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                  id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $event->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.event.index') }}" class="btn btn-secondary">
                            <i class="fi fi-rr-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fi fi-rr-check me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
    // Initialize Flatpickr for date time
    flatpickr("#tgl_mulai", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        locale: flatpickr.l10ns.id,
        allowInput: true,
        altInput: false
    });

    flatpickr("#tgl_selesai", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        locale: flatpickr.l10ns.id,
        allowInput: true,
        altInput: false
    });

    @if($canSelectOpd)
    // Initialize Select2 for OPD with AJAX
    $('#opd_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih OPD',
        allowClear: true,
        width: '100%',
        ajax: {
            url: "{{ route('admin.event.opd.data') }}",
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || ''
                };
            },
            processResults: function (data) {
                return {
                    results: data.results
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
    @endif

    // Initialize Select2 for Status
    $('#status').select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih Status',
        allowClear: false,
        width: '100%'
    });
});
</script>
@endpush

