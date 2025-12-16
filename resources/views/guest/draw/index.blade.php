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
            <!-- Drawing Area -->
                    <div class="col-lg-8">
                        <div class="draw-card">
                            <!-- Prize Input -->
                            <div class="prize-input-section">
                                <label class="prize-label">
                                    <i class="fi fi-rr-gift"></i>
                                    <span>Hadiah yang diundi</span>
                                </label>
                                <input type="text" 
                                    class="prize-input" 
                                    id="prizeName" 
                                    placeholder="Contoh: Sepeda Motor, Kulkas, dll..." 
                                    autocomplete="off">
                            </div>
                            
                            <!-- Drawing Display -->
                            <div class="draw-display">
                            <!-- Initial State -->
                                <div id="drawInitial" class="draw-state">
                                    <div class="state-icon initial-icon">
                                        <i class="fi fi-rr-box-open"></i>
                                </div>
                                    <h3 class="state-title">Siap untuk mengundi?</h3>
                                    <p class="state-subtitle">Pastikan peserta sudah diimport oleh panitia</p>
                            </div>

                            <!-- Rolling State -->
                                <div id="drawRolling" class="draw-state d-none">
                                    <div class="state-icon rolling-icon">
                                        <i class="fi fi-rr-refresh"></i>
                                </div>
                                    <div class="rolling-content">
                                        <div class="rolling-text" id="rollingCoupon">000000</div>
                                        <div class="rolling-label">Nomor Kupon</div>
                                    </div>
                            </div>

                            <!-- Winner State -->
                                <div id="drawWinner" class="draw-state d-none">
                                    <div class="winner-celebration">
                                        <div class="confetti-burst"></div>
                                        <div class="state-icon winner-icon">
                                            <i class="fi fi-rr-trophy"></i>
                                        </div>
                                    </div>
                                    <div class="winner-badge">
                                        <i class="fi fi-rr-star"></i>
                                        <span>PEMENANG</span>
                                        <i class="fi fi-rr-star"></i>
                                    </div>
                                    <div class="winner-content">
                                        <div class="winner-coupon" id="winnerCoupon">000000</div>
                                        <div class="winner-label">Nomor Kupon</div>
                                        <div class="winner-prize" id="winnerPrize">
                                            <i class="fi fi-rr-gift"></i>
                                            <span>Hadiah: -</span>
                                        </div>
                                </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="action-buttons">
                                <button class="action-btn btn-start" id="btnStart">
                                    <i class="fi fi-rr-play"></i>
                                    <span>MULAI ACAK</span>
                                </button>
                                <button class="action-btn btn-stop d-none" id="btnStop">
                                    <i class="fi fi-rr-stop"></i>
                                    <span>STOP & PILIH PEMENANG</span>
                                </button>
                                <button class="action-btn btn-reset d-none" id="btnReset">
                                    <i class="fi fi-rr-refresh"></i>
                                    <span>RESET / UNDI LAGI</span>
                                </button>
                        </div>
                    </div>
                </div>

                <!-- Winners List -->
                    <div class="col-lg-4">
                        <div class="winners-card">
                            <div class="winners-header">
                                <i class="fi fi-rr-trophy"></i>
                                <h3>Daftar Pemenang</h3>
                        </div>
                            <div class="winners-list" id="winnerList">
                                <div class="list-group list-group-flush">
                                @forelse($winners as $winner)
                                        <div class="list-group-item winner-item">
                                            <div class="winner-item-icon">
                                                <i class="fi fi-rr-trophy"></i>
                                            </div>
                                            <div class="winner-item-content">
                                                <div class="winner-item-header">
                                                    <div class="winner-item-name">{{ $winner->participant->name ? substr($winner->participant->name, 0, 2) . str_repeat('*', max(0, strlen($winner->participant->name) - 2)) : '***' }}</div>
                                                    <div class="winner-item-coupon">{{ $winner->participant->coupon_number }}</div>
                                                </div>
                                                <div class="winner-item-footer">
                                                    <div class="winner-item-prize">
                                                        <i class="fi fi-rr-gift"></i>
                                                        <span>{{ $winner->prize_name }}</span>
                                                    </div>
                                                    <div class="winner-item-time">{{ $winner->drawn_at->format('H:i') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="winners-empty">
                                            <i class="fi fi-rr-confetti"></i>
                                            <p>Belum ada pemenang</p>
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
    let isRolling = false;
    let rollInterval;
    const shortlink = "{{ $event->shortlink }}";
    
    // Initialize SimpleBar for winner list
    if (typeof SimpleBar !== 'undefined') {
        new SimpleBar(document.getElementById('winnerList'));
    }
    
    // Load candidates on start
    loadCandidates();

    function loadCandidates() {
        axios.get(`{{ route('draw.candidates', $event->shortlink) }}`)
            .then(res => {
                candidates = res.data;
                if(candidates.length === 0) {
                     $('#btnStart').prop('disabled', true).html('<i class="fi fi-rr-exclamation-triangle"></i><span>TIDAK ADA PESERTA</span>');
                }
            })
            .catch(err => {
                if (err.response && (err.response.status === 401 || err.response.status === 419)) {
                     window.location.reload();
                } else {
                     $.toast({ 
                         text: 'Gagal memuat peserta.', 
                         icon: 'error',
                         position: 'top-center'
                     });
                }
            });
    }

    // Start Button
    $('#btnStart').on('click', function() {
        const prizeName = $('#prizeName').val().trim();
        if (!prizeName) {
            $.toast({ 
                text: 'Harap isi nama hadiah terlebih dahulu!', 
                icon: 'warning', 
                position: 'top-center'
            });
            $('#prizeName').focus();
            return;
        }

        if (candidates.length === 0) {
             $.toast({ 
                text: 'Peserta habis atau belum diimport!', 
                icon: 'error', 
                position: 'top-center'
            });
            return;
        }

        // Lock prize input
        $('#prizeName').prop('disabled', true);
        
        // UI Change
        $('#drawInitial').addClass('d-none');
        $('#drawWinner').addClass('d-none');
        $('#drawRolling').removeClass('d-none');
        
        $('#btnStart').addClass('d-none');
        $('#btnStop').removeClass('d-none');

        // Start Animation Loop - Only show coupon number
        isRolling = true;
        rollInterval = setInterval(() => {
            const random = candidates[Math.floor(Math.random() * candidates.length)];
            $('#rollingCoupon').text(random.coupon_number);
        }, 50);
    });

    // Stop Button
    $('#btnStop').on('click', function() {
        clearInterval(rollInterval);
        isRolling = false;

        $(this).prop('disabled', true).html('<i class="fi fi-rr-spinner fi-spin"></i><span>MEMILIH...</span>');

        // Pick random winner from candidates
        const winnerIndex = Math.floor(Math.random() * candidates.length);
        const winner = candidates[winnerIndex];

        // Send to backend
        axios.post(`{{ route('draw.winner', $event->shortlink) }}`, {
            participant_id: winner.id,
            prize_name: $('#prizeName').val()
        })
        .then(res => {
            showWinner(res.data.winner);
             candidates.splice(winnerIndex, 1);
        })
        .catch(err => {
            $.toast({ 
                text: err.response?.data?.message || 'Gagal menyimpan pemenang.', 
                icon: 'error',
                position: 'top-center'
            });
             
             // Reset UI if failed
             $('#drawRolling').addClass('d-none');
             $('#drawInitial').removeClass('d-none');
            $('#btnStop').addClass('d-none').prop('disabled', false).html('<i class="fi fi-rr-stop"></i><span>STOP & PILIH PEMENANG</span>');
             $('#btnStart').removeClass('d-none');
             $('#prizeName').prop('disabled', false);
        });
    });

    // Reset Button
    $('#btnReset').on('click', function() {
        $('#drawWinner').addClass('d-none');
        $('#drawInitial').removeClass('d-none');
        
        $('#btnReset').addClass('d-none');
        $('#btnStart').removeClass('d-none');
        
        $('#prizeName').val('').prop('disabled', false).focus();
    });

    function showWinner(winnerData) {
        // Stop button hide
        $('#btnStop').addClass('d-none').prop('disabled', false).html('<i class="fi fi-rr-stop"></i><span>STOP & PILIH PEMENANG</span>');
        
        // Show Winner UI - Only show coupon number
        $('#drawRolling').addClass('d-none');
        $('#drawWinner').removeClass('d-none');
        
        // Update texts - Only coupon number, no name
        $('#winnerCoupon').text(winnerData.participant.coupon_number);
        $('#winnerPrize').html('<i class="fi fi-rr-gift"></i><span>Hadiah: ' + winnerData.prize_name + '</span>');
        
        // Confetti!
        confetti({
            particleCount: 200,
            spread: 70,
            origin: { y: 0.6 },
            colors: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#ffffff']
        });
        
        // Add to list with masked name
        addWinnerToList(winnerData);
        
        // Show Reset Button
        setTimeout(() => {
            $('#btnReset').removeClass('d-none');
        }, 1000);
    }

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
            <div class="list-group-item winner-item animate__animated animate__fadeInLeft">
                <div class="winner-item-icon">
                    <i class="fi fi-rr-trophy"></i>
                </div>
                <div class="winner-item-content">
                    <div class="winner-item-header">
                        <div class="winner-item-name">${maskedName}</div>
                        <div class="winner-item-coupon">${winner.participant.coupon_number}</div>
                    </div>
                    <div class="winner-item-footer">
                        <div class="winner-item-prize">
                            <i class="fi fi-rr-gift"></i>
                            <span>${winner.prize_name}</span>
                        </div>
                        <div class="winner-item-time">${timeStr}</div>
                    </div>
                </div>
            </div>
        `;
        
        // Find list-group (should always exist now)
        let $listGroup = $('#winnerList').find('.list-group');
        
        // If list-group doesn't exist (shouldn't happen, but just in case)
        if ($listGroup.length === 0) {
            // Check if there are existing winner items
            const existingItems = $('#winnerList').children('.list-group-item, .winner-item');
            if (existingItems.length > 0) {
                // Wrap existing items
                existingItems.wrapAll('<div class="list-group list-group-flush"></div>');
            } else {
                // Create new list-group only if no items exist
                $('#winnerList').html('<div class="list-group list-group-flush"></div>');
            }
            $listGroup = $('#winnerList').find('.list-group');
        }
        
        // Remove "Belum ada pemenang" if exists (inside list-group)
        $listGroup.find('.winners-empty').remove();
        
        // Prepend new winner to list-group
        $listGroup.prepend(html);
    }

    // Copy Link Feature
    $('#btnCopyLink').on('click', function() {
        const link = window.location.href;
        navigator.clipboard.writeText(link).then(() => {
            $.toast({ 
                text: 'Link berhasil disalin!', 
                position: 'bottom-center',
                icon: 'success',
                loader: false
            });
        }).catch(() => {
            $.toast({ 
                text: 'Gagal menyalin link.', 
                position: 'bottom-center',
                icon: 'error',
                loader: false
            });
        });
    });
});
</script>
@endpush
