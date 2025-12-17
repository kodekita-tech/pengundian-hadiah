@extends('admin.layouts.app')

@section('title', $event->nm_event)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-1 fw-bold">{{ $event->nm_event }}</h4>
                <div class="d-flex align-items-center gap-3 flex-wrap text-muted small">
                    <span>
                        <i class="fi fi-rr-building me-1"></i>
                        {{ $event->opd->nama_penyelenggara ?? '-' }}
                    </span>
                    @if($event->tgl_mulai && $event->tgl_selesai)
                    <span>
                        <i class="fi fi-rr-calendar me-1"></i>
                        {{ $event->tgl_mulai->format('d/m/Y') }} - {{ $event->tgl_selesai->format('d/m/Y') }}
                    </span>
                    @endif
                    <span class="badge {{ $event->status_badge_class }} rounded-pill px-2 py-1">
                        {{ $event->status_label }}
                    </span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.event.index') }}" class="btn btn-secondary waves-effect waves-light">
                    <i class="fi fi-rr-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('admin.event.participants.index', $event) }}"
                    class="btn btn-info waves-effect waves-light">
                    <i class="fi fi-rr-users-alt me-1"></i> Peserta
                </a>
                <a href="{{ route('admin.event.edit', $event) }}" class="btn btn-primary waves-effect waves-light">
                    <i class="fi fi-rr-edit me-1"></i> Edit
                </a>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-6 col-md-3 mb-3">
                <div class="card bg-primary bg-opacity-05 shadow-none border-0">
                    <div class="card-body">
                        <div class="avatar bg-primary shadow-primary rounded-circle text-white mb-3">
                            <i class="fi fi-rr-users-alt"></i>
                        </div>
                        <h3 class="mb-1">{{ $event->participants()->count() }}</h3>
                        <h6 class="mb-0">Total Peserta</h6>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card bg-success bg-opacity-05 shadow-none border-0">
                    <div class="card-body">
                        <div class="avatar bg-success shadow-success rounded-circle text-white mb-3">
                            <i class="fi fi-rr-trophy"></i>
                        </div>
                        <h3 class="mb-1">{{ $event->prizes()->count() }}</h3>
                        <h6 class="mb-0">Total Hadiah</h6>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card bg-warning bg-opacity-05 shadow-none border-0">
                    <div class="card-body">
                        <div class="avatar bg-warning shadow-warning rounded-circle text-white mb-3">
                            <i class="fi fi-rr-star"></i>
                        </div>
                        <h3 class="mb-1">{{ $event->winners()->count() }}</h3>
                        <h6 class="mb-0">Pemenang</h6>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card bg-info bg-opacity-05 shadow-none border-0">
                    <div class="card-body">
                        <div class="avatar bg-info shadow-info rounded-circle text-white mb-3">
                            <i class="fi fi-rr-calendar"></i>
                        </div>
                        <h3 class="mb-1">
                            @if($event->tgl_mulai && $event->tgl_selesai)
                            {{ \Carbon\Carbon::parse($event->tgl_mulai)->diffInDays($event->tgl_selesai) + 1 }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </h3>
                        <h6 class="mb-0">Periode (Hari)</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content Card with Tabs --}}
        <div class="card mb-5">
            <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overview" type="button"
                            role="tab">
                            <i class="fi fi-rr-info me-1"></i> Overview
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#registration" type="button"
                            role="tab">
                            <i class="fi fi-rr-qrcode me-1"></i> Pendaftaran
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#drawing" type="button"
                            role="tab">
                            <i class="fi fi-rr-dice me-1"></i> Pengundian
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#prizes" type="button" role="tab">
                            <i class="fi fi-rr-gift me-1"></i> Hadiah
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body pt-3">
                <div class="tab-content">
                    {{-- Overview Tab --}}
                    <div class="tab-pane fade show active" id="overview" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label text-muted small mb-2">Tanggal Mulai</label>
                                <p class="mb-0 fw-semibold">
                                    @if($event->tgl_mulai)
                                    <i class="fi fi-rr-calendar me-1 text-primary"></i>
                                    {{ $event->tgl_mulai->format('d F Y, H:i') }}
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label text-muted small mb-2">Tanggal Selesai</label>
                                <p class="mb-0 fw-semibold">
                                    @if($event->tgl_selesai)
                                    <i class="fi fi-rr-calendar me-1 text-primary"></i>
                                    {{ $event->tgl_selesai->format('d F Y, H:i') }}
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($event->deskripsi)
                        <div class="mb-4">
                            <label class="form-label text-muted small mb-2">Deskripsi</label>
                            <p class="mb-0">{{ $event->deskripsi }}</p>
                        </div>
                        @endif

                        <div class="border-top pt-4">
                            <label class="form-label text-muted small mb-3">Informasi Event</label>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <p class="mb-0 small">
                                        <span class="text-muted">Dibuat:</span>
                                        <span class="fw-semibold ms-2">{{ $event->created_at->format('d/m/Y H:i')
                                            }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <p class="mb-0 small">
                                        <span class="text-muted">Diperbarui:</span>
                                        <span class="fw-semibold ms-2">{{ $event->updated_at->format('d/m/Y H:i')
                                            }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-4 mt-4">
                            <label class="form-label text-muted small mb-2">Ubah Status Event</label>
                            <form action="{{ route('admin.event.update-status', $event) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <div class="input-group" style="max-width: 300px;">
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="aktif" {{ $event->status == 'aktif' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="tidak_aktif" {{ $event->status == 'tidak_aktif' ? 'selected' : ''
                                            }}>Tidak Aktif</option>
                                    </select>
                                </div>
                            </form>
                            <small class="text-muted d-block mt-2">
                                <i class="fi fi-rr-info me-1"></i>
                                Status akan otomatis di-update berdasarkan tanggal mulai dan tanggal selesai.
                            </small>
                        </div>
                    </div>

                    {{-- Registration Tab --}}
                    <div class="tab-pane fade" id="registration" role="tabpanel">
                        @if($event->qr_token)
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h6 class="mb-1 fw-bold">QR Code Pendaftaran</h6>
                                <small class="text-muted">Gunakan QR code atau URL untuk pendaftaran peserta</small>
                            </div>
                            <form action="{{ route('admin.event.regenerate-qr', $event) }}" method="POST"
                                class="d-inline" id="regenerateQrForm">
                                @csrf
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-regenerate-qr">
                                    <i class="fi fi-rr-refresh me-1"></i> Regenerate
                                </button>
                            </form>
                        </div>
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div id="qrcode" class="d-flex justify-content-center mb-2"></div>
                                        <small class="text-muted d-block">Scan untuk pendaftaran</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">QR Code URL:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="qrUrlInput"
                                            value="{{ url('/qr/' . $event->qr_token) }}" readonly>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyQrUrl()"
                                            title="Copy">
                                            <i class="fi fi-rr-copy"></i>
                                        </button>
                                        <a href="{{ url('/qr/' . $event->qr_token) }}" class="btn btn-primary"
                                            target="_blank" title="Buka URL">
                                            <i class="fi fi-rr-external-link me-1"></i> Buka
                                        </a>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <i class="fi fi-rr-info me-1"></i>
                                    Gunakan QR code atau URL ini untuk pendaftaran peserta
                                </small>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="fi fi-rr-qrcode mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="text-muted mb-0">QR Code belum dibuat. Silakan edit event untuk membuat QR Code.
                            </p>
                        </div>
                        @endif
                    </div>

                    {{-- Drawing Tab --}}
                    <div class="tab-pane fade" id="drawing" role="tabpanel">
                        @if($event->shortlink)
                        <div class="mb-4">
                            <h6 class="mb-1 fw-bold">Halaman Pengundian</h6>
                            <small class="text-muted">Shortlink untuk mengakses halaman pengundian</small>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small text-muted mb-1">Shortlink URL:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="shortlinkInput"
                                    value="{{ url('/d/' . $event->shortlink) }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="copyShortlink()"
                                    title="Copy">
                                    <i class="fi fi-rr-copy"></i>
                                </button>
                                <a href="{{ route('draw.show', $event->shortlink) }}" class="btn btn-primary"
                                    target="_blank" title="Open Draw Page">
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
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="fi fi-rr-dice mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="text-muted mb-0">Shortlink belum dibuat. Silakan edit event untuk membuat
                                shortlink.</p>
                        </div>
                        @endif
                    </div>

                    {{-- Prizes Tab --}}
                    <div class="tab-pane fade" id="prizes" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h6 class="mb-1 fw-bold">Daftar Hadiah</h6>
                                <small class="text-muted">Kelola hadiah untuk event ini</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addPrizeModal">
                                <i class="fi fi-rr-plus me-1"></i> Tambah Hadiah
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama Hadiah</th>
                                        <th style="width: 150px;">Stok</th>
                                        <th style="width: 150px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($event->prizes as $prize)
                                    <tr>
                                        <td>{{ $prize->name }}</td>
                                        <td>
                                            @if($prize->hasStockLimit())
                                            {{ $prize->stock }}
                                            @else
                                            <span class="badge bg-success">Unlimited</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                                                data-bs-target="#editPrizeModal{{ $prize->id }}">
                                                <i class="fi fi-rr-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.event.prizes.destroy', [$event, $prize]) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus hadiah ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            </form>

                                            {{-- Edit Prize Modal --}}
                                            <div class="modal fade text-start" id="editPrizeModal{{ $prize->id }}"
                                                tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form
                                                            action="{{ route('admin.event.prizes.update', [$event, $prize]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Hadiah</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Nama Hadiah</label>
                                                                    <input type="text" name="name" class="form-control"
                                                                        value="{{ $prize->name }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <div class="form-check form-switch mb-2">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="unlimitedStock{{ $prize->id }}"
                                                                            name="is_unlimited" value="1" {{
                                                                            !$prize->hasStockLimit() ? 'checked' : '' }}
                                                                        onchange="toggleStockInput('{{ $prize->id }}')">
                                                                        <label class="form-check-label"
                                                                            for="unlimitedStock{{ $prize->id }}">Stok
                                                                            Unlimited</label>
                                                                    </div>
                                                                    <label class="form-label">Jumlah Stok</label>
                                                                    <input type="number" name="stock"
                                                                        id="stockInput{{ $prize->id }}"
                                                                        class="form-control" value="{{ $prize->stock }}"
                                                                        min="0" {{ !$prize->hasStockLimit() ? 'disabled'
                                                                    : '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">Simpan
                                                                    Perubahan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="fi fi-rr-gift mb-2 d-block"
                                                style="font-size: 2rem; opacity: 0.3;"></i>
                                            Belum ada hadiah yang ditambahkan
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Prize Modal --}}
<div class="modal fade" id="addPrizeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.event.prizes.store', $event) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Hadiah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Hadiah</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Sepeda Gunung"
                            required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="unlimitedStockNew" name="is_unlimited"
                                value="1" onchange="toggleStockInput('New')">
                            <label class="form-check-label" for="unlimitedStockNew">Stok Unlimited</label>
                        </div>
                        <label class="form-label">Jumlah Stok</label>
                        <input type="number" name="stock" id="stockInputNew" class="form-control" value="1" min="0">
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

// Copy QR URL to clipboard
function copyQrUrl() {
    const input = document.getElementById('qrUrlInput');
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    showToast('success', 'QR URL berhasil disalin!');
}

// Copy Shortlink to clipboard
function copyShortlink() {
    const input = document.getElementById('shortlinkInput');
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    showToast('success', 'Shortlink berhasil disalin!');
}

// Toggle stock input
function toggleStockInput(id) {
    const checkbox = document.getElementById('unlimitedStock' + id);
    const input = document.getElementById('stockInput' + id);
    
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
</script>
@endpush