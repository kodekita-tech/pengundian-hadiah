@extends('admin.layouts.app')

@section('title', $event->nm_event)

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.event.index') }}">Events</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            {{ $event->nm_event }}
        </li>
    </ol>
</nav>

<!-- Event Header Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="avatar-lg bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fi fi-rr-calendar text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold">{{ $event->nm_event }}</h4>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge {{ $event->status_badge_class }} rounded-pill px-3">
                                        <i class="fi fi-rr-{{ $event->status == 'aktif' ? 'check' : 'pause' }} me-1"></i>
                                        {{ $event->status_label }}
                                    </span>
                                    @if($event->opd)
                                    <span class="text-muted">
                                        <i class="fi fi-rr-building me-1"></i>
                                        {{ $event->opd->nama_penyelenggara }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($event->deskripsi)
                        <p class="text-muted mb-0">{{ $event->deskripsi }}</p>
                        @endif
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex gap-2 flex-wrap justify-content-md-end">
                            @if($event->shortlink)
                            <a href="{{ route('draw.show', $event->shortlink) }}" 
                               class="btn btn-success waves-effect waves-light" 
                               target="_blank">
                                <i class="fi fi-rr-play me-1"></i> Buka Pengundian
                            </a>
                            @endif
                            <a href="{{ route('admin.event.participants.index', $event) }}"
                                class="btn btn-info waves-effect waves-light">
                                <i class="fi fi-rr-users-alt me-1"></i> Peserta
                            </a>
                            <a href="{{ route('admin.event.edit', $event) }}" 
                               class="btn btn-primary waves-effect waves-light">
                                <i class="fi fi-rr-edit me-1"></i> Edit
                            </a>
                            <a href="{{ route('admin.event.index') }}" 
                               class="btn btn-outline-secondary waves-effect waves-light">
                                <i class="fi fi-rr-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="avatar-md bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                    <i class="fi fi-rr-users text-primary"></i>
                </div>
                <h3 class="mb-0 fw-bold">{{ $stats['participants_count'] }}</h3>
                <small class="text-muted">Total Peserta</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="avatar-md bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                    <i class="fi fi-rr-user-check text-success"></i>
                </div>
                <h3 class="mb-0 fw-bold">{{ $stats['available_participants'] }}</h3>
                <small class="text-muted">Peserta Tersedia</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="avatar-md bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                    <i class="fi fi-rr-gift text-warning"></i>
                </div>
                <h3 class="mb-0 fw-bold">{{ $stats['prizes_count'] }}</h3>
                <small class="text-muted">Total Hadiah</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="avatar-md bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                    <i class="fi fi-rr-trophy text-info"></i>
                </div>
                <h3 class="mb-0 fw-bold">{{ $stats['winners_count'] }}</h3>
                <small class="text-muted">Total Pemenang</small>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Tabs -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                            <i class="fi fi-rr-info me-2"></i>Informasi Event
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="prize-tab" data-bs-toggle="tab" data-bs-target="#prize" type="button" role="tab">
                            <i class="fi fi-rr-gift me-2"></i>Hadiah
                            <span class="badge bg-primary ms-2">{{ $stats['prizes_count'] }}</span>
                        </button>
                    </li>
                    @if($event->qr_token)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="qr-tab" data-bs-toggle="tab" data-bs-target="#qr" type="button" role="tab">
                            <i class="fi fi-rr-qrcode me-2"></i>QR Code
                        </button>
                    </li>
                    @endif
                    @if($event->shortlink)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="draw-tab" data-bs-toggle="tab" data-bs-target="#draw" type="button" role="tab">
                            <i class="fi fi-rr-play me-2"></i>Pengundian
                        </button>
                    </li>
                    @endif
                </ul>

                <!-- Tab Content -->
                <div class="tab-content p-4">
                    <!-- Info Tab -->
                    <div class="tab-pane fade show active" id="info" role="tabpanel">
                        <div class="row g-4">
                            <!-- Event Details -->
                            <div class="col-12 col-md-6">
                                <h6 class="fw-bold mb-3 text-primary">
                                    <i class="fi fi-rr-calendar me-2"></i>Detail Event
                                </h6>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0 border-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <small class="text-muted d-block mb-1">Tanggal Mulai</small>
                                                <p class="mb-0 fw-semibold">
                                                    @if($event->tgl_mulai)
                                                        {{ $event->tgl_mulai->format('d F Y, H:i') }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <i class="fi fi-rr-calendar-check text-success"></i>
                                        </div>
                                    </div>
                                    <div class="list-group-item px-0 border-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <small class="text-muted d-block mb-1">Tanggal Selesai</small>
                                                <p class="mb-0 fw-semibold">
                                                    @if($event->tgl_selesai)
                                                        {{ $event->tgl_selesai->format('d F Y, H:i') }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <i class="fi fi-rr-clock text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="list-group-item px-0 border-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <small class="text-muted d-block mb-1">Status</small>
                                                <span class="badge {{ $event->status_badge_class }} rounded-pill px-3">
                                                    {{ $event->status_label }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Event Metadata -->
                            <div class="col-12 col-md-6">
                                <h6 class="fw-bold mb-3 text-primary">
                                    <i class="fi fi-rr-info me-2"></i>Metadata
                                </h6>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0 border-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <small class="text-muted d-block mb-1">Dibuat</small>
                                                <p class="mb-0 fw-semibold">{{ $event->created_at->format('d F Y, H:i') }}</p>
                                            </div>
                                            <i class="fi fi-rr-time-past text-info"></i>
                                        </div>
                                    </div>
                                    <div class="list-group-item px-0 border-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <small class="text-muted d-block mb-1">Diperbarui</small>
                                                <p class="mb-0 fw-semibold">{{ $event->updated_at->format('d F Y, H:i') }}</p>
                                            </div>
                                            <i class="fi fi-rr-time-forward text-primary"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Update -->
                                <div class="mt-4 pt-3 border-top">
                                    <label for="statusSelect" class="form-label fw-bold mb-2">Ubah Status Event</label>
                                    <form action="{{ route('admin.event.update-status', $event) }}" method="POST" class="d-inline" id="statusForm">
                                        @csrf
                                        <select name="status" id="statusSelect" class="form-select">
                                            <option value="aktif" {{ $event->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="tidak_aktif" {{ $event->status == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                        </select>
                                    </form>
                                    <small class="text-muted d-block mt-2">
                                        <i class="fi fi-rr-info me-1"></i>
                                        Status akan otomatis di-update berdasarkan tanggal mulai dan tanggal selesai.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prize Tab -->
                    <div class="tab-pane fade" id="prize" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold">Daftar Hadiah</h6>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#prizeModal" id="btnAddPrize">
                                <i class="fi fi-rr-plus me-1"></i> Tambah Hadiah
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Hadiah</th>
                                        <th style="width: 150px;">Stok</th>
                                        <th style="width: 120px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="prizeTableBody">
                                    @forelse($event->prizes as $prize)
                                    <tr data-prize-id="{{ $prize->id }}">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fi fi-rr-gift text-primary"></i>
                                                <span class="fw-semibold">{{ $prize->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($prize->hasStockLimit())
                                                <span class="badge bg-info">{{ $prize->stock }} tersisa</span>
                                            @else
                                                <span class="badge bg-success">Unlimited</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-outline-warning btn-edit-prize"
                                                    data-id="{{ $prize->id }}" 
                                                    data-name="{{ $prize->name }}"
                                                    data-stock="{{ $prize->stock }}"
                                                    data-is-unlimited="{{ !$prize->hasStockLimit() ? '1' : '0' }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#prizeModal"
                                                    title="Edit">
                                                    <i class="fi fi-rr-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-delete-prize"
                                                    data-id="{{ $prize->id }}" 
                                                    data-name="{{ $prize->name }}"
                                                    title="Hapus">
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr id="prizeEmptyRow">
                                        <td colspan="3" class="text-center text-muted py-5">
                                            <i class="fi fi-rr-gift mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <p class="mb-0">Belum ada hadiah yang ditambahkan</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- QR Code Tab -->
                    @if($event->qr_token)
                    <div class="tab-pane fade" id="qr" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-12 col-md-5">
                                <div class="card border">
                                    <div class="card-body text-center p-4">
                                        <div id="qrcode" class="d-flex justify-content-center mb-3" style="min-height: 250px; align-items: center;"></div>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-success btn-sm btn-download-qr">
                                                <i class="fi fi-rr-download me-1"></i> Download
                                            </button>
                                            <form action="{{ route('admin.event.regenerate-qr', $event) }}" method="POST" class="d-inline regenerate-qr-form">
                                                @csrf
                                                <button type="button" class="btn btn-outline-secondary btn-sm btn-regenerate-qr">
                                                    <i class="fi fi-rr-refresh me-1"></i> Regenerate
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-7">
                                <h6 class="fw-bold mb-3">QR Code Pendaftaran</h6>
                                <div class="mb-3">
                                    <label for="qrUrlInput" class="form-label small text-muted mb-1">URL:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="qrUrlInput" value="{{ url('/qr/' . $event->qr_token) }}" readonly>
                                        <button class="btn btn-outline-primary btn-copy-qr-url" type="button" onclick="copyQrUrl()">
                                            <i class="fi fi-rr-copy me-1"></i> Salin
                                        </button>
                                    </div>
                                </div>
                                <div class="alert alert-info mb-0">
                                    <i class="fi fi-rr-info me-2"></i>
                                    <small>Gunakan QR code atau URL ini untuk pendaftaran peserta. Scan QR code atau bagikan URL kepada peserta.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Draw Page Tab -->
                    @if($event->shortlink)
                    <div class="tab-pane fade" id="draw" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Halaman Pengundian</h6>
                                <div class="mb-3">
                                    <label for="shortlinkInput" class="form-label small text-muted mb-1">Shortlink URL:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="shortlinkInput" value="{{ url('/d/' . $event->shortlink) }}" readonly>
                                        <button class="btn btn-outline-primary btn-copy-shortlink" type="button" onclick="copyShortlink()">
                                            <i class="fi fi-rr-copy me-1"></i> Salin
                                        </button>
                                        <a href="{{ route('draw.show', $event->shortlink) }}" class="btn btn-success" target="_blank">
                                            <i class="fi fi-rr-external-link me-1"></i> Buka Halaman
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if($event->hasPasskey())
                                    <span class="badge bg-warning text-dark">
                                        <i class="fi fi-rr-lock me-1"></i> Protected with Passkey
                                    </span>
                                    @else
                                    <span class="badge bg-success">
                                        <i class="fi fi-rr-unlock me-1"></i> Public Access
                                    </span>
                                    @endif
                                    <small class="text-muted">
                                        <i class="fi fi-rr-info me-1"></i>
                                        Gunakan shortlink ini untuk mengakses halaman pengundian
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Prize Modal -->
<div class="modal fade" id="prizeModal" tabindex="-1" aria-labelledby="prizeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="prizeForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="prizeModalLabel">Tambah Hadiah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="prize_id" name="id">
                    <input type="hidden" name="_token" id="prize_csrf_token" value="{{ csrf_token() }}">
                    <div class="mb-3">
                        <label for="prize_name" class="form-label">Nama Hadiah <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="prize_name" name="name" placeholder="Contoh: Sepeda Gunung" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="unlimitedStock" name="is_unlimited" value="1" onchange="toggleStockInput('')">
                            <label class="form-check-label" for="unlimitedStock">Stok Unlimited</label>
                        </div>
                        <label for="prize_stock" class="form-label">Jumlah Stok</label>
                        <input type="number" name="stock" id="prize_stock" class="form-control" value="1" min="0">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
<style>
    .nav-tabs-custom .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        padding: 1rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s;
    }
    .nav-tabs-custom .nav-link:hover {
        border-bottom-color: #e9ecef;
        color: #495057;
    }
    .nav-tabs-custom .nav-link.active {
        border-bottom-color: #0d6efd;
        color: #0d6efd;
        background: transparent;
    }
    .avatar-lg {
        width: 80px;
        height: 80px;
    }
    .avatar-md {
        width: 60px;
        height: 60px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize tooltips
    function initTooltips() {
        var existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"], [title]');
        existingTooltips.forEach(function(el) {
            var tooltip = bootstrap.Tooltip.getInstance(el);
            if (tooltip) tooltip.dispose();
        });
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]:not([data-bs-toggle="modal"])'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    initTooltips();
    
    $('#prizeModal').on('shown.bs.modal hidden.bs.modal', function() {
        setTimeout(initTooltips, 100);
    });

    // Show toast notification
    @if(session('success'))
        showToast('success', '{{ session('success') }}');
    @endif
    @if(session('error'))
        showToast('error', '{{ session('error') }}');
    @endif

    // Generate QR Code
    @if($event->qr_token)
    function generateQRCode() {
        const qrCodeUrl = "{{ url('/qr/' . $event->qr_token) }}";
        const qrcodeEl = document.getElementById('qrcode');
        if (!qrcodeEl) return;
        
        if (typeof QRCode === 'undefined') {
            setTimeout(generateQRCode, 200);
            return;
        }
        
        qrcodeEl.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
        
        const getQRSize = () => {
            if (window.innerWidth < 576) return 150;
            if (window.innerWidth < 768) return 180;
            return 250;
        };
        
        try {
            const qrSize = getQRSize();
            qrcodeEl.innerHTML = '';
            new QRCode(qrcodeEl, {
                text: qrCodeUrl,
                width: qrSize,
                height: qrSize,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
            
            setTimeout(function() {
                const qrImg = qrcodeEl.querySelector('img');
                if (!qrImg && qrcodeEl.children.length === 0) {
                    qrcodeEl.innerHTML = '<div class="alert alert-danger mb-0">Gagal memuat QR Code</div>';
                } else if (qrImg) {
                    qrImg.onload = function() {};
                    if (qrImg.complete) qrImg.onload();
                }
            }, 300);
        } catch (err) {
            qrcodeEl.innerHTML = '<div class="alert alert-danger mb-0">Gagal memuat QR Code</div>';
            console.error('QR Code generation error:', err);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', generateQRCode);
    } else {
        setTimeout(generateQRCode, 100);
    }
    @endif

    // Download QR Code
    $(document).on('click', '.btn-download-qr', function(e) {
        e.preventDefault();
        if (typeof window.downloadQRCode === 'function') {
            window.downloadQRCode();
        } else {
            showToast('error', 'Fungsi download belum siap');
        }
    });

    window.downloadQRCode = function() {
        const qrcodeEl = document.getElementById('qrcode');
        if (!qrcodeEl) {
            showToast('error', 'QR Code belum dimuat');
            return;
        }
        const qrImg = qrcodeEl.querySelector('img');
        if (!qrImg) {
            showToast('error', 'QR Code belum siap untuk di-download. Silakan tunggu sebentar.');
            return;
        }
        if (!qrImg.complete || qrImg.naturalWidth === 0) {
            showToast('error', 'QR Code sedang dimuat. Silakan tunggu sebentar.');
            return;
        }
        try {
            const borderWidth = 20;
            const qrSize = qrImg.naturalWidth || qrImg.width || 200;
            const canvasSize = qrSize + (borderWidth * 2);
            const canvas = document.createElement('canvas');
            canvas.width = canvasSize;
            canvas.height = canvasSize;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvasSize, canvasSize);
            ctx.drawImage(qrImg, borderWidth, borderWidth, qrSize, qrSize);
            canvas.toBlob(function(blob) {
                if (!blob) {
                    showToast('error', 'Gagal membuat file QR Code');
                    return;
                }
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                const eventName = '{{ $event->nm_event }}'.replace(/[^a-z0-9]/gi, '_').toLowerCase();
                link.download = 'QR-Code-Pendaftaran-' + eventName + '-{{ date("YmdHis") }}.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                showToast('success', 'QR Code berhasil di-download!');
            }, 'image/png', 1.0);
        } catch (err) {
            console.error('Download QR Code error:', err);
            showToast('error', 'Gagal mengunduh QR Code: ' + err.message);
        }
    };

    // Regenerate QR
    $(document).on('click', '.btn-regenerate-qr', function(e) {
        e.preventDefault();
        const form = $('.regenerate-qr-form');
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
                form[0].submit();
            }
        });
    });

    // Status update
    $('#statusSelect').on('change', function(e) {
        e.preventDefault();
        const form = $('#statusForm');
        const selectedStatus = $(this).val();
        const statusText = selectedStatus === 'aktif' ? 'Aktif' : 'Tidak Aktif';
        Swal.fire({
            title: 'Ubah Status Event?',
            text: `Status akan diubah menjadi "${statusText}". Apakah Anda yakin?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, ubah status!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form[0].submit();
            } else {
                const originalStatus = "{{ $event->status }}";
                $(this).val(originalStatus);
            }
        });
    });

    // Prize Management
    let isEditPrize = false;
    let currentPrizeId = null;

    function resetPrizeForm() {
        $('#prizeForm')[0].reset();
        $('#prize_id').val('');
        $('#prizeModalLabel').text('Tambah Hadiah Baru');
        $('.invalid-feedback').text('');
        $('.form-control').removeClass('is-invalid');
        isEditPrize = false;
        currentPrizeId = null;
        $('#unlimitedStock').prop('checked', false);
        $('#prize_stock').val('1').prop('disabled', false);
    }

    $('#btnAddPrize').on('click', function() {
        resetPrizeForm();
    });

    $(document).on('click', '.btn-edit-prize', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const stock = $(this).data('stock');
        const isUnlimited = $(this).data('is-unlimited') == '1';
        
        isEditPrize = true;
        currentPrizeId = id;
        $('#prize_id').val(id);
        $('#prize_name').val(name);
        $('#prize_stock').val(stock);
        $('#unlimitedStock').prop('checked', isUnlimited);
        $('#prize_stock').prop('disabled', isUnlimited);
        $('#prizeModalLabel').text('Edit Hadiah');
        $('.invalid-feedback').text('');
        $('.form-control').removeClass('is-invalid');
    });

    $('#prizeForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const csrfToken = $('meta[name="csrf-token"]').attr('content') || $('#prize_csrf_token').val() || '{{ csrf_token() }}';
        formData.set('_token', csrfToken);
        const wasEditMode = isEditPrize;
        const editPrizeId = currentPrizeId;
        
        let url, method;
        if (isEditPrize) {
            url = "{{ route('admin.event.prizes.update', [$event, ':id']) }}".replace(':id', currentPrizeId);
            method = 'POST';
            formData.append('_method', 'PUT');
        } else {
            url = "{{ route('admin.event.prizes.store', $event) }}";
            method = 'POST';
        }

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#prizeModal').modal('hide');
                $('#prizeForm')[0].reset();
                isEditPrize = false;
                currentPrizeId = null;
                $('#prizeModalLabel').text('Tambah Hadiah Baru');
                $('#unlimitedStock').prop('checked', false);
                $('#prize_stock').prop('disabled', false);
                
                const prize = response.prize;
                const stockHtml = prize.stock !== null 
                    ? `<span class="badge bg-info">${prize.stock} tersisa</span>`
                    : '<span class="badge bg-success">Unlimited</span>';
                
                if (wasEditMode) {
                    const row = $(`tr[data-prize-id="${prize.id}"]`);
                    row.find('td:first').html(`<div class="d-flex align-items-center gap-2"><i class="fi fi-rr-gift text-primary"></i><span class="fw-semibold">${prize.name}</span></div>`);
                    row.find('td:eq(1)').html(stockHtml);
                    row.find('.btn-edit-prize')
                        .attr('data-id', prize.id)
                        .attr('data-name', prize.name)
                        .attr('data-stock', prize.stock || '')
                        .attr('data-is-unlimited', prize.stock === null ? '1' : '0');
                    row.find('.btn-delete-prize')
                        .attr('data-id', prize.id)
                        .attr('data-name', prize.name);
                } else {
                    $('#prizeEmptyRow').remove();
                    const newRow = `
                        <tr data-prize-id="${prize.id}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fi fi-rr-gift text-primary"></i>
                                    <span class="fw-semibold">${prize.name}</span>
                                </div>
                            </td>
                            <td>${stockHtml}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-warning btn-edit-prize"
                                        data-id="${prize.id}" data-name="${prize.name}"
                                        data-stock="${prize.stock || ''}"
                                        data-is-unlimited="${prize.stock === null ? '1' : '0'}"
                                        data-bs-toggle="modal" data-bs-target="#prizeModal" title="Edit">
                                        <i class="fi fi-rr-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-delete-prize"
                                        data-id="${prize.id}" data-name="${prize.name}" title="Hapus">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    $('#prizeTableBody').append(newRow);
                }
                initTooltips();
                showToast('success', response.message || 'Hadiah berhasil disimpan');
            },
            error: function(xhr) {
                if (xhr.status === 419) {
                    showToast('error', 'Session expired. Halaman akan di-refresh...');
                    setTimeout(function() { location.reload(); }, 1500);
                } else if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $('.invalid-feedback').text('');
                    $('.form-control').removeClass('is-invalid');
                    $.each(errors, function(key, value) {
                        const input = $('#prize_' + key);
                        if (input.length) {
                            input.addClass('is-invalid');
                            input.siblings('.invalid-feedback').text(value[0]);
                        } else {
                            const inputAlt = $('#' + key);
                            if (inputAlt.length) {
                                inputAlt.addClass('is-invalid');
                                inputAlt.siblings('.invalid-feedback').text(value[0]);
                            }
                        }
                    });
                } else {
                    showToast('error', xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            }
        });
    });

    $(document).on('click', '.btn-delete-prize', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const row = $(this).closest('tr');
        Swal.fire({
            title: 'Hapus Hadiah?',
            text: `Yakin ingin menghapus hadiah "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
                $.ajax({
                    url: "{{ route('admin.event.prizes.destroy', [$event, ':id']) }}".replace(':id', id),
                    method: 'POST',
                    data: { _token: csrfToken, _method: 'DELETE' },
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(response) {
                        row.fadeOut(300, function() {
                            $(this).remove();
                            if ($('#prizeTableBody tr').length === 0) {
                                $('#prizeTableBody').append(`
                                    <tr id="prizeEmptyRow">
                                        <td colspan="3" class="text-center text-muted py-5">
                                            <i class="fi fi-rr-gift mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <p class="mb-0">Belum ada hadiah yang ditambahkan</p>
                                        </td>
                                    </tr>
                                `);
                            }
                        });
                        showToast('success', response.message || 'Hadiah berhasil dihapus');
                    },
                    error: function(xhr) {
                        if (xhr.status === 419) {
                            showToast('error', 'Session expired. Halaman akan di-refresh...');
                            setTimeout(function() { location.reload(); }, 1500);
                        } else {
                            showToast('error', xhr.responseJSON?.message || 'Gagal menghapus hadiah');
                        }
                    }
                });
            }
        });
    });

    $('#prizeModal').on('hidden.bs.modal', function() {
        resetPrizeForm();
    });
});

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

async function copyQrUrl() {
    const input = document.getElementById('qrUrlInput');
    const text = input.value;
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            showToast('success', 'QR Code URL berhasil disalin!');
        } else {
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand('copy');
            showToast('success', 'QR Code URL berhasil disalin!');
        }
    } catch (err) {
        showToast('error', 'Gagal menyalin QR Code URL');
        console.error('Copy error:', err);
    }
}

async function copyShortlink() {
    const input = document.getElementById('shortlinkInput');
    const text = input.value;
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            showToast('success', 'Shortlink berhasil disalin!');
        } else {
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand('copy');
            showToast('success', 'Shortlink berhasil disalin!');
        }
    } catch (err) {
        showToast('error', 'Gagal menyalin Shortlink');
        console.error('Copy error:', err);
    }
}

function toggleStockInput(id) {
    const checkbox = document.getElementById('unlimitedStock' + (id ? id : ''));
    const input = document.getElementById('prize_stock');
    if (checkbox && input) {
        if (checkbox.checked) {
            input.disabled = true;
            input.value = '';
        } else {
            input.disabled = false;
            if (!input.value) input.value = 1;
        }
    }
}
</script>
@endpush
