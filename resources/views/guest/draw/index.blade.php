@extends('guest.layouts.app')

@section('title', $event->nm_event)

@section('content')
<div class="draw-page-wrapper">
    <!-- Background Animation -->
    <div class="draw-background">
        <div class="animated-particles"></div>
    </div>

    <div class="container py-5">
    <div class="row justify-content-center">
            <div class="col-xxl-11">
            <!-- Event Header -->
                <div class="event-header-card">
                    <div class="event-header-content">
                        <div class="event-info">
                            <h2 class="event-title">{{ $event->nm_event }}</h2>
                            <div class="event-meta">
                                <div class="meta-item">
                                    <i class="fi fi-rr-calendar"></i>
                                    <span>{{ $event->tgl_mulai->format('d M Y') }} - {{ $event->tgl_selesai->format('d M Y') }}</span>
                                </div>
                                @if($event->opd)
                                <div class="meta-item">
                                    <i class="fi fi-rr-building"></i>
                                    <span>{{ $event->opd->nama_instansi }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        <button class="copy-link-btn" id="btnCopyLink">
                            <i class="fi fi-rr-copy"></i>
                            <span>Copy Link</span>
                        </button>
                </div>
            </div>

                <!-- Main Content -->
                <div class="row g-4">
                    <!-- Drawing Machines Area -->
                    <div class="col-lg-9" id="machinesArea">
                        <!-- Controls -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0 text-white fw-bold"><i class="fi fi-rr-gamepad me-2"></i>Area Pengundian</h4>
                            <div class="d-flex gap-2">
                                <button id="btnShowWinners" class="btn btn-outline-light fw-bold shadow-sm d-none" data-bs-toggle="modal" data-bs-target="#winnersModal">
                                    <i class="fi fi-rr-trophy me-2"></i>Lihat Pemenang
                                </button>
                                <button id="btnAddNewMachine" class="btn btn-link text-white p-0" title="Tambah Alat Undi">
                                    <i class="fi fi-rr-add" style="font-size: 1.5rem;"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Machines Container -->
                        <div id="machinesContainer" class="row g-4">
                            <!-- Machines will be appended here by JS -->
                        </div>

                        <!-- Template for Machine Card -->
                        <template id="machineTemplate">
                            <div class="col-md-6 col-xl-4 machine-col animate__animated animate__fadeInUp">
                                <div class="draw-card h-100 d-flex flex-column">
                                    <div class="card-header-actions text-end p-2 pb-0">
                                        <button class="btn btn-link text-danger btn-remove-machine p-0" title="Hapus Alat Undi">
                                            <i class="fi fi-rr-cross-circle"></i>
                                        </button>
                                    </div>
                                    <!-- Prize Selection -->
                                    <div class="prize-input-section pt-0">
                                        <label class="prize-label justify-content-center">
                                            <i class="fi fi-rr-gift"></i>
                                            <span>Pilih Hadiah</span>
                                        </label>
                                        <select class="prize-input form-select form-select-sm machine-prize-select">
                                            <option value="" selected disabled>-- Pilih Hadiah --</option>
                                            @foreach($prizes as $prize)
                                                <option value="{{ $prize->id }}" 
                                                        data-name="{{ $prize->name }}"
                                                        data-stock="{{ $prize->stock }}"
                                                        data-unlimited="{{ $prize->hasStockLimit() ? '0' : '1' }}"
                                                        {{ !$prize->isAvailable() ? 'disabled' : '' }}>
                                                    {{ $prize->name }} 
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="text-center mt-1">
                                            <small class="stock-display text-muted" style="font-size: 0.75rem;">&nbsp;</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Drawing Display -->
                                    <div class="draw-display flex-grow-1 py-4" style="min-height: 250px;">
                                        <!-- Initial State -->
                                        <div class="draw-state state-initial">
                                            <div class="state-icon initial-icon mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                                                <i class="fi fi-rr-box-open"></i>
                                            </div>
                                            <h5 class="state-title fs-5">Siap?</h5>
                                            <p class="state-subtitle small mb-0">Pilih hadiah dulu</p>
                                        </div>

                                        <!-- Rolling State -->
                                        <div class="draw-state state-rolling d-none">
                                            <div class="state-icon rolling-icon mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                                                <i class="fi fi-rr-refresh"></i>
                                            </div>
                                            <div class="rolling-content">
                                                <div class="rolling-text coupon-display mb-0" style="font-size: 2.5rem; letter-spacing: 4px;">000000</div>
                                                <div class="rolling-label small mt-1">Mengacak...</div>
                                            </div>
                                        </div>

                                        <!-- Winner State -->
                                        <div class="draw-state state-winner d-none">
                                            <div class="winner-celebration mb-2">
                                                <div class="state-icon winner-icon mb-2" style="width: 80px; height: 80px; font-size: 2rem;">
                                                    <i class="fi fi-rr-trophy"></i>
                                                </div>
                                            </div>
                                            <div class="winner-badge mb-3 py-1 px-3 fs-6">
                                                <span>PEMENANG</span>
                                            </div>
                                            <div class="winner-content">
                                                <div class="winner-coupon coupon-display mb-2" style="font-size: 3rem; letter-spacing: 6px;">000000</div>
                                                <div class="winner-label small mb-2">Nomor Kupon</div>
                                                <div class="winner-prize py-1 px-2 fs-6">
                                                    <i class="fi fi-rr-gift me-1"></i>
                                                    <span class="prize-name-display">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="action-buttons p-3">
                                        <button class="action-btn btn-start btn-sm py-2">
                                            <i class="fi fi-rr-play"></i>
                                            <span>MULAI</span>
                                        </button>
                                        <button class="action-btn btn-stop d-none btn-sm py-2">
                                            <i class="fi fi-rr-stop"></i>
                                            <span>STOP</span>
                                        </button>
                                        <button class="action-btn btn-reset d-none btn-sm py-2">
                                            <i class="fi fi-rr-refresh"></i>
                                            <span>RESET</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Winners List Sidebar -->
                    <div class="col-lg-3" id="winnersSidebar">
                        <div class="winners-card h-100" id="winnersCardContent">
                            <div class="winners-header py-3 px-3">
                                <i class="fi fi-rr-trophy fs-5"></i>
                                <h3 class="fs-6 mb-0">Daftar Pemenang</h3>
                            </div>
                            <div class="winners-list" id="winnerList" style="max-height: calc(100vh - 250px);">
                                <div class="list-group list-group-flush">
                                @forelse($winners as $winner)
                                        <div class="list-group-item winner-item p-3">
                                            <div class="winner-item-icon" style="width: 36px; height: 36px; font-size: 1rem;">
                                                <i class="fi fi-rr-trophy"></i>
                                            </div>
                                            <div class="winner-item-content">
                                                <div class="winner-item-header mb-1">
                                                    <div class="winner-item-name small">{{ $winner->participant->name ? substr($winner->participant->name, 0, 2) . str_repeat('*', max(0, strlen($winner->participant->name) - 2)) : '***' }}</div>
                                                    <div class="winner-item-coupon small">{{ $winner->participant->coupon_number }}</div>
                                                </div>
                                                <div class="winner-item-footer">
                                                    <div class="winner-item-prize small">
                                                        <i class="fi fi-rr-gift text-primary"></i>
                                                        <span class="text-truncate" style="max-width: 120px;">{{ $winner->prize_name }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="winners-empty small">
                                            <i class="fi fi-rr-confetti mb-2" style="font-size: 2rem;"></i>
                                            <p class="mb-0">Belum ada pemenang</p>
                                        </div>
                                    @endforelse
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Winner List Modal -->
<div class="modal fade" id="winnersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0" id="modalWinnersBody">
                <!-- Content will be moved here via JS -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
<link rel="stylesheet" href="{{ asset('assets') }}/libs/simplebar/simplebar.css">
<style>
    .draw-page-wrapper {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        position: relative;
        overflow-x: hidden;
    }

    .draw-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        overflow: hidden;
    }

    .animated-particles {
        position: absolute;
        width: 100%;
        height: 100%;
        background-image: 
            radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        animation: particleMove 20s infinite ease-in-out;
    }

    @keyframes particleMove {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(30px, 30px); }
    }

    /* Event Header */
    .event-header-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        z-index: 1;
    }

    .event-header-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        border-radius: 20px 20px 0 0;
    }

    .event-header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .event-title {
        font-size: 1.75rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.75rem;
    }

    .event-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        font-size: 0.95rem;
    }

    .meta-item i {
        color: #667eea;
    }

    .copy-link-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .copy-link-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    /* Draw Card */
    .draw-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    .draw-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
    }

    .prize-input-section {
        padding: 2rem 2rem 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }

    .prize-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #6c757d;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }

    .prize-label i {
        color: #667eea;
    }

    .prize-input {
        width: 100%;
        padding: 1rem 1.5rem;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 700;
        text-align: center;
        transition: all 0.3s;
    }

    .prize-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .prize-input:disabled {
        background: #f8f9fa;
        cursor: not-allowed;
    }

    /* Draw Display */
    .draw-display {
        min-height: 500px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 3rem 2rem;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
        position: relative;
    }

    .draw-state {
        text-align: center;
        width: 100%;
    }

    .state-icon {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        font-size: 4rem;
        color: white;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .initial-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .rolling-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .winner-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        animation: bounce 0.6s ease-out;
    }

    @keyframes bounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .state-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.5rem;
    }

    .state-subtitle {
        color: #6c757d;
        font-size: 1rem;
    }

    /* Rolling State */
    .rolling-content {
        text-align: center;
    }

    .rolling-text {
        font-size: 5rem;
        font-weight: 900;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-family: 'Courier New', monospace;
        letter-spacing: 8px;
        margin-bottom: 1rem;
        animation: pulse 0.1s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .rolling-label {
        font-size: 1.25rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    /* Winner State */
    .winner-celebration {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .confetti-burst {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
        border-radius: 50%;
        animation: burst 1s ease-out;
    }

    @keyframes burst {
        0% {
            transform: translate(-50%, -50%) scale(0);
            opacity: 1;
        }
        100% {
            transform: translate(-50%, -50%) scale(2);
            opacity: 0;
        }
    }

    .winner-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(240, 147, 251, 0.4);
        animation: slideDown 0.6s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .winner-content {
        text-align: center;
    }

    .winner-coupon {
        font-size: 6rem;
        font-weight: 900;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-family: 'Courier New', monospace;
        letter-spacing: 12px;
        margin-bottom: 1rem;
        animation: scaleIn 0.6s ease-out;
    }

    @keyframes scaleIn {
        from {
            transform: scale(0);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .winner-label {
        font-size: 1.25rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 1.5rem;
    }

    .winner-prize {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        padding: 1rem 2rem;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 700;
        color: #667eea;
    }

    .winner-prize i {
        font-size: 1.25rem;
    }

    /* Action Buttons */
    .action-buttons {
        padding: 2rem;
        border-top: 1px solid #e9ecef;
    }

    .action-btn {
        width: 100%;
        padding: 1.25rem 2rem;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .action-btn:last-child {
        margin-bottom: 0;
    }

    .btn-start {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-start:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }

    .btn-stop {
        background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
    }

    .btn-stop:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 87, 108, 0.5);
    }

    .btn-reset {
        background: #6c757d;
        color: white;
        box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    }

    .btn-reset:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
    }

    .action-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    /* Winners Card */
    .winners-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        position: relative;
        z-index: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .winners-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
    }

    .winners-header {
        padding: 2rem 2rem 1.5rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .winners-header i {
        font-size: 1.5rem;
        color: #f093fb;
    }

    .winners-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #212529;
        margin: 0;
    }

    .winners-list {
        flex: 1;
        overflow-y: auto;
        max-height: 600px;
        padding: 0;
    }

    .winners-list .list-group {
        border: none;
    }

    .winner-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border: none;
        border-bottom: 1px solid #e9ecef !important;
        background: transparent;
        transition: all 0.3s;
        animation: slideIn 0.5s ease-out;
        margin: 0;
    }

    .list-group-item:last-child.winner-item {
        border-bottom: none !important;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .winner-item:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    }

    .winner-item-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .winner-item-content {
        flex: 1;
        min-width: 0;
    }

    .winner-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
        gap: 1rem;
    }

    .winner-item-name {
        font-weight: 700;
        color: #212529;
        font-size: 1rem;
        word-break: break-word;
    }

    .winner-item-coupon {
        font-weight: 700;
        color: #667eea;
        font-size: 1.1rem;
        font-family: 'Courier New', monospace;
        flex-shrink: 0;
    }

    .winner-item-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .winner-item-prize {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .winner-item-prize i {
        color: #f093fb;
    }

    .winner-item-time {
        color: #6c757d;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .winners-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }

    .winners-empty i {
        font-size: 3rem;
        opacity: 0.3;
        margin-bottom: 1rem;
        display: block;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .event-header-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .rolling-text {
            font-size: 3.5rem;
            letter-spacing: 4px;
        }

        .winner-coupon {
            font-size: 4rem;
            letter-spacing: 6px;
        }

        .state-icon {
            width: 100px;
            height: 100px;
            font-size: 3rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
<script src="{{ asset('assets') }}/libs/simplebar/simplebar.min.js"></script>
<script>
$(document).ready(function() {
    let candidates = [];
    const shortlink = "{{ $event->shortlink }}";
    
    // Initialize SimpleBar for winner list
    if (typeof SimpleBar !== 'undefined') {
        new SimpleBar(document.getElementById('winnerList'));
    }

    // --- DRAW MACHINE CLASS ---
    class DrawMachine {
        constructor(id, canDelete = true) {
            this.id = id;
            this.canDelete = canDelete;
            this.element = null;
            this.isRolling = false;
            this.rollInterval = null;
            this.candidates = []; 
            
            this.render();
        }

        render() {
            const template = document.getElementById('machineTemplate');
            const clone = template.content.cloneNode(true);
            
            // Set unique ID for the column
            const col = clone.querySelector('.machine-col');
            col.id = `machine-${this.id}`;
            col.dataset.id = this.id;
            
            // Remove button event
            const btnRemove = clone.querySelector('.btn-remove-machine');
            if (this.canDelete) {
                btnRemove.addEventListener('click', () => this.remove());
            } else {
                btnRemove.remove(); // Remove button from DOM if not deletable
            }

            // Bind events for this specific machine
            const btnStart = clone.querySelector('.btn-start');
            const btnStop = clone.querySelector('.btn-stop');
            const btnReset = clone.querySelector('.btn-reset');
            const prizeSelect = clone.querySelector('.machine-prize-select');
            
            btnStart.addEventListener('click', () => this.start());
            btnStop.addEventListener('click', () => this.stop());
            btnReset.addEventListener('click', () => this.reset());
            prizeSelect.addEventListener('change', (e) => this.updateStockDisplay(e.target));

            // Append to container
            document.getElementById('machinesContainer').appendChild(clone);
            this.element = document.getElementById(`machine-${this.id}`);
            
            // Trigger stock display update initially if value selected
            this.updateStockDisplay(prizeSelect);
        }

        remove() {
            if (this.element && this.canDelete) {
                this.element.classList.add('animate__fadeOutDown');
                setTimeout(() => {
                    this.element.remove();
                    // Remove from global machines array
                    machines = machines.filter(m => m.id !== this.id);
                    checkMachineCount();
                }, 500);
            }
        }

        updateStockDisplay(select) {
            const option = select.options[select.selectedIndex];
            const display = this.element.querySelector('.stock-display');
            
            if (option && option.value) {
                const isUnlimited = option.dataset.unlimited === '1';
                const stock = option.dataset.stock;
                
                if (isUnlimited) {
                     display.textContent = 'Stok: Unlimited';
                     display.className = 'stock-display text-success fw-bold';
                } else {
                     display.textContent = `Stok Tersisa: ${stock}`;
                     display.className = stock > 0 ? 'stock-display text-primary fw-bold' : 'stock-display text-danger fw-bold';
                }
            } else {
                display.innerHTML = '&nbsp;';
            }
        }

        start() {
            const prizeSelect = this.element.querySelector('.machine-prize-select');
            const prizeId = prizeSelect.value;
            
            if (!prizeId) {
                $.toast({ text: 'Pilih hadiah dulu!', icon: 'warning', position: 'top-center' });
                prizeSelect.focus();
                return;
            }

            if (candidates.length === 0) {
                 $.toast({ text: 'Peserta habis!', icon: 'error', position: 'top-center' });
                return;
            }

            // Lock UI
            prizeSelect.disabled = true;
            if(this.canDelete) {
                this.element.querySelector('.btn-remove-machine').disabled = true;
            }
            
            // Switch State
            this.element.querySelector('.state-initial').classList.add('d-none');
            this.element.querySelector('.state-winner').classList.add('d-none');
            this.element.querySelector('.state-rolling').classList.remove('d-none');
            
            // Buttons
            this.element.querySelector('.btn-start').classList.add('d-none');
            this.element.querySelector('.btn-stop').classList.remove('d-none');

            // Animation
            this.isRolling = true;
            const display = this.element.querySelector('.rolling-text');
            this.rollInterval = setInterval(() => {
                const random = candidates[Math.floor(Math.random() * candidates.length)];
                display.textContent = random.coupon_number;
            }, 50);
        }

        stop() {
            clearInterval(this.rollInterval);
            this.isRolling = false;
            
            const btnStop = this.element.querySelector('.btn-stop');
            btnStop.disabled = true;
            btnStop.innerHTML = '<i class="fi fi-rr-spinner fi-spin"></i>';

            // Pick winner
            const winnerIndex = Math.floor(Math.random() * candidates.length);
            const winner = candidates[winnerIndex];
            const prizeSelect = this.element.querySelector('.machine-prize-select');
            const prizeId = prizeSelect.value;

            axios.post(`{{ route('draw.winner', $event->shortlink) }}`, {
                participant_id: winner.id,
                prize_id: prizeId
            })
            .then(res => {
                this.showWinner(res.data.winner);
                // Remove from local candidates immediately
                const idx = candidates.findIndex(c => c.id === winner.id);
                if (idx > -1) candidates.splice(idx, 1);
            })
            .catch(err => {
                $.toast({ 
                    text: err.response?.data?.message || 'Gagal menyimpan.', 
                    icon: 'error',
                    position: 'top-center'
                });
                // Reset on fail
                this.resetUI();
            });
        }

        showWinner(winnerData) {
            this.element.querySelector('.btn-stop').classList.add('d-none');
            this.element.querySelector('.btn-stop').disabled = false;
            this.element.querySelector('.btn-stop').innerHTML = '<i class="fi fi-rr-stop"></i> <span>STOP</span>';

            // Switch State
            this.element.querySelector('.state-rolling').classList.add('d-none');
            this.element.querySelector('.state-winner').classList.remove('d-none');

            // Update Winner Info
            this.element.querySelector('.winner-coupon').textContent = winnerData.participant.coupon_number;
            this.element.querySelector('.prize-name-display').textContent = winnerData.prize_name;

            // Confetti scoped to this card? (global for now for effect)
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#667eea', '#764ba2', '#f093fb']
            });

            addWinnerToList(winnerData);

            // Show Reset
            setTimeout(() => {
                this.element.querySelector('.btn-reset').classList.remove('d-none');
            }, 1000);
        }

        reset() {
            this.element.querySelector('.state-winner').classList.add('d-none');
            this.element.querySelector('.state-initial').classList.remove('d-none');
            
            this.element.querySelector('.btn-reset').classList.add('d-none');
            this.element.querySelector('.btn-start').classList.remove('d-none');
            
            const prizeSelect = this.element.querySelector('.machine-prize-select');
            prizeSelect.disabled = false;
            prizeSelect.value = '';
            this.updateStockDisplay(prizeSelect);
            
            if(this.canDelete) {
                this.element.querySelector('.btn-remove-machine').disabled = false;
            }
        }

        resetUI() {
             this.element.querySelector('.state-rolling').classList.add('d-none');
             this.element.querySelector('.state-initial').classList.remove('d-none');
             
             const btnStop = this.element.querySelector('.btn-stop');
             btnStop.classList.add('d-none');
             btnStop.disabled = false;
             btnStop.innerHTML = '<i class="fi fi-rr-stop"></i> <span>STOP</span>';
             
             this.element.querySelector('.btn-start').classList.remove('d-none');
             this.element.querySelector('.machine-prize-select').disabled = false;
             if(this.canDelete) {
                this.element.querySelector('.btn-remove-machine').disabled = false;
             }
        }
    }

    // --- MANAGE MACHINES ---
    let machines = [];
    const MAX_MACHINES = 3;

    function addMachine() {
        if (machines.length >= MAX_MACHINES) return;
        
        const id = Date.now();
        // First machine cannot be deleted logic handled by check below, 
        // but explicitly passing false for first one created
        const canDelete = machines.length > 0; 
        
        const machine = new DrawMachine(id, canDelete);
        machines.push(machine);
        checkMachineCount();
    }

    function checkMachineCount() {
        const count = machines.length;
        
        // Button visibility
        if (count >= MAX_MACHINES) {
            $('#btnAddNewMachine').hide();
        } else {
            $('#btnAddNewMachine').show();
        }

        // Layout Update
        updateLayout(count);
    }

    function updateLayout(count) {
        const machinesArea = document.getElementById('machinesArea');
        const winnersSidebar = document.getElementById('winnersSidebar');
        const winnersCardContent = document.getElementById('winnersCardContent');
        const modalBody = document.getElementById('modalWinnersBody');
        const btnShowWinners = document.getElementById('btnShowWinners');

        // Logic for container layout
        if (count > 1) {
            // Full Width Mode
            machinesArea.classList.remove('col-lg-9');
            machinesArea.classList.add('col-lg-12');
            
            winnersSidebar.classList.add('d-none');
            btnShowWinners.classList.remove('d-none');

            // Move winner list to modal if not already there
            if (!modalBody.contains(winnersCardContent)) {
                modalBody.appendChild(winnersCardContent);
            }
        } else {
            // Normal Mode
            machinesArea.classList.remove('col-lg-12');
            machinesArea.classList.add('col-lg-9');
            
            winnersSidebar.classList.remove('d-none');
            btnShowWinners.classList.add('d-none');

            // Move winner list back to sidebar if not already there
            if (!winnersSidebar.contains(winnersCardContent)) {
                winnersSidebar.appendChild(winnersCardContent);
            }
        }

        // Logic for Machine Card resizing (Dynamic Grid)
        // 1 machine -> col-12
        // 2 machines -> col-md-6
        // 3 machines -> col-lg-4
        const machineCols = document.querySelectorAll('.machine-col');
        machineCols.forEach(col => {
            col.className = 'machine-col animate__animated animate__fadeInUp'; // Reset
            if (count === 1) {
                col.classList.add('col-12');
            } else if (count === 2) {
                col.classList.add('col-md-6');
            } else {
                col.classList.add('col-md-6', 'col-lg-4');
            }
        });
    }

    $('#btnAddNewMachine').on('click', addMachine);

    // Initial Machine (Static, non-deletable)
    addMachine();


    // --- LOAD CANDIDATES ---
    function loadCandidates() {
        axios.get(`{{ route('draw.candidates', $event->shortlink) }}`)
            .then(res => {
                candidates = res.data;
                if(candidates.length === 0) {
                     $.toast({ text: 'Tidak ada peserta!', icon: 'error' });
                }
            })
            .catch(err => {
                console.error(err);
                $.toast({ text: 'Gagal memuat peserta', icon: 'error' });
            });
    }
    loadCandidates();

    // --- HELPER: WINNER LIST ---
    function maskName(name) {
        if (!name || name.length === 0) return '***';
        if (name.length <= 2) return name.charAt(0) + '*';
        return name.substring(0, 2) + '*'.repeat(Math.max(0, name.length - 2));
    }

    function addWinnerToList(winner) {
        const date = new Date(winner.drawn_at);
        const timeStr = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
        const maskedName = maskName(winner.participant.name);
        
        const html = `
            <div class="list-group-item winner-item p-3 animate__animated animate__fadeInLeft">
                <div class="winner-item-icon" style="width: 36px; height: 36px; font-size: 1rem;">
                    <i class="fi fi-rr-trophy"></i>
                </div>
                <div class="winner-item-content">
                    <div class="winner-item-header mb-1">
                        <div class="winner-item-name small">${maskedName}</div>
                        <div class="winner-item-coupon small">${winner.participant.coupon_number}</div>
                    </div>
                    <div class="winner-item-footer">
                        <div class="winner-item-prize small">
                            <i class="fi fi-rr-gift text-primary"></i>
                            <span class="text-truncate" style="max-width: 120px;">${winner.prize_name}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        let $listGroup = $('#winnerList').find('.list-group');
        if ($listGroup.length === 0) {
            const existingItems = $('#winnerList').children('.list-group-item, .winner-item');
            if (existingItems.length > 0) {
                existingItems.wrapAll('<div class="list-group list-group-flush"></div>');
            } else {
                $('#winnerList').html('<div class="list-group list-group-flush"></div>');
            }
            $listGroup = $('#winnerList').find('.list-group');
        }
        
        $listGroup.find('.winners-empty').remove();
        $listGroup.prepend(html);
    }

    // Copy Link Feature
    $('#btnCopyLink').on('click', function() {
        const link = window.location.href;
        navigator.clipboard.writeText(link).then(() => {
            $.toast({ text: 'Link berhasil disalin!', position: 'bottom-center', icon: 'success', loader: false });
        });
    });
});
</script>
@endpush
