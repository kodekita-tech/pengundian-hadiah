@extends('admin.layouts.app')

@section('title', $event->nm_event)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
                <h6 class="card-title mb-0">{{ $event->nm_event }}</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.event.participants.index', $event) }}" class="btn btn-info waves-effect waves-light">
                        <i class="fi fi-rr-users-alt me-1"></i> Peserta
                    </a>
                    <a href="{{ route('admin.event.edit', $event) }}" class="btn btn-primary waves-effect waves-light">
                        <i class="fi fi-rr-edit me-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.event.index') }}" class="btn btn-secondary waves-effect waves-light">
                        <i class="fi fi-rr-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">

                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2 text-uppercase" style="font-size: 0.75rem;">Status</h6>
                                <span class="badge {{ $event->status_badge_class }} rounded-pill px-3 py-1">
                                    {{ $event->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2 text-uppercase" style="font-size: 0.75rem;">OPD</h6>
                                <p class="mb-0 fw-bold">{{ $event->opd->nama_instansi ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2 text-uppercase" style="font-size: 0.75rem;">Tanggal Mulai
                                </h6>
                                <p class="mb-0">
                                    @if($event->tgl_mulai)
                                    {{ $event->tgl_mulai->format('d/m/Y H:i') }}
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2 text-uppercase" style="font-size: 0.75rem;">Tanggal Selesai
                                </h6>
                                <p class="mb-0">
                                    @if($event->tgl_selesai)
                                    {{ $event->tgl_selesai->format('d/m/Y H:i') }}
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($event->deskripsi)
                <div class="mb-4">
                    <h6 class="mb-2">Deskripsi</h6>
                    <p class="text-muted">{{ $event->deskripsi }}</p>
                </div>
                @endif

                @if($event->qr_token)
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">QR Code</h6>
                        <form action="{{ route('admin.event.regenerate-qr', $event) }}" method="POST"
                            class="d-inline regenerate-qr-form" id="regenerateQrForm">
                            @csrf
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-regenerate-qr">
                                <i class="fi fi-rr-refresh me-1"></i> Regenerate
                            </button>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <div id="qrcode" class="d-flex justify-content-center mb-2"></div>
                                    <small class="text-muted d-block">Scan untuk pendaftaran</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">QR Token:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="qrTokenInput"
                                        value="{{ $event->qr_token }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyQrToken()"
                                        title="Copy">
                                        <i class="fi fi-rr-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">QR Code URL:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="qrUrlInput"
                                        value="{{ url('/qr/' . $event->qr_token) }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyQrUrl()"
                                        title="Copy">
                                        <i class="fi fi-rr-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fi fi-rr-info me-1"></i>
                                Gunakan QR code atau URL ini untuk pendaftaran peserta
                            </small>
                        </div>
                    </div>
                </div>
                @endif

                @if($event->shortlink)
                <div class="mb-4">
                    <h6 class="mb-3">Draw Page Access</h6>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Shortlink URL:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="shortlinkInput"
                                        value="{{ url('/d/' . $event->shortlink) }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyShortlink()"
                                        title="Copy">
                                        <i class="fi fi-rr-copy"></i>
                                    </button>
                                    <a href="{{ route('draw.show', $event->shortlink) }}" 
                                       class="btn btn-primary" target="_blank" title="Open Draw Page">
                                        <i class="fi fi-rr-external-link me-1"></i> Open Draw Page
                                    </a>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <small class="text-muted">
                                    <i class="fi fi-rr-info me-1"></i>
                                    Gunakan shortlink ini untuk mengakses halaman pengundian
                                </small>
                                @if($event->hasPasskey())
                                    <span class="badge bg-warning text-dark">
                                        <i class="fi fi-rr-lock me-1"></i> Protected with Passkey
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fi fi-rr-unlock me-1"></i> Public Access
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mb-4">
                    <h6 class="mb-2">Informasi Event</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Dibuat:</strong> {{ $event->created_at->format('d/m/Y H:i') }}
                        </li>
                        <li class="mb-2">
                            <strong>Diperbarui:</strong> {{ $event->updated_at->format('d/m/Y H:i') }}
                        </li>
                    </ul>
                </div>

                <div class="d-flex gap-2">
                    <form action="{{ route('admin.event.update-status', $event) }}" method="POST" class="d-inline">
                        @csrf
                        <div class="input-group" style="width: 300px;">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="draft" {{ $event->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="pendaftaran_dibuka" {{ $event->status == 'pendaftaran_dibuka' ?
                                    'selected' : '' }}>Pendaftaran Dibuka</option>
                                <option value="pendaftaran_ditutup" {{ $event->status == 'pendaftaran_ditutup' ?
                                    'selected' : '' }}>Pendaftaran Ditutup</option>
                                <option value="pengundian" {{ $event->status == 'pengundian' ? 'selected' : ''
                                    }}>Pengundian</option>
                                <option value="selesai" {{ $event->status == 'selesai' ? 'selected' : '' }}>Selesai
                                </option>
                            </select>
                        </div>
                    </form>
                </div>
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
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    $(document).ready(function() {
    // Show toast notification for session messages
    @if(session('success'))
        showToast('success', '{{ session('success') }}');
    @endif

    @if(session('error'))
        showToast('error', '{{ session('error') }}');
    @endif

    // Generate QR Code
    @if($event->qr_token)
    const qrCodeUrl = "{{ url('/qr/' . $event->qr_token) }}";
    const qrcodeEl = document.getElementById('qrcode');
    if (qrcodeEl) {
        new QRCode(qrcodeEl, {
            text: qrCodeUrl,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }
    @endif

    // Handle regenerate QR with SweetAlert2
    $(document).on('click', '.btn-regenerate-qr', function(e) {
        e.preventDefault();
        const form = $('#regenerateQrForm');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Token lama tidak akan bisa digunakan lagi setelah regenerate.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, regenerate!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form secara langsung
                form[0].submit();
            }
        });
    });
});

// Show toast notification function (global)
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

// Copy QR Token to clipboard
function copyQrToken() {
    const input = document.getElementById('qrTokenInput');
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    document.execCommand('copy');
    showToast('success', 'QR Token berhasil disalin!');
}

// Copy QR URL to clipboard
function copyQrUrl() {
    const input = document.getElementById('qrUrlInput');
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    document.execCommand('copy');
    showToast('success', 'QR URL berhasil disalin!');
}

// Copy Shortlink to clipboard
function copyShortlink() {
    const input = document.getElementById('shortlinkInput');
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    document.execCommand('copy');
    showToast('success', 'Shortlink berhasil disalin!');
}
</script>
@endpush