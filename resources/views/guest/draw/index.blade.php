@extends('guest.layouts.app')

@section('title', $event->nm_event)

@section('content')
<div class="draw-page-wrapper">
    <!-- Background Animation -->
    <div class="draw-background">
        <div class="animated-particles"></div>
    </div>

    <div class="draw-container">
        <!-- Combined Event Header & Prize Selection -->
        <div class="combined-header-card">
            <div class="combined-header-content">
                <!-- Left: Event Info -->
                <div class="event-info-section">
                    <h2 class="event-title">{{ $event->nm_event }}</h2>
                    <div class="event-meta">
                        <div class="meta-item">
                            <i class="fi fi-rr-calendar"></i>
                            <span>{{ $event->tgl_mulai->format('d M Y') }} - {{ $event->tgl_selesai->format('d M Y')
                                }}</span>
                        </div>
                        @if($event->opd)
                        <div class="meta-item">
                            <i class="fi fi-rr-building"></i>
                            <span>{{ $event->opd->nama_instansi }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Center: Prize Selection -->
                <div class="prize-selection-section">
                    <label class="prize-selection-label">
                        <i class="fi fi-rr-gift"></i>
                        <span>Pilih Hadiah</span>
                    </label>
                    <select class="prize-selection-input form-select" id="globalPrizeSelect">
                        <option value="" selected disabled>-- Pilih Hadiah --</option>
                        @foreach($prizes as $prize)
                        <option value="{{ $prize->id }}" data-name="{{ $prize->name }}" data-stock="{{ $prize->stock }}"
                            data-unlimited="{{ $prize->hasStockLimit() ? '0' : '1' }}" {{ !$prize->isAvailable() ?
                            'disabled' : '' }}>
                            {{ $prize->name }}
                        </option>
                        @endforeach
                    </select>
                    <div class="stock-display-wrapper">
                        <small class="global-stock-display text-muted">&nbsp;</small>
                    </div>
                </div>

                <!-- Right: Action Buttons -->
                <div class="action-buttons-section">
                    <button id="btnShowWinners" class="btn btn-outline-primary fw-bold shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#winnersModal">
                        <i class="fi fi-rr-trophy me-2"></i>Lihat Pemenang
                    </button>
                    <button class="copy-link-btn" id="btnCopyLink">
                        <i class="fi fi-rr-copy"></i>
                        <span>Copy Link</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content: Drawing Machines Area -->
        <div class="drawing-area-wrapper">
            <!-- Machines Container -->
            <div id="machinesContainer" class="machines-grid">
                <!-- Machines will be appended here by JS -->
            </div>

            <!-- Template for Machine Card -->
            <template id="machineTemplate">
                <div class="machine-col animate__animated animate__fadeInUp">
                    <div class="draw-card h-100 d-flex flex-column">
                        <!-- Remove Button (X) - hanya untuk card ke-2 dan seterusnya, di pojok kanan atas -->
                        <button class="btn-remove-machine d-none" title="Hapus Alat Undi">
                            <i class="fi fi-rr-cross"></i>
                        </button>

                        <!-- Drawing Display -->
                        <div class="draw-display flex-grow-1 py-3">
                            <!-- Add Machine Button (+) - hanya untuk card pertama -->
                            <button class="btn-add-machine-in-card" id="btnAddMachineInCard" title="Tambah Alat Undi">
                                <i class="fi fi-rr-add"></i>
                            </button>

                            <!-- Initial State -->
                            <div class="draw-state state-initial">
                                <div class="state-icon initial-icon mb-2">
                                    <i class="fi fi-rr-box-open"></i>
                                </div>
                                <h5 class="state-title fs-5">Siap?</h5>
                                <p class="state-subtitle small mb-0">Pilih hadiah dan mulai undi</p>
                            </div>

                            <!-- Rolling State -->
                            <div class="draw-state state-rolling d-none">
                                <div class="state-icon rolling-icon mb-3"
                                    style="width: 100px; height: 100px; font-size: 2.5rem;">
                                    <i class="fi fi-rr-refresh"></i>
                                </div>
                                <div class="rolling-content">
                                    <div class="rolling-text coupon-display mb-0"
                                        style="font-size: 2.5rem; letter-spacing: 4px;">000000</div>
                                </div>
                            </div>

                            <!-- Winner State -->
                            <div class="draw-state state-winner d-none">
                                <div class="winner-celebration mb-1">
                                    <div class="state-icon winner-icon mb-1">
                                        <i class="fi fi-rr-trophy"></i>
                                    </div>
                                </div>
                                <div class="winner-badge mb-2 py-1 px-3 fs-6">
                                    <span>PEMENANG</span>
                                </div>
                                <div class="winner-content">
                                    <div class="winner-coupon coupon-display mb-1">000000</div>
                                    <div class="winner-label small mb-1">Nomor Kupon</div>
                                    <div class="winner-prize py-1 px-2 fs-6">
                                        <i class="fi fi-rr-gift me-1"></i>
                                        <span class="prize-name-display">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons p-3">
                            <button class="action-btn btn-start btn-sm py-2" style="display: flex !important;">
                                <i class="fi fi-rr-play"></i>
                                <span>MULAI</span>
                            </button>
                            <button class="action-btn btn-stop d-none btn-sm py-2">
                                <i class="fi fi-rr-stop"></i>
                                <span>STOP</span>
                            </button>
                            <button class="action-btn btn-redraw d-none btn-sm py-2">
                                <i class="fi fi-rr-refresh"></i>
                                <span>ULANG UNDI</span>
                            </button>
                            <button class="action-btn btn-confirm d-none btn-sm py-2">
                                <i class="fi fi-rr-check"></i>
                                <span>KONFIRMASI</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Save Button (Fixed at Bottom) -->
    <div class="save-section mt-4">
        <div class="save-card">
            <div class="save-content">
                <div class="save-info">
                    <i class="fi fi-rr-trophy"></i>
                    <span id="saveCount">0</span> pemenang siap disimpan
                </div>
                <button id="btnSaveWinners" class="btn-save-winners" disabled>
                    <i class="fi fi-rr-disk"></i>
                    <span>Simpan Pemenang</span>
                </button>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>

<!-- Winner List Modal -->
<div class="modal fade" id="winnersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fi fi-rr-trophy me-2"></i>Daftar Pemenang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0" id="modalWinnersBody">
                <div class="winners-list" id="winnerList" style="max-height: 500px; overflow-y: auto;">
                    <div class="list-group list-group-flush">
                        @forelse($winners as $winner)
                        <div class="list-group-item winner-item p-3">
                            <div class="winner-item-icon" style="width: 36px; height: 36px; font-size: 1rem;">
                                <i class="fi fi-rr-trophy"></i>
                            </div>
                            <div class="winner-item-content">
                                <div class="winner-item-header mb-1">
                                    <div class="winner-item-name small">{{ $winner->participant->name ?
                                        substr($winner->participant->name, 0, 2) . str_repeat('*', max(0,
                                        strlen($winner->participant->name) - 2)) : '***' }}</div>
                                    <div class="winner-item-coupon small">{{ $winner->participant->coupon_number }}
                                    </div>
                                </div>
                                <div class="winner-item-footer">
                                    <div class="winner-item-prize small">
                                        <i class="fi fi-rr-gift text-primary"></i>
                                        <span class="text-truncate" style="max-width: 120px;">{{ $winner->prize_name
                                            }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="winners-empty small text-center py-5">
                            <i class="fi fi-rr-confetti mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                            <p class="mb-0 text-muted">Belum ada pemenang</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
<style>
    .draw-page-wrapper {
        height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .draw-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 1rem;
        overflow: hidden;
        position: relative;
        z-index: 1;
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

        0%,
        100% {
            transform: translate(0, 0);
        }

        50% {
            transform: translate(30px, 30px);
        }
    }

    /* Combined Header Card */
    .combined-header-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
        flex-shrink: 0;
    }

    .combined-header-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        border-radius: 16px 16px 0 0;
    }

    .combined-header-content {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 1.5rem;
        align-items: center;
    }

    .event-info-section {
        min-width: 0;
    }

    .event-title {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .event-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .meta-item i {
        color: #667eea;
        font-size: 0.875rem;
    }

    .prize-selection-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 280px;
    }

    .prize-selection-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #6c757d;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .prize-selection-label i {
        color: #667eea;
        font-size: 1rem;
    }

    .prize-selection-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 700;
        text-align: center;
        transition: all 0.3s;
    }

    .prize-selection-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .prize-selection-input:disabled {
        background: #f8f9fa;
        cursor: not-allowed;
    }

    .stock-display-wrapper {
        margin-top: 0.25rem;
    }

    .global-stock-display {
        font-size: 0.75rem;
    }

    .action-buttons-section {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .copy-link-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        white-space: nowrap;
    }

    .copy-link-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    /* Drawing Area */
    .drawing-area-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        min-height: 0;
    }

    .machines-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1rem;
        overflow-y: auto;
        flex: 1;
        min-height: 0;
        padding-bottom: 0.5rem;
    }

    .machine-col {
        min-width: 0;
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

    /* Remove Button (X) Styling */
    .btn-remove-machine {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(220, 53, 69, 0.15);
        border: 2px solid rgba(220, 53, 69, 0.3);
        z-index: 10;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
    }

    .btn-remove-machine:hover {
        background: rgba(220, 53, 69, 0.25);
        border-color: rgba(220, 53, 69, 0.5);
        transform: scale(1.1) rotate(90deg);
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
    }

    .btn-remove-machine:active {
        transform: scale(0.95) rotate(90deg);
    }

    .btn-remove-machine i {
        font-size: 1.25rem;
        color: #dc3545;
        font-weight: bold;
        transition: transform 0.3s;
    }

    .btn-remove-machine:hover i {
        color: #c82333;
        transform: rotate(180deg);
    }

    /* Draw Display */
    .draw-display {
        min-height: 150px;
        max-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 1rem 0.75rem;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
        position: relative;
        overflow: hidden;
    }

    /* Add Machine Button in Card */
    .btn-add-machine-in-card {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: 3px solid rgba(255, 255, 255, 0.9);
        color: white;
        font-size: 1.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5), 0 0 0 4px rgba(102, 126, 234, 0.1);
        z-index: 10;
        opacity: 0;
        transform: scale(0.8);
        pointer-events: none;
        animation: pulse-glow 2s infinite;
    }

    /* Pastikan animasi hanya berjalan saat tombol visible */
    .btn-add-machine-in-card.show {
        animation: pulse-glow 2s infinite;
    }

    @keyframes pulse-glow {

        0%,
        100% {
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5), 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        50% {
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.7), 0 0 0 6px rgba(102, 126, 234, 0.2);
        }
    }

    .btn-add-machine-in-card.show {
        opacity: 1;
        transform: scale(1);
        pointer-events: all;
    }

    .btn-add-machine-in-card:hover {
        transform: scale(1.15) rotate(90deg) !important;
        box-shadow: 0 8px 30px rgba(102, 126, 234, 0.8), 0 0 0 8px rgba(102, 126, 234, 0.3) !important;
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        border-color: rgba(255, 255, 255, 1);
    }

    .btn-add-machine-in-card:active {
        transform: scale(1.05) rotate(90deg) !important;
    }

    .btn-add-machine-in-card i {
        transition: transform 0.3s;
        font-weight: bold;
    }

    .btn-add-machine-in-card:hover i {
        transform: rotate(180deg);
    }

    .draw-state {
        text-align: center;
        width: 100%;
    }

    .state-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.75rem;
        font-size: 2.5rem;
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
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .winner-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        animation: bounce 0.6s ease-out;
    }

    @keyframes bounce {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    .state-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.25rem;
    }

    .state-subtitle {
        color: #6c757d;
        font-size: 0.8rem;
    }

    /* Rolling State */
    .rolling-content {
        text-align: center;
    }

    .rolling-text {
        font-size: 2.5rem;
        font-weight: 900;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-family: 'Courier New', monospace;
        letter-spacing: 4px;
        margin-bottom: 0.25rem;
        animation: pulse 0.1s infinite;
        line-height: 1.2;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
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

    .winner-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 1rem;
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
        font-size: 3rem;
        font-weight: 900;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-family: 'Courier New', monospace;
        letter-spacing: 6px;
        margin-bottom: 0.25rem;
        animation: scaleIn 0.6s ease-out;
        line-height: 1.2;
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
        padding: 0.75rem 1rem;
        border-top: 1px solid #e9ecef;
        flex-shrink: 0;
    }

    .action-btn {
        width: 100%;
        padding: 0.75rem 1.25rem;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .action-btn:last-child {
        margin-bottom: 0;
    }

    .btn-start {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .btn-start:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }

    .btn-start.d-none {
        display: none !important;
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

    .btn-redraw {
        background: linear-gradient(135deg, #ffa726 0%, #fb8c00 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(255, 167, 38, 0.4);
    }

    .btn-redraw:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 167, 38, 0.5);
    }

    .btn-confirm {
        background: linear-gradient(135deg, #66bb6a 0%, #43a047 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 187, 106, 0.4);
    }

    .btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 187, 106, 0.5);
    }

    .action-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    /* Save Section */
    .save-section {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.1);
    }

    .save-card {
        max-width: 1200px;
        margin: 0 auto;
    }

    .save-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .save-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.1rem;
        font-weight: 600;
        color: #212529;
    }

    .save-info i {
        color: #667eea;
        font-size: 1.5rem;
    }

    .btn-save-winners {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 2.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-save-winners:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }

    .btn-save-winners:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    /* Winner Item */
    .winner-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border: none;
        border-bottom: 1px solid #e9ecef !important;
        background: transparent;
        transition: all 0.3s;
    }

    .list-group-item:last-child.winner-item {
        border-bottom: none !important;
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

    /* Responsive */
    @media (max-width: 1200px) {
        .combined-header-content {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .prize-selection-section {
            min-width: 100%;
        }

        .action-buttons-section {
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .draw-container {
            padding: 0.75rem;
        }

        .combined-header-card {
            padding: 1rem;
        }

        .combined-header-content {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .event-title {
            font-size: 1.25rem;
        }

        .event-meta {
            gap: 0.75rem;
        }

        .meta-item {
            font-size: 0.8rem;
        }

        .prize-selection-section {
            min-width: 100%;
        }

        .prize-selection-input {
            font-size: 0.875rem;
            padding: 0.625rem 0.875rem;
        }

        .action-buttons-section {
            flex-direction: column;
            width: 100%;
        }

        .action-buttons-section button {
            width: 100%;
        }

        .machines-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .rolling-text {
            font-size: 2.5rem;
            letter-spacing: 4px;
        }

        .winner-coupon {
            font-size: 3rem;
            letter-spacing: 6px;
        }

        .state-icon {
            width: 80px;
            height: 80px;
            font-size: 2.5rem;
        }

        .draw-display {
            min-height: 140px;
            max-height: 160px;
            padding: 0.75rem;
        }

        .save-content {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-save-winners {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
<script>
    $(document).ready(function() {
        let candidates = [];
        let allCouponNumbers = []; // Semua nomor kupon untuk visual random
        const shortlink = "{{ $event->shortlink }}";
        let confirmedWinners = []; // Array untuk menyimpan pemenang yang sudah dikonfirmasi

        // --- GLOBAL PRIZE SELECT ---
        const globalPrizeSelect = document.getElementById('globalPrizeSelect');
        const globalStockDisplay = document.querySelector('.global-stock-display');

        function updateGlobalStockDisplay() {
            const option = globalPrizeSelect.options[globalPrizeSelect.selectedIndex];
            if (option && option.value) {
                const isUnlimited = option.dataset.unlimited === '1';
                const stock = option.dataset.stock;
                if (isUnlimited) {
                    globalStockDisplay.textContent = 'Stok: Unlimited';
                    globalStockDisplay.className = 'global-stock-display text-success fw-bold';
                } else {
                    globalStockDisplay.textContent = `Stok Tersisa: ${stock}`;
                    globalStockDisplay.className = stock > 0 ? 'global-stock-display text-primary fw-bold' : 'global-stock-display text-danger fw-bold';
                }
            } else {
                globalStockDisplay.innerHTML = '&nbsp;';
            }
        }

        globalPrizeSelect.addEventListener('change', updateGlobalStockDisplay);

        // --- DRAW MACHINE CLASS ---
        class DrawMachine {
            constructor(id, canDelete = true) {
                this.id = id;
                this.canDelete = canDelete;
                this.element = null;
                this.isRolling = false;
                this.rollInterval = null;
                this.currentWinner = null; // Menyimpan hasil undian sementara
                this.isConfirmed = false; // Status konfirmasi
                
                this.render();
            }

            render() {
                const template = document.getElementById('machineTemplate');
                const clone = template.content.cloneNode(true);
                
                const col = clone.querySelector('.machine-col');
                col.id = `machine-${this.id}`;
                col.dataset.id = this.id;
                
                const btnRemove = clone.querySelector('.btn-remove-machine');
                if (this.canDelete) {
                    btnRemove.classList.remove('d-none');
                    btnRemove.addEventListener('click', () => this.remove());
                } else {
                    btnRemove.classList.add('d-none');
                }

                const btnStart = clone.querySelector('.btn-start');
                const btnStop = clone.querySelector('.btn-stop');
                const btnRedraw = clone.querySelector('.btn-redraw');
                const btnConfirm = clone.querySelector('.btn-confirm');
                const btnAddInCard = clone.querySelector('#btnAddMachineInCard');
                
                btnStart.addEventListener('click', () => this.start());
                btnStop.addEventListener('click', () => this.stop());
                btnRedraw.addEventListener('click', () => this.redraw());
                btnConfirm.addEventListener('click', () => this.confirm());
                
                // Set unique ID untuk tombol add di card
                if (btnAddInCard) {
                    btnAddInCard.id = `btnAddMachineInCard-${this.id}`;
                    btnAddInCard.addEventListener('click', (e) => {
                        e.stopPropagation();
                        addMachine();
                    });
                }

                document.getElementById('machinesContainer').appendChild(clone);
                this.element = document.getElementById(`machine-${this.id}`);
                
                // Update visibility tombol + dan X setelah element terpasang
                setTimeout(() => {
                    this.updateAddButtonVisibility();
                    const btnRemove = this.element.querySelector('.btn-remove-machine');
                    if (btnRemove) {
                        if (this.canDelete && !this.isConfirmed) {
                            btnRemove.classList.remove('d-none');
                        } else {
                            btnRemove.classList.add('d-none');
                        }
                    }
                }, 100);
            }

            updateAddButtonVisibility() {
                const btnAddInCard = this.element.querySelector('.btn-add-machine-in-card');
                if (!btnAddInCard) return;
                
                const stateInitial = this.element.querySelector('.state-initial');
                const stateRolling = this.element.querySelector('.state-rolling');
                const stateWinner = this.element.querySelector('.state-winner');
                
                if (!stateInitial || !stateRolling || !stateWinner) return;
                
                const isInitial = !stateInitial.classList.contains('d-none');
                const isRolling = !stateRolling.classList.contains('d-none');
                const isWinner = !stateWinner.classList.contains('d-none');
                
                // Hanya tampilkan tombol + di card pertama (index 0) yang belum dikonfirmasi
                const isFirstMachine = machines.length > 0 && machines[0] && machines[0].id === this.id;
                const canAddMore = machines.length < MAX_MACHINES;
                
                // Debug: console.log untuk melihat kondisi
                // console.log('Machine ID:', this.id, 'isFirst:', isFirstMachine, 'isInitial:', isInitial, 'canAddMore:', canAddMore);
                
                if (isInitial && !isRolling && !isWinner && !this.isConfirmed && isFirstMachine && canAddMore) {
                    btnAddInCard.classList.add('show');
                } else {
                    btnAddInCard.classList.remove('show');
                }
            }

            remove() {
                if (this.element && this.canDelete && !this.isConfirmed) {
                    this.element.classList.add('animate__fadeOutDown');
                    setTimeout(() => {
                        this.element.remove();
                        machines = machines.filter(m => m.id !== this.id);
                        checkMachineCount();
                        updateSaveButton();
                        
                        // Update visibility tombol + dan X di semua card setelah remove
                        machines.forEach(m => {
                            m.updateAddButtonVisibility();
                            // Update visibility tombol X
                            const btnRemove = m.element.querySelector('.btn-remove-machine');
                            if (btnRemove) {
                                if (m.canDelete && !m.isConfirmed) {
                                    btnRemove.classList.remove('d-none');
                                } else {
                                    btnRemove.classList.add('d-none');
                                }
                            }
                        });
                    }, 500);
                }
            }

            start() {
                const prizeId = globalPrizeSelect.value;
                
                if (!prizeId) {
                    $.toast({ text: 'Pilih hadiah dulu!', icon: 'warning', position: 'top-center' });
                    globalPrizeSelect.focus();
                    return;
                }

                if (candidates.length === 0) {
                    $.toast({ text: 'Peserta habis!', icon: 'error', position: 'top-center' });
                    return;
                }

                if (this.isConfirmed) {
                    $.toast({ text: 'Pemenang sudah dikonfirmasi!', icon: 'warning', position: 'top-center' });
                    return;
                }

                // Lock UI
                globalPrizeSelect.disabled = true;
                if(this.canDelete) {
                    const btnRemove = this.element.querySelector('.btn-remove-machine');
                    if (btnRemove) btnRemove.disabled = true;
                }
                
                // Switch State
                this.element.querySelector('.state-initial').classList.add('d-none');
                this.element.querySelector('.state-winner').classList.add('d-none');
                this.element.querySelector('.state-rolling').classList.remove('d-none');
                
                // Update add button visibility
                this.updateAddButtonVisibility();
                
                // Buttons
                this.element.querySelector('.btn-start').classList.add('d-none');
                this.element.querySelector('.btn-stop').classList.remove('d-none');
                this.element.querySelector('.btn-redraw').classList.add('d-none');
                this.element.querySelector('.btn-confirm').classList.add('d-none');

                // Animation dengan visual random dari semua nomor kupon
                this.isRolling = true;
                const display = this.element.querySelector('.rolling-text');
                this.rollInterval = setInterval(() => {
                    // Gunakan semua nomor kupon untuk visual random yang lebih menarik
                    if (allCouponNumbers.length > 0) {
                        const randomIndex = Math.floor(Math.random() * allCouponNumbers.length);
                        display.textContent = allCouponNumbers[randomIndex];
                    } else if (candidates.length > 0) {
                        // Fallback ke candidates jika allCouponNumbers belum terload
                        const random = candidates[Math.floor(Math.random() * candidates.length)];
                        display.textContent = random.coupon_number;
                    }
                }, 50);
            }

            stop() {
                clearInterval(this.rollInterval);
                this.isRolling = false;
                
                const btnStop = this.element.querySelector('.btn-stop');
                btnStop.disabled = true;
                btnStop.innerHTML = '<i class="fi fi-rr-spinner fi-spin"></i>';

                // Pick winner (tidak langsung save ke database)
                const winnerIndex = Math.floor(Math.random() * candidates.length);
                const winner = candidates[winnerIndex];
                const prizeId = globalPrizeSelect.value;
                const prizeOption = globalPrizeSelect.options[globalPrizeSelect.selectedIndex];
                const prizeName = prizeOption.dataset.name;

                // Simpan hasil sementara
                this.currentWinner = {
                    participant_id: winner.id,
                    prize_id: prizeId,
                    participant: {
                        id: winner.id,
                        coupon_number: winner.coupon_number,
                        name: winner.name
                    },
                    prize_name: prizeName
                };

                this.showWinner();
            }

            showWinner() {
                const btnStop = this.element.querySelector('.btn-stop');
                btnStop.classList.add('d-none');
                btnStop.disabled = false;
                btnStop.innerHTML = '<i class="fi fi-rr-stop"></i> <span>STOP</span>';

                // Switch State
                this.element.querySelector('.state-rolling').classList.add('d-none');
                this.element.querySelector('.state-winner').classList.remove('d-none');

                // Update add button visibility
                this.updateAddButtonVisibility();

                // Update Winner Info
                this.element.querySelector('.winner-coupon').textContent = this.currentWinner.participant.coupon_number;
                this.element.querySelector('.prize-name-display').textContent = this.currentWinner.prize_name;

                // Confetti
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 },
                    colors: ['#667eea', '#764ba2', '#f093fb']
                });

                // Show buttons
                setTimeout(() => {
                    this.element.querySelector('.btn-redraw').classList.remove('d-none');
                    this.element.querySelector('.btn-confirm').classList.remove('d-none');
                }, 500);
            }

            redraw() {
                // Reset dan mulai ulang
                this.currentWinner = null;
                this.element.querySelector('.state-winner').classList.add('d-none');
                this.element.querySelector('.state-initial').classList.remove('d-none');
                
                // Update add button visibility
                this.updateAddButtonVisibility();
                
                this.element.querySelector('.btn-redraw').classList.add('d-none');
                this.element.querySelector('.btn-confirm').classList.add('d-none');
                this.element.querySelector('.btn-start').classList.remove('d-none');
                
                globalPrizeSelect.disabled = false;
                if(this.canDelete) {
                    const btnRemove = this.element.querySelector('.btn-remove-machine');
                    if (btnRemove) btnRemove.disabled = false;
                }
            }

            confirm() {
                if (!this.currentWinner) {
                    $.toast({ text: 'Tidak ada pemenang untuk dikonfirmasi!', icon: 'error', position: 'top-center' });
                    return;
                }

                // Tambahkan ke array confirmedWinners
                const existingIndex = confirmedWinners.findIndex(w => w.machineId === this.id);
                if (existingIndex > -1) {
                    confirmedWinners[existingIndex] = {
                        machineId: this.id,
                        ...this.currentWinner
                    };
                } else {
                    confirmedWinners.push({
                        machineId: this.id,
                        ...this.currentWinner
                    });
                }

                this.isConfirmed = true;
                
                // Update UI
                this.element.querySelector('.btn-redraw').classList.add('d-none');
                this.element.querySelector('.btn-confirm').classList.add('d-none');
                
                // Update add button visibility
                this.updateAddButtonVisibility();
                
                // Hide remove button saat dikonfirmasi
                const btnRemove = this.element.querySelector('.btn-remove-machine');
                if (btnRemove) {
                    btnRemove.classList.add('d-none');
                    btnRemove.disabled = true;
                }

                $.toast({ 
                    text: 'Pemenang dikonfirmasi!', 
                    icon: 'success', 
                    position: 'top-center' 
                });

                updateSaveButton();
            }
        }

        // --- MANAGE MACHINES ---
        let machines = [];
        const MAX_MACHINES = 3;

        function addMachine() {
            if (machines.length >= MAX_MACHINES) {
                $.toast({ text: 'Maksimal ' + MAX_MACHINES + ' alat undi!', icon: 'warning', position: 'top-center' });
                return;
            }
            
            const id = Date.now();
            const canDelete = machines.length > 0; // Card pertama tidak bisa dihapus, card selanjutnya bisa
            
            const machine = new DrawMachine(id, canDelete);
            machines.push(machine);
            checkMachineCount();
            
            // Update visibility tombol + dan X di semua card setelah machine ditambahkan
            setTimeout(() => {
                machines.forEach(m => {
                    m.updateAddButtonVisibility();
                    // Update visibility tombol X
                    const btnRemove = m.element.querySelector('.btn-remove-machine');
                    if (btnRemove) {
                        if (m.canDelete && !m.isConfirmed) {
                            btnRemove.classList.remove('d-none');
                        } else {
                            btnRemove.classList.add('d-none');
                        }
                    }
                });
            }, 150);
        }

        function checkMachineCount() {
            const count = machines.length;
            
            // Update visibility tombol add di semua card
            machines.forEach(m => m.updateAddButtonVisibility());
        }

        // Event listener untuk tombol add machine (jika masih ada di controls)
        $(document).on('click', '#btnAddNewMachine', addMachine);
        
        // Event listener untuk tombol add machine di dalam card
        $(document).on('click', '.btn-add-machine-in-card', function(e) {
            e.stopPropagation();
            addMachine();
        });
        
        // Initial Machine
        addMachine();
        
        // Update visibility setelah semua machine ter-render (dengan beberapa delay untuk memastikan DOM ready)
        setTimeout(() => {
            machines.forEach(m => {
                if (m && m.updateAddButtonVisibility) {
                    m.updateAddButtonVisibility();
                }
            });
        }, 300);
        
        // Update lagi setelah 500ms untuk memastikan
        setTimeout(() => {
            machines.forEach(m => {
                if (m && m.updateAddButtonVisibility) {
                    m.updateAddButtonVisibility();
                }
            });
        }, 500);
        
        // Update visibility setelah semua machine ter-render
        setTimeout(() => {
            machines.forEach(m => m.updateAddButtonVisibility());
        }, 200);

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

        // --- LOAD ALL COUPON NUMBERS FOR VISUAL RANDOM ---
        function loadAllCouponNumbers() {
            axios.get(`{{ route('draw.coupon-numbers', $event->shortlink) }}`)
                .then(res => {
                    allCouponNumbers = res.data;
                })
                .catch(err => {
                    console.error(err);
                    // Fallback: extract coupon numbers from candidates
                    allCouponNumbers = candidates.map(c => c.coupon_number);
                });
        }

        loadCandidates();
        loadAllCouponNumbers();

        // --- UPDATE SAVE BUTTON ---
        function updateSaveButton() {
            const count = confirmedWinners.length;
            const saveCount = document.getElementById('saveCount');
            const btnSave = document.getElementById('btnSaveWinners');
            
            saveCount.textContent = count;
            
            if (count > 0) {
                btnSave.disabled = false;
            } else {
                btnSave.disabled = true;
            }
        }

        // --- SAVE WINNERS ---
        $('#btnSaveWinners').on('click', function() {
            if (confirmedWinners.length === 0) {
                $.toast({ text: 'Tidak ada pemenang untuk disimpan!', icon: 'warning', position: 'top-center' });
                return;
            }

            const btnSave = $(this);
            btnSave.prop('disabled', true);
            btnSave.html('<i class="fi fi-rr-spinner fi-spin"></i> <span>Menyimpan...</span>');

            // Prepare data untuk dikirim
            const winnersData = confirmedWinners.map(w => ({
                participant_id: w.participant_id,
                prize_id: w.prize_id
            }));

            axios.post(`{{ route('draw.winners', $event->shortlink) }}`, {
                winners: winnersData
            })
            .then(res => {
                $.toast({ 
                    text: res.data.message, 
                    icon: 'success', 
                    position: 'top-center' 
                });

                // Reset
                confirmedWinners = [];
                machines.forEach(m => {
                    m.currentWinner = null;
                    m.isConfirmed = false;
                    m.redraw();
                });
                
                // Reload candidates dan coupon numbers
                loadCandidates();
                loadAllCouponNumbers();
                
                // Reload page setelah 1 detik untuk update winner list
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            })
            .catch(err => {
                $.toast({ 
                    text: err.response?.data?.message || 'Gagal menyimpan pemenang.', 
                    icon: 'error',
                    position: 'top-center'
                });
                btnSave.prop('disabled', false);
                btnSave.html('<i class="fi fi-rr-disk"></i> <span>Simpan Pemenang</span>');
            });
        });

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