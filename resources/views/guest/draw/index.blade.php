@extends('guest.layouts.app')

@section('title', $event->nm_event)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Event Header -->
            <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);">
                <div class="card-body p-4 text-white text-center">
                    <h1 class="display-5 fw-bold mb-2">{{ $event->nm_event }}</h1>
                    <p class="lead mb-0 opacity-75">
                        <i class="fi fi-rr-calendar me-1"></i> 
                        {{ $event->tgl_mulai->format('d M Y') }} - {{ $event->tgl_selesai->format('d M Y') }}
                    </p>
                    <div class="mt-3">
                        <button class="btn btn-light rounded-pill px-4" id="btnCopyLink">
                            <i class="fi fi-rr-copy me-1"></i> Copy Link Halaman Ini
                        </button>
                    </div>
                </div>
            </div>

            <!-- Drawing Area -->
            <div class="row g-4">
                <div class="col-md-7">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <div class="form-group">
                                <label class="form-label text-muted small fw-bold text-uppercase">Hadiah yang diundi</label>
                                <input type="text" class="form-control form-control-lg fw-bold text-center border-2" 
                                    id="prizeName" placeholder="Contoh: Sepeda Motor, Kulkas..." 
                                    style="border-color: #e5e7eb; border-radius: 12px;">
                            </div>
                        </div>
                        <div class="card-body p-5 text-center d-flex flex-column justify-content-center align-items-center" style="min-height: 400px; background: #f9fafb;">
                            
                            <!-- Initial State -->
                            <div id="drawInitial">
                                <div class="mb-4">
                                    <i class="fi fi-rr-box-open text-primary" style="font-size: 80px;"></i>
                                </div>
                                <h3 class="text-muted">Siap untuk mengundi?</h3>
                                <p class="text-muted mb-0">Pastikan peserta sudah diimport oleh panitia.</p>
                            </div>

                            <!-- Rolling State -->
                            <div id="drawRolling" class="d-none w-100">
                                <div class="display-1 fw-bold text-primary mb-2" id="rollingName" style="word-break: break-all;">
                                    ROLLING...
                                </div>
                                <div class="h3 text-muted" id="rollingCoupon">0000</div>
                            </div>

                            <!-- Winner State -->
                            <div id="drawWinner" class="d-none w-100">
                                <div class="mb-2">
                                    <span class="badge bg-warning text-dark h4 px-3 py-2 rounded-pill">🎉 PEMENANG 🎉</span>
                                </div>
                                <div class="display-3 fw-bold text-dark mb-2 animate__animated animate__tada" id="winnerName">
                                    NAMA PEMENANG
                                </div>
                                <div class="h2 text-muted mb-4" id="winnerCoupon">0000</div>
                                <div class="h5 text-primary" id="winnerPrize">Hadiah: -</div>
                            </div>

                        </div>
                        <div class="card-footer bg-white border-0 p-4">
                             <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-lg rounded-pill py-3 fw-bold" id="btnStart" style="font-size: 1.2rem;">
                                    <i class="fi fi-rr-play me-2"></i> MULAI ACAK
                                </button>
                                <button class="btn btn-danger btn-lg rounded-pill py-3 fw-bold d-none" id="btnStop" style="font-size: 1.2rem;">
                                    <i class="fi fi-rr-stop me-2"></i> STOP & PILIH PEMENANG
                                </button>
                                <button class="btn btn-secondary btn-lg rounded-pill py-3 fw-bold d-none" id="btnReset" style="font-size: 1.2rem;">
                                    <i class="fi fi-rr-refresh me-2"></i> RESET / UNDI LAGI
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Winners List -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-bottom border-light py-3 px-4">
                            <h5 class="mb-0 fw-bold"><i class="fi fi-rr-trophy me-2 text-warning"></i> Daftar Pemenang</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" id="winnerList" style="max-height: 500px; overflow-y: auto;">
                                @forelse($winners as $winner)
                                    <div class="list-group-item px-4 py-3 border-light">
                                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 fw-bold text-primary">{{ $winner->participant->name }}</h6>
                                            <small class="text-muted">{{ $winner->participant->coupon_number }}</small>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <small class="text-muted"><i class="fi fi-rr-gift me-1"></i> {{ $winner->prize_name }}</small>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $winner->drawn_at->format('H:i') }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted">
                                        <i class="fi fi-rr-confetti mb-2" style="font-size: 32px; opacity: 0.5;"></i>
                                        <p class="mb-0">Belum ada pemenang.</p>
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
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
<style>
    .list-group-item {
        transition: all 0.2s;
    }
    .list-group-item:hover {
        background-color: #f9fafb;
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
    let isRolling = false;
    let rollInterval;
    const shortlink = "{{ $event->shortlink }}";
    
    // Load candidates on start
    loadCandidates();

    function loadCandidates() {
        axios.get(`{{ route('draw.candidates', $event->shortlink) }}`)
            .then(res => {
                candidates = res.data;
                console.log(`Loaded ${candidates.length} candidates`);
                if(candidates.length === 0) {
                     $('#btnStart').prop('disabled', true).text('TIDAK ADA PESERTA');
                }
            })
            .catch(err => {
                console.error(err);
                if (err.response.status === 401 || err.response.status === 419) {
                     window.location.reload();
                } else {
                     $.toast({ text: 'Gagal memuat peserta.', icon: 'error' });
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

        // Start Animation Loop
        isRolling = true;
        rollInterval = setInterval(() => {
            const random = candidates[Math.floor(Math.random() * candidates.length)];
            $('#rollingName').text(random.name);
            $('#rollingCoupon').text(random.coupon_number);
        }, 50); // Fast switching 50ms
    });

    // Stop Button
    $('#btnStop').on('click', function() {
        // Stop animation but don't show result yet, let's slow down or pick winner in backend
        clearInterval(rollInterval);
        isRolling = false;

        $(this).prop('disabled', true).html('<i class="fi fi-rr-spinner fi-spin me-2"></i> MEMILIH...');

        // Pick random winner from LOCAL candidates list first (for visual consistency)
        // Ideally we should asking backend to pick one, but for simple app we pick here and send to backend
        // Or better: Send request to "pick winner" and backend picks random. 
        // My implementation plan said: "POST winner to backend". 
        // But backend needs ID. So we pick ID here.
        
        // Better approach for consistency: Pick 1 random candidate from array
        const winnerIndex = Math.floor(Math.random() * candidates.length);
        const winner = candidates[winnerIndex];

        // Send to backend
        axios.post(`{{ route('draw.winner', $event->shortlink) }}`, {
            participant_id: winner.id,
            prize_name: $('#prizeName').val()
        })
        .then(res => {
            // Success
            showWinner(res.data.winner);
            
            // Remove winner from local candidates list
             candidates.splice(winnerIndex, 1);
        })
        .catch(err => {
            console.error(err);
             $.toast({ text: err.response?.data?.message || 'Gagal menyimpan pemenang.', icon: 'error' });
             
             // Reset UI if failed
             $('#drawRolling').addClass('d-none');
             $('#drawInitial').removeClass('d-none');
             $('#btnStop').addClass('d-none').prop('disabled', false).text('STOP & PILIH PEMENANG');
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
        $('#btnStop').addClass('d-none').prop('disabled', false).html('<i class="fi fi-rr-stop me-2"></i> STOP & PILIH PEMENANG');
        
        // Show Winner UI
        $('#drawRolling').addClass('d-none');
        $('#drawWinner').removeClass('d-none');
        
        // Update texts
        $('#winnerName').text(winnerData.participant.name);
        $('#winnerCoupon').text(winnerData.participant.coupon_number);
        $('#winnerPrize').text('Hadiah: ' + winnerData.prize_name);
        
        // Confetti!
        confetti({
            particleCount: 150,
            spread: 70,
            origin: { y: 0.6 }
        });
        
        // Add to list
        addWinnerToList(winnerData);
        
        // Show Reset Button
        setTimeout(() => {
            $('#btnReset').removeClass('d-none');
        }, 1000); // 1 sec delay to avoid accidental click
    }

    function addWinnerToList(winner) {
        const date = new Date(winner.drawn_at);
        const timeStr = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
        
        const html = `
            <div class="list-group-item px-4 py-3 border-light animate__animated animate__fadeInLeft">
                <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                    <h6 class="mb-0 fw-bold text-primary">${winner.participant.name}</h6>
                    <small class="text-muted">${winner.participant.coupon_number}</small>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <small class="text-muted"><i class="fi fi-rr-gift me-1"></i> ${winner.prize_name}</small>
                    <small class="text-muted" style="font-size: 0.75rem;">${timeStr}</small>
                </div>
            </div>
        `;
        
        // Remove "Belum ada pemenang" if exists
        $('#winnerList').find('.text-center').remove();
        
        // Prepend
        $('#winnerList').prepend(html);
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
        });
    });
});
</script>
@endpush
