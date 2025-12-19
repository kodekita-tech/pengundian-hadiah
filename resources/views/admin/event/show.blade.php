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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
                <h6 class="card-title mb-0">{{ $event->nm_event }}</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.event.participants.index', $event) }}"
                        class="btn btn-info waves-effect waves-light">
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
                <!-- Event Info Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="card bg-secondary bg-opacity-05 shadow-none border-0">
                            <div class="card-body text-center">
                                <div
                                    class="avatar bg-secondary shadow-secondary rounded-circle text-white mb-2 d-flex align-items-center justify-content-center mx-auto">
                                    <i class="fi fi-rr-calendar-check"></i>
                                </div>
                                <h6 class="text-muted mb-2 small text-uppercase">Status</h6>
                                <span class="badge {{ $event->status_badge_class }} rounded-pill px-3 py-1">
                                    {{ $event->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="card bg-info bg-opacity-05 shadow-none border-0">
                            <div class="card-body text-center">
                                <div
                                    class="avatar bg-info shadow-info rounded-circle text-white mb-2 d-flex align-items-center justify-content-center mx-auto">
                                    <i class="fi fi-rr-building"></i>
                                </div>
                                <h6 class="text-muted mb-2 small text-uppercase">Penyelenggara</h6>
                                <p class="mb-0 fw-bold">{{ $event->opd->nama_penyelenggara ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="card bg-success bg-opacity-05 shadow-none border-0">
                            <div class="card-body text-center">
                                <div
                                    class="avatar bg-success shadow-success rounded-circle text-white mb-2 d-flex align-items-center justify-content-center mx-auto">
                                    <i class="fi fi-rr-calendar"></i>
                                </div>
                                <h6 class="text-muted mb-2 small text-uppercase">Tanggal Mulai</h6>
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
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="card bg-warning bg-opacity-05 shadow-none border-0">
                            <div class="card-body text-center">
                                <div
                                    class="avatar bg-warning shadow-warning rounded-circle text-white mb-2 d-flex align-items-center justify-content-center mx-auto">
                                    <i class="fi fi-rr-clock"></i>
                                </div>
                                <h6 class="text-muted mb-2 small text-uppercase">Tanggal Selesai</h6>
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

                <!-- Status Update Section -->
                <div class="border-top pt-4 mb-4">
                    <div class="mb-3">
                        <label for="statusSelect" class="form-label fw-bold mb-2">Ubah Status Event</label>
                        <form action="{{ route('admin.event.update-status', $event) }}" method="POST" class="d-inline"
                            id="statusForm">
                            @csrf
                            <select name="status" id="statusSelect" class="form-select" style="max-width: 300px;">
                                <option value="aktif" {{ $event->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak_aktif" {{ $event->status == 'tidak_aktif' ? 'selected' : ''
                                    }}>Tidak Aktif</option>
                            </select>
                        </form>
                    </div>
                    <small class="text-muted d-block">
                        <i class="fi fi-rr-info me-1"></i>
                        Status akan otomatis di-update berdasarkan tanggal mulai dan tanggal selesai. "Aktif" jika dalam
                        rentang tanggal, "Tidak Aktif" jika di luar rentang.
                    </small>
                </div>

                <!-- Event Metadata -->
                <div class="border-top pt-4 mb-4">
                    <h6 class="mb-2 fw-bold">Informasi Event</h6>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <p class="mb-1">
                                <strong>Dibuat:</strong>
                                <span class="text-muted">{{ $event->created_at->format('d/m/Y H:i') }}</span>
                            </p>
                        </div>
                        <div class="col-12 col-md-6">
                            <p class="mb-1">
                                <strong>Diperbarui:</strong>
                                <span class="text-muted">{{ $event->updated_at->format('d/m/Y H:i') }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($event->deskripsi)
                <div class="border-top pt-4 mb-4">
                    <h6 class="mb-2 fw-bold">Deskripsi</h6>
                    <p class="text-muted mb-0">{{ $event->deskripsi }}</p>
                </div>
                @endif

                <!-- QR Code Section -->
                @if($event->qr_token)
                <div class="border-top pt-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold">QR Code Pendaftaran</h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-success btn-download-qr"
                                aria-label="Download QR Code" data-bs-toggle="tooltip" title="Download QR Code">
                                <i class="fi fi-rr-download me-1"></i> Download
                            </button>
                        <form action="{{ route('admin.event.regenerate-qr', $event) }}" method="POST"
                            class="d-inline regenerate-qr-form" id="regenerateQrForm">
                            @csrf
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-regenerate-qr"
                                    aria-label="Regenerate QR Code" data-bs-toggle="tooltip" title="Regenerate QR Code">
                                <i class="fi fi-rr-refresh me-1"></i> Regenerate
                            </button>
                        </form>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <div id="qrcode" class="d-flex justify-content-center mb-2"
                                        style="min-height: 200px; align-items: center;"></div>
                                    <small class="text-muted d-block">Scan untuk pendaftaran</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="mb-3">
                                <label for="qrUrlInput" class="form-label small text-muted mb-1">QR Code URL:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="qrUrlInput"
                                        value="{{ url('/qr/' . $event->qr_token) }}" readonly>
                                    <button class="btn btn-outline-secondary btn-copy-qr-url" type="button"
                                        onclick="copyQrUrl()" aria-label="Salin QR Code URL" data-bs-toggle="tooltip"
                                        title="Salin QR Code URL">
                                        <i class="fi fi-rr-copy"></i>
                                        <span class="visually-hidden">Salin</span>
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

                <!-- Draw Page Section -->
                @if($event->shortlink)
                <div class="border-top pt-4 mb-4">
                    <h6 class="mb-3 fw-bold">Halaman Pengundian</h6>
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <div class="mb-3">
                                <label for="shortlinkInput" class="form-label small text-muted mb-1">Shortlink
                                    URL:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="shortlinkInput"
                                        value="{{ url('/d/' . $event->shortlink) }}" readonly>
                                    <button class="btn btn-outline-secondary btn-copy-shortlink" type="button"
                                        onclick="copyShortlink()" aria-label="Salin Shortlink" data-bs-toggle="tooltip"
                                        title="Salin Shortlink">
                                        <i class="fi fi-rr-copy"></i>
                                        <span class="visually-hidden">Salin</span>
                                    </button>
                                    <a href="{{ route('draw.show', $event->shortlink) }}" class="btn btn-primary"
                                        target="_blank" aria-label="Buka Halaman Pengundian" data-bs-toggle="tooltip"
                                        title="Buka Halaman Pengundian">
                                        <i class="fi fi-rr-external-link me-1"></i> Buka Halaman
                                    </a>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
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

                <!-- Prize Management Section -->
                <div class="border-top pt-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold">Daftar Hadiah</h6>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#prizeModal" id="btnAddPrize">
                            <i class="fi fi-rr-plus me-1"></i> Tambah Hadiah
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Hadiah</th>
                                    <th style="width: 150px;">Stok</th>
                                    <th style="width: 150px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="prizeTableBody">
                                @forelse($event->prizes as $prize)
                                <tr data-prize-id="{{ $prize->id }}">
                                    <td>{{ $prize->name }}</td>
                                    <td>
                                        @if($prize->hasStockLimit())
                                        {{ $prize->stock }}
                                        @else
                                        <span class="badge bg-success">Unlimited</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning me-1 btn-edit-prize"
                                            data-id="{{ $prize->id }}" data-name="{{ $prize->name }}"
                                            data-stock="{{ $prize->stock }}"
                                            data-is-unlimited="{{ !$prize->hasStockLimit() ? '1' : '0' }}"
                                            data-bs-toggle="modal" data-bs-target="#prizeModal" aria-label="Edit Hadiah"
                                            title="Edit">
                                            <i class="fi fi-rr-edit"></i>
                                            <span class="visually-hidden">Edit</span>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete-prize"
                                            data-id="{{ $prize->id }}" data-name="{{ $prize->name }}"
                                            aria-label="Hapus Hadiah" title="Hapus">
                                                <i class="fi fi-rr-trash"></i>
                                            <span class="visually-hidden">Hapus</span>
                                            </button>
                                    </td>
                                </tr>
                                @empty
                                <tr id="prizeEmptyRow">
                                    <td colspan="3" class="text-center text-muted">Belum ada hadiah yang ditambahkan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Prize Modal (Single Modal for Add/Edit) -->
                <div class="modal fade" id="prizeModal" tabindex="-1" aria-labelledby="prizeModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="prizeForm">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="prizeModalLabel">Tambah Hadiah Baru</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="prize_id" name="id">
                                    <input type="hidden" name="_token" id="prize_csrf_token" value="{{ csrf_token() }}">
                                    <div class="mb-3">
                                        <label for="prize_name" class="form-label">Nama Hadiah <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="prize_name" name="name"
                                            placeholder="Contoh: Sepeda Gunung" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="unlimitedStock"
                                                name="is_unlimited" value="1" onchange="toggleStockInput('')">
                                            <label class="form-check-label" for="unlimitedStock">Stok Unlimited</label>
                                        </div>
                                        <label for="prize_stock" class="form-label">Jumlah Stok</label>
                                        <input type="number" name="stock" id="prize_stock" class="form-control"
                                            value="1" min="0">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
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
    // Initialize tooltips
    function initTooltips() {
        // Dispose existing tooltips first
        var existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"], [title]');
        existingTooltips.forEach(function(el) {
            var tooltip = bootstrap.Tooltip.getInstance(el);
            if (tooltip) {
                tooltip.dispose();
            }
        });
        
        // Initialize new tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]:not([data-bs-toggle="modal"])'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    initTooltips();
    
    // Re-initialize tooltips after modal is shown/hidden
    $('#prizeModal').on('shown.bs.modal hidden.bs.modal', function() {
        setTimeout(initTooltips, 100);
    });

    // Show toast notification for session messages
    @if(session('success'))
        showToast('success', '{{ session('success') }}');
    @endif

    @if(session('error'))
        showToast('error', '{{ session('error') }}');
    @endif

    // Generate QR Code with responsive size
    @if($event->qr_token)
    function generateQRCode() {
    const qrCodeUrl = "{{ url('/qr/' . $event->qr_token) }}";
    const qrcodeEl = document.getElementById('qrcode');
        
        if (!qrcodeEl) return;
        
        // Check if QRCode library is loaded
        if (typeof QRCode === 'undefined') {
            // Retry after a short delay if library not loaded yet
            setTimeout(generateQRCode, 200);
            return;
        }
        
        // Show loading
        qrcodeEl.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
        
        // Calculate responsive size
        const getQRSize = () => {
            if (window.innerWidth < 576) return 150; // Mobile
            if (window.innerWidth < 768) return 180; // Tablet
            return 200; // Desktop
        };
        
        try {
            const qrSize = getQRSize();
            
            // Clear loading and generate QR Code
            qrcodeEl.innerHTML = '';
            
        new QRCode(qrcodeEl, {
            text: qrCodeUrl,
                width: qrSize,
                height: qrSize,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
            
            // Verify QR code was generated successfully and wait for image to load
            setTimeout(function() {
                const qrImg = qrcodeEl.querySelector('img');
                if (!qrImg && qrcodeEl.children.length === 0) {
                    qrcodeEl.innerHTML = '<div class="alert alert-danger mb-0">Gagal memuat QR Code</div>';
                } else if (qrImg) {
                    // Ensure image is fully loaded before enabling download
                    qrImg.onload = function() {
                        // Image is ready for download
                    };
                    // If image already loaded
                    if (qrImg.complete) {
                        qrImg.onload();
                    }
                }
            }, 300);
        } catch (err) {
            qrcodeEl.innerHTML = '<div class="alert alert-danger mb-0">Gagal memuat QR Code</div>';
            console.error('QR Code generation error:', err);
        }
    }
    
    // Wait for DOM and library to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', generateQRCode);
    } else {
        // DOM already loaded, wait a bit for QRCode library
        setTimeout(generateQRCode, 100);
    }
    @endif

    // Handle download QR Code with border
    $(document).on('click', '.btn-download-qr', function(e) {
        e.preventDefault();
        if (typeof window.downloadQRCode === 'function') {
            window.downloadQRCode();
        } else {
            showToast('error', 'Fungsi download belum siap');
        }
    });

    // Function to download QR Code with border (global function)
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

        // Check if image is loaded
        if (!qrImg.complete || qrImg.naturalWidth === 0) {
            showToast('error', 'QR Code sedang dimuat. Silakan tunggu sebentar.');
            return;
        }

        try {
            // Create canvas for QR code with border
            const borderWidth = 20; // Border width in pixels
            // Use natural width/height for better quality
            const qrSize = qrImg.naturalWidth || qrImg.width || 200;
            const canvasSize = qrSize + (borderWidth * 2);
            
            const canvas = document.createElement('canvas');
            canvas.width = canvasSize;
            canvas.height = canvasSize;
            const ctx = canvas.getContext('2d');

            // Fill white background (border)
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvasSize, canvasSize);

            // Draw QR code in the center
            ctx.drawImage(qrImg, borderWidth, borderWidth, qrSize, qrSize);

            // Convert canvas to blob and download
            canvas.toBlob(function(blob) {
                if (!blob) {
                    showToast('error', 'Gagal membuat file QR Code');
                    return;
                }
                
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                // Clean filename from event name
                const eventName = '{{ $event->nm_event }}'.replace(/[^a-z0-9]/gi, '_').toLowerCase();
                link.download = 'QR-Code-Pendaftaran-' + eventName + '-{{ date("YmdHis") }}.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                showToast('success', 'QR Code berhasil di-download!');
            }, 'image/png', 1.0); // Highest quality
        } catch (err) {
            console.error('Download QR Code error:', err);
            showToast('error', 'Gagal mengunduh QR Code: ' + err.message);
        }
    };

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
                form[0].submit();
            }
        });
    });

    // Handle status update with confirmation
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
                // Reset to original value
                const originalStatus = "{{ $event->status }}";
                $(this).val(originalStatus);
            }
        });
    });

    // Prize Management - Single Modal
    let isEditPrize = false;
    let currentPrizeId = null;

    // Reset prize form
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

    // Open modal for add
    $('#btnAddPrize').on('click', function() {
        resetPrizeForm();
    });

    // Open modal for edit
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

    // Handle prize form submit
    $('#prizeForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        let url, method;
        
        // Get CSRF token - prioritize from meta tag, then hidden input, then blade token
        const csrfToken = $('meta[name="csrf-token"]').attr('content') || 
                          $('#prize_csrf_token').val() || 
                          '{{ csrf_token() }}';
        
        // Always ensure CSRF token is in FormData (override if exists)
        formData.set('_token', csrfToken);
        
        const wasEditMode = isEditPrize;
        const editPrizeId = currentPrizeId;
        
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
                
                // Reset edit state
                isEditPrize = false;
                currentPrizeId = null;
                $('#prizeModalLabel').text('Tambah Hadiah Baru');
                $('#unlimitedStock').prop('checked', false);
                $('#prize_stock').prop('disabled', false);
                
                const prize = response.prize;
                const stockHtml = prize.stock !== null 
                    ? prize.stock 
                    : '<span class="badge bg-success">Unlimited</span>';
                
                if (wasEditMode) {
                    // Update existing row
                    const row = $(`tr[data-prize-id="${prize.id}"]`);
                    row.find('td:first').text(prize.name);
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
                    // Add new row
                    // Remove empty row if exists
                    $('#prizeEmptyRow').remove();
                    
                    const newRow = `
                        <tr data-prize-id="${prize.id}">
                            <td>${prize.name}</td>
                            <td>${stockHtml}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning me-1 btn-edit-prize"
                                    data-id="${prize.id}" data-name="${prize.name}"
                                    data-stock="${prize.stock || ''}"
                                    data-is-unlimited="${prize.stock === null ? '1' : '0'}"
                                    data-bs-toggle="modal" data-bs-target="#prizeModal" aria-label="Edit Hadiah"
                                    title="Edit">
                                    <i class="fi fi-rr-edit"></i>
                                    <span class="visually-hidden">Edit</span>
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete-prize"
                                    data-id="${prize.id}" data-name="${prize.name}"
                                    aria-label="Hapus Hadiah" title="Hapus">
                                    <i class="fi fi-rr-trash"></i>
                                    <span class="visually-hidden">Hapus</span>
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#prizeTableBody').append(newRow);
                }
                
                // Re-initialize tooltips for new/updated buttons
                initTooltips();
                
                showToast('success', response.message || 'Hadiah berhasil disimpan');
            },
            error: function(xhr) {
                if (xhr.status === 419) {
                    // CSRF token mismatch - reload page
                    showToast('error', 'Session expired. Halaman akan di-refresh...');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
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
                            // Try without prefix
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

    // Handle delete prize
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
                    data: {
                        _token: csrfToken,
                        _method: 'DELETE'
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        // Remove row from table
                        row.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Check if table is empty, show empty message
                            if ($('#prizeTableBody tr').length === 0) {
                                $('#prizeTableBody').append(`
                                    <tr id="prizeEmptyRow">
                                        <td colspan="3" class="text-center text-muted">Belum ada hadiah yang ditambahkan</td>
                                    </tr>
                                `);
                            }
                        });
                        
                        showToast('success', response.message || 'Hadiah berhasil dihapus');
                    },
                    error: function(xhr) {
                        if (xhr.status === 419) {
                            showToast('error', 'Session expired. Halaman akan di-refresh...');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            showToast('error', xhr.responseJSON?.message || 'Gagal menghapus hadiah');
                        }
                    }
                });
            }
        });
    });

    // Reset form when modal is closed
    $('#prizeModal').on('hidden.bs.modal', function() {
        resetPrizeForm();
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

// Copy QR URL to clipboard (Modern Clipboard API with fallback)
async function copyQrUrl() {
    const input = document.getElementById('qrUrlInput');
    const text = input.value;
    
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            showToast('success', 'QR Code URL berhasil disalin!');
        } else {
            // Fallback untuk browser lama
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

// Copy Shortlink to clipboard (Modern Clipboard API with fallback)
async function copyShortlink() {
    const input = document.getElementById('shortlinkInput');
    const text = input.value;
    
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            showToast('success', 'Shortlink berhasil disalin!');
        } else {
            // Fallback untuk browser lama
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

// Toggle stock input
function toggleStockInput(id) {
    const checkbox = document.getElementById('unlimitedStock' + (id ? id : ''));
    const input = document.getElementById('prize_stock');
    
    if (checkbox && input) {
    if (checkbox.checked) {
        input.disabled = true;
        input.value = '';
    } else {
        input.disabled = false;
        if (!input.value) {
            input.value = 1;
            }
        }
    }
}
</script>
@endpush