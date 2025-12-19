@extends('guest.layouts.app')

@section('title', $event->nm_event)

@section('content')
<div class="draw-page-wrapper">
    <!-- Background Animation -->
    <div class="draw-background">
        <div class="animated-particles"></div>
    </div>

    <div class="container pt-5">
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
                            <span>{{ $event->opd->nama_penyelenggara }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Center: Prize Selection -->
                <div class="prize-selection-section">
                    <label class="prize-selection-label">
                        <i class="fi fi-rr-gift"></i>
                        <span>Pilih Hadiah untuk Diundi</span>
                    </label>
                    
                    <!-- Prize Filter Section -->
                    <div class="prize-filter-section">
                        <button class="btn-prize-filter-toggle" id="btnPrizeFilterToggle">
                            <i class="fi fi-rr-filter"></i>
                            <span>Pilih Hadiah</span>
                            <i class="fi fi-rr-angle-down toggle-icon"></i>
                        </button>
                        <div class="prize-filter-panel" id="prizeFilterPanel">
                            <div class="prize-filter-list">
                                @foreach($prizes as $prize)
                                <label class="prize-filter-item">
                                    <input type="radio" 
                                           name="selectedPrize"
                                           class="prize-filter-radio" 
                                           value="{{ $prize->id }}" 
                                           data-prize-id="{{ $prize->id }}"
                                           data-prize-name="{{ $prize->name }}"
                                           data-stock="{{ $prize->stock }}"
                                           data-unlimited="{{ $prize->hasStockLimit() ? '0' : '1' }}"
                                           {{ !$prize->isAvailable() ? 'disabled' : '' }}>
                                    <div class="prize-filter-content">
                                        <div class="prize-filter-icon">
                                            <i class="fi fi-rr-gift"></i>
                                        </div>
                                        <div class="prize-filter-name">{{ $prize->name }}</div>
                                        <div class="prize-filter-stock">
                                            @if($prize->hasStockLimit())
                                                <span class="stock-badge-small {{ $prize->stock > 0 ? 'stock-available' : 'stock-empty' }}">
                                                    {{ $prize->stock }} tersisa
                                                </span>
                                            @else
                                                <span class="stock-badge-small stock-unlimited">
                                                    Unlimited
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="globalPrizeSelect" value="">
                </div>

                <!-- Right: Action Buttons -->
                <div class="action-buttons-section">
                    <button id="btnShowWinners" class="btn btn-outline-primary fw-bold shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#winnersModal">
                        <i class="fi fi-rr-trophy me-2"></i>Lihat Pemenang
                    </button>
                    <button class="copy-link-btn add-machine-btn" id="btnAddMachine">
                        <i class="fi fi-rr-add"></i>
                        <span>Tambah Alat Undi</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Selected Prize Card -->
        <div class="selected-prize-card" id="selectedPrizeCard" style="display: none;">
            <div class="selected-prize-content">
                <div class="selected-prize-icon">
                    <i class="fi fi-rr-gift"></i>
                </div>
                <div class="selected-prize-info">
                    <div class="selected-prize-label">Sedang Mengundi Hadiah:</div>
                    <div class="selected-prize-name" id="selectedPrizeName">-</div>
                </div>
                <div class="selected-prize-stock">
                    <small class="global-stock-display text-muted">&nbsp;</small>
                </div>
            </div>
        </div>

        <!-- Main Content: Drawing Machines Area -->
        <div class="drawing-area-wrapper">
            <!-- Machines Container -->
            <div id="machinesContainer" class="machines-grid pt-3">
                <!-- Empty State Card -->
                <div id="emptyStateCard" class="empty-state-card animate__animated animate__fadeIn">
                    <div class="empty-state-content">
                        <div class="empty-state-icon mb-4">
                            <i class="fi fi-rr-box-open text-white"></i>
                        </div>
                        <h4 class="empty-state-title mb-3 text-white">Belum Ada Alat Undi</h4>
                        <p class="empty-state-description mb-4 text-white">
                            Klik tombol "Tambah Alat Undi" di atas untuk memulai pengundian hadiah
                        </p>
                        <button class="btn-add-from-empty" id="btnAddFromEmpty">
                            <i class="fi fi-rr-add me-2"></i>
                            <span>Tambah Alat Undi Pertama</span>
                        </button>
                    </div>
                </div>
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
                                    <div class="winner-label small mb-1">Nomor Kupon</div>
                                    <div class="winner-coupon coupon-display mb-1">000000</div>
                                    <div class="winner-name mb-2 mt-2">
                                        <i class="fi fi-rr-user me-1"></i>
                                        <span class="participant-name-display">-</span>
                                    </div>
                                    <div class="winner-phone mb-2">
                                        <i class="fi fi-rr-phone-call me-1"></i>
                                        <span class="participant-phone-display">-</span>
                                    </div>
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
                            <button class="action-btn btn-redraw d-none btn-sm py-2">
                                <i class="fi fi-rr-refresh"></i>
                                <span>ULANGI UNDI</span>
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
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
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
        background: linear-gradient(90deg, #98FB98, #00BFFF);
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
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
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
        color: #00BFFF;
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
        margin-bottom: 0.75rem;
    }

    .prize-selection-label i {
        color: #00BFFF;
        font-size: 1rem;
    }

    /* Prize Filter Section */
    .prize-filter-section {
        width: 100%;
        margin-bottom: 0.75rem;
        position: relative;
    }

    .btn-prize-filter-toggle {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        color: #2c3e50;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-prize-filter-toggle:hover {
        border-color: #00BFFF;
        box-shadow: 0 2px 8px rgba(0, 191, 255, 0.1);
    }

    .btn-prize-filter-toggle i:first-child {
        color: #00BFFF;
    }

    .btn-prize-filter-toggle .toggle-icon {
        transition: transform 0.3s ease;
        color: #6c757d;
    }

    .btn-prize-filter-toggle.active .toggle-icon {
        transform: rotate(180deg);
    }

    .prize-filter-panel {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        margin-top: 0.5rem;
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1rem;
        max-height: 400px;
        overflow-y: auto;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .prize-filter-panel.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .prize-filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e9ecef;
    }

    .filter-title {
        font-weight: 700;
        font-size: 0.9rem;
        color: #2c3e50;
    }

    .filter-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-filter-action {
        padding: 0.375rem 0.75rem;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-filter-action:hover {
        background: #e9ecef;
        border-color: #00BFFF;
        color: #00BFFF;
    }

    .prize-filter-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .prize-filter-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f8f9fa;
        border: 2px solid transparent;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .prize-filter-item:hover {
        background: #e9ecef;
        border-color: #00BFFF;
    }

    .prize-filter-item:has(input:checked) {
        background: linear-gradient(135deg, rgba(152, 251, 152, 0.1) 0%, rgba(0, 191, 255, 0.1) 100%);
        border-color: #00BFFF;
    }

    .prize-filter-item:has(input:disabled) {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .prize-filter-radio {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #00BFFF;
        flex-shrink: 0;
    }

    .prize-filter-radio:disabled {
        cursor: not-allowed;
    }

    .prize-filter-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
        min-width: 0;
    }

    .prize-filter-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(152, 251, 152, 0.2) 0%, rgba(0, 191, 255, 0.2) 100%);
        border-radius: 8px;
        flex-shrink: 0;
    }

    .prize-filter-icon i {
        font-size: 1rem;
        color: #00BFFF;
    }

    .prize-filter-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: #2c3e50;
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .prize-filter-stock {
        flex-shrink: 0;
    }

    .stock-badge-small {
        display: inline-block;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    /* Selected Prize Card */
    .selected-prize-card {
        margin-top: 1rem;
        background: white;
        border: 2px solid #00BFFF;
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 191, 255, 0.2);
        animation: slideDown 0.3s ease;
    }

    .selected-prize-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .selected-prize-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        border-radius: 12px;
        flex-shrink: 0;
    }

    .selected-prize-icon i {
        font-size: 1.5rem;
        color: white;
    }

    .selected-prize-info {
        flex: 1;
        min-width: 0;
    }

    .selected-prize-label {
        font-size: 0.75rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .selected-prize-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: #00BFFF;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .selected-prize-stock {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .prize-card {
        position: relative;
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.875rem 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .prize-card:hover:not(.disabled):not(.selected) {
        border-color: #00BFFF;
        box-shadow: 0 4px 12px rgba(0, 191, 255, 0.15);
        transform: translateY(-2px);
    }

    .prize-card.selected {
        border-color: #00BFFF;
        background: linear-gradient(135deg, rgba(152, 251, 152, 0.1) 0%, rgba(0, 191, 255, 0.1) 100%);
        box-shadow: 0 4px 15px rgba(0, 191, 255, 0.2);
    }

    .prize-card.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f8f9fa;
    }

    .prize-card-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
        min-width: 0;
    }

    .prize-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(152, 251, 152, 0.2) 0%, rgba(0, 191, 255, 0.2) 100%);
        border-radius: 10px;
        flex-shrink: 0;
    }

    .prize-icon i {
        font-size: 1.25rem;
        color: #00BFFF;
    }

    .prize-card.selected .prize-icon {
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
    }

    .prize-card.selected .prize-icon i {
        color: white;
    }

    .prize-name {
        font-weight: 600;
        font-size: 0.95rem;
        color: #2c3e50;
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .prize-card.selected .prize-name {
        color: #00BFFF;
    }

    .prize-stock {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .stock-badge i {
        font-size: 0.7rem;
    }

    .stock-available {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }

    .stock-empty {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    .stock-unlimited {
        background: rgba(0, 191, 255, 0.1);
        color: #00BFFF;
    }

    .prize-card-check {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        border-radius: 50%;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .prize-card.selected .prize-card-check {
        opacity: 1;
        transform: scale(1);
    }

    .prize-card-check i {
        color: white;
        font-size: 0.75rem;
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
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(0, 191, 255, 0.3);
        white-space: nowrap;
    }

    .copy-link-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 191, 255, 0.4);
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

    /* Empty State Card */
    .empty-state-card {
        grid-column: 1 / -1;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 400px;
        padding: 2rem;
    }

    .empty-state-content {
        text-align: center;
        max-width: 500px;
    }

    .empty-state-icon {
        width: 120px;
        height: 120px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(152, 251, 152, 0.1) 0%, rgba(0, 191, 255, 0.1) 100%);
        border-radius: 50%;
        border: 3px solid rgba(0, 191, 255, 0.2);
    }

    .empty-state-icon i {
        font-size: 4rem;
        color: #00BFFF;
        opacity: 0.7;
    }

    .empty-state-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .empty-state-description {
        font-size: 1rem;
        color: #6c757d;
        line-height: 1.6;
    }

    .btn-add-from-empty {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 2rem;
        background: transparent;
        color: transparent;
        background-image: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        border: 2px solid transparent;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(0, 191, 255, 0.2);
        margin-top: 1rem;
        position: relative;
        isolation: isolate;
    }

    .btn-add-from-empty::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #ffffff;
        border-radius: 12px;
        z-index: -2;
        pointer-events: none;
    }

    .btn-add-from-empty::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 12px;
        padding: 2px;
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        -webkit-mask: 
            linear-gradient(#fff 0 0) content-box, 
            linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        z-index: -1;
    }

    .btn-add-from-empty:hover {
        transform: translateY(-3px) scale(1.05);
        background-image: linear-gradient(135deg, #90EE90 0%, #00CED1 100%);
        box-shadow: 0 8px 25px rgba(0, 191, 255, 0.4);
    }

    .btn-add-from-empty:hover::before {
        background: linear-gradient(135deg, #90EE90 0%, #00CED1 100%);
    }

    .btn-add-from-empty i {
        font-size: 1.25rem;
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: all 0.3s ease;
    }

    .btn-add-from-empty:hover i {
        background: linear-gradient(135deg, #90EE90 0%, #00CED1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transform: rotate(90deg) scale(1.1);
    }

    .btn-add-from-empty span {
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: inline-block;
    }

    .btn-add-from-empty:hover span {
        background: linear-gradient(135deg, #90EE90 0%, #00CED1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .btn-add-from-empty:active {
        transform: translateY(-1px) scale(1.02);
        box-shadow: 0 4px 15px rgba(0, 191, 255, 0.4);
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
        background: linear-gradient(90deg, #98FB98, #00BFFF);
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
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 1rem 0.75rem;
        background: linear-gradient(135deg, rgba(152, 251, 152, 0.05) 0%, rgba(0, 191, 255, 0.05) 100%);
        position: relative;
        overflow: visible;
    }

    /* Add Machine Button in Card */
    .btn-add-machine-in-card {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        border: 3px solid rgba(255, 255, 255, 0.9);
        color: white;
        font-size: 1.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 6px 20px rgba(0, 191, 255, 0.5), 0 0 0 4px rgba(0, 191, 255, 0.1);
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
            box-shadow: 0 6px 20px rgba(0, 191, 255, 0.5), 0 0 0 4px rgba(0, 191, 255, 0.1);
        }

        50% {
            box-shadow: 0 6px 25px rgba(0, 191, 255, 0.7), 0 0 0 6px rgba(0, 191, 255, 0.2);
        }
    }

    .btn-add-machine-in-card.show {
        opacity: 1;
        transform: scale(1);
        pointer-events: all;
    }

    .btn-add-machine-in-card:hover {
        transform: scale(1.15) rotate(90deg) !important;
        box-shadow: 0 8px 30px rgba(0, 191, 255, 0.8), 0 0 0 8px rgba(0, 191, 255, 0.3) !important;
        background: linear-gradient(135deg, #00BFFF 0%, #98FB98 100%);
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
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
    }

    .rolling-icon {
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
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
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
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
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
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
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
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
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
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
        background: linear-gradient(135deg, rgba(152, 251, 152, 0.1) 0%, rgba(0, 191, 255, 0.1) 100%);
        padding: 1rem 2rem;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 700;
        color: #00BFFF;
    }

    .winner-prize i {
        font-size: 1.25rem;
    }

    .winner-name {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 1rem;
        font-weight: 600;
        color: #212529;
        padding: 0.5rem 1rem;
        background: rgba(152, 251, 152, 0.1);
        border-radius: 8px;
    }

    .winner-name i {
        color: #00BFFF;
        font-size: 1rem;
    }

    .winner-phone {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: #6c757d;
        padding: 0.5rem 1rem;
        background: rgba(0, 191, 255, 0.05);
        border-radius: 8px;
    }

    .winner-phone i {
        color: #00BFFF;
        font-size: 0.9rem;
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
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%) !important;
        color: white !important;
        box-shadow: 0 4px 15px rgba(0, 191, 255, 0.4);
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .btn-start:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 191, 255, 0.5);
    }

    .btn-start.d-none {
        display: none !important;
    }

    .btn-stop {
        background: linear-gradient(135deg, #00BFFF 0%, #98FB98 100%);
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
        color: #00BFFF;
        font-size: 1.5rem;
    }

    .btn-save-winners {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 2.5rem;
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(0, 191, 255, 0.4);
    }

    .btn-save-winners:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 191, 255, 0.5);
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
        background: linear-gradient(135deg, rgba(152, 251, 152, 0.1) 0%, rgba(0, 191, 255, 0.1) 100%);
    }

    .winner-item-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
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
        color: #00BFFF;
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
        color: #00BFFF;
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

        .selected-prize-card {
            padding: 1rem;
        }

        .selected-prize-icon {
            width: 40px;
            height: 40px;
        }

        .selected-prize-icon i {
            font-size: 1.25rem;
        }

        .selected-prize-name {
            font-size: 1.1rem;
        }

        .prize-name {
            font-size: 0.875rem;
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
        let selectedParticipantIds = []; // Melacak ID peserta yang sudah terpilih

        // --- GLOBAL PRIZE SELECT ---
        const globalPrizeSelect = document.getElementById('globalPrizeSelect');
        const globalStockDisplay = document.querySelector('.global-stock-display');
        const selectedPrizeCard = document.getElementById('selectedPrizeCard');
        const selectedPrizeName = document.getElementById('selectedPrizeName');
        const prizeFilterPanel = document.getElementById('prizeFilterPanel');
        const btnPrizeFilterToggle = document.getElementById('btnPrizeFilterToggle');
        const prizeFilterRadios = document.querySelectorAll('.prize-filter-radio');
        const btnSelectAllPrizes = document.getElementById('btnSelectAllPrizes');
        const btnDeselectAllPrizes = document.getElementById('btnDeselectAllPrizes');

        // Toggle filter panel
        if (btnPrizeFilterToggle) {
            btnPrizeFilterToggle.addEventListener('click', function() {
                this.classList.toggle('active');
                prizeFilterPanel.classList.toggle('show');
            });
        }

        // Function to update selected prize display
        function updateSelectedPrizeDisplay() {
            const selectedPrizeId = globalPrizeSelect.value;
            if (selectedPrizeId) {
                const selectedRadio = document.querySelector(`.prize-filter-radio[data-prize-id="${selectedPrizeId}"]`);
                if (selectedRadio) {
                    selectedPrizeName.textContent = selectedRadio.dataset.prizeName;
                    updateGlobalStockDisplay(selectedRadio);
                    selectedPrizeCard.style.display = 'block';
                }
            } else {
                selectedPrizeName.textContent = '-';
                globalStockDisplay.innerHTML = '&nbsp;';
                selectedPrizeCard.style.display = 'none';
            }
        }

        function updateGlobalStockDisplay(prizeElement) {
            if (prizeElement) {
                const isUnlimited = prizeElement.dataset.unlimited === '1';
                const stock = prizeElement.dataset.stock;
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

        // Function to mask phone number (sensor bagian tengah)
        function maskPhoneNumber(phone) {
            if (!phone || phone.trim().length === 0) {
                return '-';
            }
            
            // Hapus karakter non-digit
            const digits = phone.replace(/\D/g, '');
            
            if (digits.length <= 4) {
                // Jika terlalu pendek, tampilkan semua dengan asterisk
                return '*'.repeat(digits.length);
            }
            
            if (digits.length <= 6) {
                // Jika 5-6 digit, tampilkan 2 digit pertama dan 2 digit terakhir
                const firstTwo = digits.substring(0, 2);
                const lastTwo = digits.substring(digits.length - 2);
                return firstTwo + '*'.repeat(digits.length - 4) + lastTwo;
            }
            
            // Untuk nomor lebih dari 6 digit, ambil 3 digit pertama dan 3 digit terakhir
            const firstThree = digits.substring(0, 3);
            const lastThree = digits.substring(digits.length - 3);
            const middleLength = digits.length - 6;
            
            // Buat string dengan bagian tengah disensor (minimal 3 asterisk)
            const masked = firstThree + '*'.repeat(Math.max(3, middleLength)) + lastThree;
            
            return masked;
        }

        // Handle prize selection from radio buttons (only 1 can be selected)
        prizeFilterRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked && !this.disabled) {
                    // Update hidden input
                    globalPrizeSelect.value = this.dataset.prizeId;
                    updateSelectedPrizeDisplay();
                    
                    // Hide panel setelah memilih hadiah
                    if (prizeFilterPanel) {
                        prizeFilterPanel.classList.remove('show');
                    }
                    if (btnPrizeFilterToggle) {
                        btnPrizeFilterToggle.classList.remove('active');
                    }
                }
            });
        });

        // Select all prizes - select first available prize
        if (btnSelectAllPrizes) {
            btnSelectAllPrizes.addEventListener('click', function() {
                const firstAvailable = document.querySelector('.prize-filter-radio:not(:disabled)');
                if (firstAvailable) {
                    firstAvailable.checked = true;
                    globalPrizeSelect.value = firstAvailable.dataset.prizeId;
                    updateSelectedPrizeDisplay();
                }
            });
        }

        // Deselect all prizes
        if (btnDeselectAllPrizes) {
            btnDeselectAllPrizes.addEventListener('click', function() {
                prizeFilterRadios.forEach(radio => {
                    radio.checked = false;
                });
                globalPrizeSelect.value = '';
                updateSelectedPrizeDisplay();
            });
        }

        // Initial update
        updateSelectedPrizeDisplay();

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
                const btnRedraw = clone.querySelector('.btn-redraw');
                const btnAddInCard = clone.querySelector('#btnAddMachineInCard');
                
                btnStart.addEventListener('click', () => this.start());
                btnRedraw.addEventListener('click', () => this.redraw());
                
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
                        toggleEmptyState();
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
                    const currentPrizeDisplay = document.getElementById('currentPrizeDisplay');
                    if (currentPrizeDisplay) {
                        currentPrizeDisplay.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return;
                }

                if (candidates.length === 0) {
                    $.toast({ text: 'Peserta habis!', icon: 'error', position: 'top-center' });
                    return;
                }

                // Lock UI - disable prize filter radios
                prizeFilterRadios.forEach(radio => {
                    radio.disabled = true;
                });
                if (btnPrizeFilterToggle) {
                    btnPrizeFilterToggle.disabled = true;
                    btnPrizeFilterToggle.style.opacity = '0.6';
                    btnPrizeFilterToggle.style.pointerEvents = 'none';
                }
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
                
                // Buttons - Hide MULAI, Show ULANGI UNDI (disabled saat rolling)
                const btnStart = this.element.querySelector('.btn-start');
                const btnRedraw = this.element.querySelector('.btn-redraw');
                
                btnStart.classList.add('d-none');
                btnStart.style.display = 'none';
                
                btnRedraw.classList.remove('d-none');
                btnRedraw.style.display = 'flex';
                btnRedraw.disabled = true;

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
                
                // Auto-stop setelah 3-5 detik (random untuk variasi)
                const stopDelay = 3000 + Math.random() * 2000; // 3-5 detik
                setTimeout(() => {
                    if (this.isRolling) {
                        this.stop();
                    }
                }, stopDelay);
            }

            stop() {
                clearInterval(this.rollInterval);
                this.isRolling = false;

                // Dapatkan kandidat yang tersedia (belum terpilih di card lain)
                let availableCandidates = candidates.filter(candidate => 
                    !selectedParticipantIds.includes(candidate.id)
                );

                if (availableCandidates.length === 0) {
                    // Reset all selections if no more unique candidates
                    selectedParticipantIds = [];
                    
                    // Reset other machines
                    machines.forEach(machine => {
                        if (machine !== this) {
                            machine.resetUI();
                        }
                    });
                    
                    // Get all candidates again
                    availableCandidates = [...candidates];
                    
                    $.toast({ 
                        text: 'Mengulang dari awal...', 
                        icon: 'info', 
                        position: 'top-center',
                        hideAfter: 2000
                    });
                }

                // Pilih pemenang hanya dari kandidat yang tersedia
                const winnerIndex = Math.floor(Math.random() * availableCandidates.length);
                const winner = availableCandidates[winnerIndex];
                const prizeId = globalPrizeSelect.value;
                const prizeName = selectedPrizeName ? selectedPrizeName.textContent : '';

                // Tambahkan ke daftar peserta terpilih
                selectedParticipantIds.push(winner.id);

                // Simpan hasil sementara
                this.currentWinner = {
                    participant_id: winner.id,
                    prize_id: prizeId,
                    participant: {
                        id: winner.id,
                        coupon_number: winner.coupon_number,
                        name: winner.name,
                        phone: winner.phone || ''
                    },
                    prize_name: prizeName
                };

                this.showWinner();
            }

            showWinner() {
                // Auto-confirm pemenang saat stop (karena button KONFIRMASI sudah dihapus)
                if (this.currentWinner) {
                    // Jika pemenang dihapus dari daftar terpilih (seharusnya tidak terjadi, tapi untuk berjaga-jaga)
                    if (!selectedParticipantIds.includes(this.currentWinner.participant_id)) {
                        selectedParticipantIds.push(this.currentWinner.participant_id);
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
                    updateSaveButton();
                }

                // Switch State
                this.element.querySelector('.state-rolling').classList.add('d-none');
                this.element.querySelector('.state-winner').classList.remove('d-none');

                // Update add button visibility
                this.updateAddButtonVisibility();

                // Update Winner Info
                this.element.querySelector('.winner-coupon').textContent = this.currentWinner.participant.coupon_number;
                this.element.querySelector('.participant-name-display').textContent = this.currentWinner.participant.name || '-';
                this.element.querySelector('.participant-phone-display').textContent = maskPhoneNumber(this.currentWinner.participant.phone);
                this.element.querySelector('.prize-name-display').textContent = this.currentWinner.prize_name;

                // Confetti
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 },
                    colors: ['#98FB98', '#00BFFF', '#ffffff']
                });

                // Enable ULANGI UNDI button and hide MULAI
                const btnStart = this.element.querySelector('.btn-start');
                const btnRedraw = this.element.querySelector('.btn-redraw');
                
                btnStart.classList.add('d-none');
                btnStart.style.display = 'none';
                
                btnRedraw.classList.remove('d-none');
                btnRedraw.style.display = 'flex';
                btnRedraw.disabled = false;
            }

            redraw() {
                // Hapus dari daftar peserta terpilih jika ada pemenang saat ini
                if (this.currentWinner) {
                    selectedParticipantIds = selectedParticipantIds.filter(
                        id => id !== this.currentWinner.participant_id
                    );
                    this.currentWinner = null;
                    
                    // Reset selected participants if all candidates are used
                    const availableCandidates = candidates.filter(candidate => 
                        !selectedParticipantIds.includes(candidate.id)
                    );
                    
                    if (availableCandidates.length === 0) {
                        // Reset all selections if no more unique candidates
                        selectedParticipantIds = [];
                        
                        // Also reset other machines' selections
                        machines.forEach(machine => {
                            if (machine !== this && machine.currentWinner) {
                                machine.resetUI();
                                machine.currentWinner = null;
                            }
                        });
                        
                        $.toast({ 
                            text: 'Semua peserta sudah diundi, mengulang dari awal...', 
                            icon: 'info', 
                            position: 'top-center',
                            hideAfter: 2000
                        });
                    }
                }
                
                // Hapus dari confirmedWinners jika ada
                const existingIndex = confirmedWinners.findIndex(w => w.machineId === this.id);
                if (existingIndex > -1) {
                    confirmedWinners.splice(existingIndex, 1);
                    updateSaveButton();
                }
                
                // Reset status konfirmasi
                this.isConfirmed = false;
                
                // Langsung start lagi (mengacak lagi)
                this.start();
            }

            resetUI() {
                // Stop rolling jika masih berjalan
                if (this.isRolling && this.rollInterval) {
                    clearInterval(this.rollInterval);
                    this.rollInterval = null;
                    this.isRolling = false;
                }
                
                // Reset UI ke initial state
                if (this.currentWinner) {
                    selectedParticipantIds = selectedParticipantIds.filter(
                        id => id !== this.currentWinner.participant_id
                    );
                    this.currentWinner = null;
                }
                
                this.isConfirmed = false;
                
                // Reset states
                this.element.querySelector('.state-rolling').classList.add('d-none');
                this.element.querySelector('.state-winner').classList.add('d-none');
                this.element.querySelector('.state-initial').classList.remove('d-none');
                
                // Reset buttons - Show MULAI, Hide ULANGI UNDI
                const btnStart = this.element.querySelector('.btn-start');
                const btnRedraw = this.element.querySelector('.btn-redraw');
                
                btnStart.classList.remove('d-none');
                btnStart.style.display = 'flex';
                
                btnRedraw.classList.add('d-none');
                btnRedraw.style.display = 'none';
                
                // Update add button visibility
                this.updateAddButtonVisibility();
                
                // Show remove button jika bisa dihapus
                if(this.canDelete) {
                    const btnRemove = this.element.querySelector('.btn-remove-machine');
                    if (btnRemove) {
                        btnRemove.classList.remove('d-none');
                        btnRemove.disabled = false;
                    }
                }
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
            toggleEmptyState();
            
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
        
        // Toggle empty state
        function toggleEmptyState() {
            const emptyCard = document.getElementById('emptyStateCard');
            if (emptyCard) {
                if (machines.length === 0) {
                    emptyCard.classList.remove('d-none');
                } else {
                    emptyCard.classList.add('d-none');
                }
            }
        }

        // Event listener untuk tombol add dari empty state
        $(document).on('click', '#btnAddFromEmpty', function() {
            addMachine();
        });

        // Initial empty state
        toggleEmptyState();
        
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

                // Reset machines tanpa mengacak lagi (gunakan resetUI, bukan redraw)
                confirmedWinners = [];
                machines.forEach(m => {
                    // Hapus dari selectedParticipantIds jika ada
                    if (m.currentWinner) {
                        selectedParticipantIds = selectedParticipantIds.filter(
                            id => id !== m.currentWinner.participant_id
                        );
                    }
                    m.currentWinner = null;
                    m.isConfirmed = false;
                    m.isRolling = false;
                    // Clear interval jika masih ada
                    if (m.rollInterval) {
                        clearInterval(m.rollInterval);
                        m.rollInterval = null;
                    }
                    // Reset UI tanpa start lagi
                    m.resetUI();
                });
                
                // Unlock UI - enable prize filter radios
                prizeFilterRadios.forEach(radio => {
                    if (!radio.dataset.prizeId || document.querySelector(`.prize-filter-radio[data-prize-id="${radio.dataset.prizeId}"]:not([disabled])`)) {
                        radio.disabled = false;
                    }
                });
                if (btnPrizeFilterToggle) {
                    btnPrizeFilterToggle.disabled = false;
                    btnPrizeFilterToggle.style.opacity = '1';
                    btnPrizeFilterToggle.style.pointerEvents = 'auto';
                }
                
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

        // Add Machine Feature
        $('#btnAddMachine').on('click', function() {
            addMachine();
        });
    });
</script>
@endpush