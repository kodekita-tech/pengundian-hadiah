@extends('guest.layouts.app')

@section('title', 'Pendaftaran Berhasil - ' . $event->nm_event)

@section('content')
<div class="success-page-wrapper">
    <!-- Background Animation -->
    <div class="success-background">
        <div class="confetti-container"></div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <!-- Main Success Card -->
                <div class="success-card">
                    <!-- Success Header -->
                    <div class="success-header">
                        <div class="success-icon-wrapper">
                            <div class="success-icon-circle">
                                <i class="fi fi-rr-check"></i>
                            </div>
                            <div class="success-ripple"></div>
                            <div class="success-ripple delay-1"></div>
                            <div class="success-ripple delay-2"></div>
                        </div>
                        <h1 class="success-title">Pendaftaran Berhasil!</h1>
                        <p class="success-subtitle">Terima kasih telah mendaftar pada event ini</p>
                    </div>

                    <!-- Event Info Section -->
                    <div class="info-section">
                        <div class="section-header">
                            <i class="fi fi-rr-calendar-alt"></i>
                            <h3>{{ $event->nm_event }}</h3>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fi fi-rr-calendar"></i>
                                </div>
                                <div class="info-content">
                                    <span class="info-label">Periode Event</span>
                                    <span class="info-value">{{ $event->tgl_mulai->format('d M Y') }} - {{
                                        $event->tgl_selesai->format('d M Y') }}</span>
                                </div>
                            </div>
                            @if($event->opd)
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fi fi-rr-building"></i>
                                </div>
                                <div class="info-content">
                                    <span class="info-label">Organisasi</span>
                                    <span class="info-value">{{ $event->opd->nama_instansi }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Participant Data Section -->
                    <div class="data-section">
                        <div class="section-header">
                            <i class="fi fi-rr-user"></i>
                            <h3>Data Pendaftaran Anda</h3>
                        </div>
                        <div class="data-grid">
                            <div class="data-item">
                                <div class="data-label">Nama Lengkap</div>
                                <div class="data-value">{{ $participant->name }}</div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Nomor HP</div>
                                <div class="data-value">{{ $participant->phone }}</div>
                            </div>
                            <div class="data-item highlight">
                                <div class="data-label">
                                    <i class="fi fi-rr-ticket me-1"></i>
                                    Nomor Kupon
                                </div>
                                <div class="data-value coupon-number">{{ $participant->coupon_number }}</div>
                                <div class="data-hint">Simpan nomor kupon ini dengan baik!</div>
                            </div>
                        </div>
                    </div>

                    <!-- Important Notice -->
                    <div class="notice-section">
                        <div class="notice-header">
                            <i class="fi fi-rr-info"></i>
                            <strong>Penting untuk Diingat</strong>
                        </div>
                        <ul class="notice-list">
                            <li>
                                <i class="fi fi-rr-shield-check"></i>
                                <span>Simpan nomor kupon Anda dengan baik</span>
                            </li>
                            <li>
                                <i class="fi fi-rr-trophy"></i>
                                <span>Nomor kupon akan digunakan saat pengundian</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-section">
                        <button class="btn-action btn-primary-action" onclick="window.print()">
                            <i class="fi fi-rr-print"></i>
                            <span>Cetak / Simpan</span>
                        </button>
                        <a href="{{ route('qr.show', $token) }}" class="btn-action btn-secondary-action">
                            <i class="fi fi-rr-arrow-left"></i>
                            <span>Kembali ke Form</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
    .success-page-wrapper {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        position: relative;
        overflow: hidden;
    }

    .success-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        overflow: hidden;
    }

    .confetti-container {
        position: absolute;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.3)"/><circle cx="80" cy="40" r="1.5" fill="rgba(255,255,255,0.3)"/><circle cx="40" cy="80" r="2" fill="rgba(255,255,255,0.3)"/></svg>') repeat;
        animation: float 20s infinite linear;
    }

    @keyframes float {
        0% {
            transform: translateY(0);
        }

        100% {
            transform: translateY(-100px);
        }
    }

    .success-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        padding: 0;
        position: relative;
        z-index: 1;
        overflow: hidden;
        margin-top: 2rem;
        margin-bottom: 2rem;
    }

    .success-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
    }

    /* Success Header */
    .success-header {
        text-align: center;
        padding: 3rem 2rem 2rem;
        background: linear-gradient(180deg, rgba(102, 126, 234, 0.05) 0%, transparent 100%);
    }

    .success-icon-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 1.5rem;
    }

    .success-icon-circle {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        position: relative;
        z-index: 2;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        animation: scaleIn 0.6s ease-out;
    }

    .success-icon-circle i {
        animation: checkmark 0.6s ease-out 0.3s both;
    }

    @keyframes scaleIn {
        0% {
            transform: scale(0);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    @keyframes checkmark {
        0% {
            transform: scale(0) rotate(45deg);
        }

        50% {
            transform: scale(1.2) rotate(45deg);
        }

        100% {
            transform: scale(1) rotate(0deg);
        }
    }

    .success-ripple {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100px;
        height: 100px;
        border: 2px solid rgba(102, 126, 234, 0.3);
        border-radius: 50%;
        animation: ripple 2s infinite;
    }

    .success-ripple.delay-1 {
        animation-delay: 0.5s;
    }

    .success-ripple.delay-2 {
        animation-delay: 1s;
    }

    @keyframes ripple {
        0% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        100% {
            transform: translate(-50%, -50%) scale(2.5);
            opacity: 0;
        }
    }

    .success-title {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
        animation: fadeInUp 0.6s ease-out 0.2s both;
    }

    .success-subtitle {
        color: #6c757d;
        font-size: 1.1rem;
        margin-bottom: 0;
        animation: fadeInUp 0.6s ease-out 0.4s both;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Info Section */
    .info-section,
    .data-section {
        padding: 2rem;
        border-top: 1px solid #e9ecef;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .section-header i {
        font-size: 1.5rem;
        color: #667eea;
    }

    .section-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #212529;
        margin: 0;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .info-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }

    .info-icon {
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

    .info-content {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #212529;
    }

    /* Data Section */
    .data-grid {
        display: grid;
        gap: 1rem;
    }

    .data-item {
        padding: 1.25rem;
        background: #f8f9fa;
        border-radius: 12px;
        border: 2px solid transparent;
        transition: all 0.3s;
    }

    .data-item.highlight {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-color: #667eea;
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.2);
    }

    .data-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .data-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #212529;
    }

    .data-value.coupon-number {
        font-size: 2.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: 2px;
        font-family: 'Courier New', monospace;
    }

    .data-hint {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }

    /* Notice Section */
    .notice-section {
        margin: 2rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
        border-radius: 12px;
        border-left: 4px solid #667eea;
    }

    .notice-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
        color: #667eea;
        font-weight: 700;
    }

    .notice-header i {
        font-size: 1.25rem;
    }

    .notice-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .notice-list li {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        color: #495057;
        font-size: 0.95rem;
    }

    .notice-list li i {
        color: #667eea;
        margin-top: 0.2rem;
        flex-shrink: 0;
    }

    /* Action Buttons */
    .action-section {
        padding: 2rem;
        border-top: 1px solid #e9ecef;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-action {
        flex: 1;
        min-width: 200px;
        padding: 1rem 2rem;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        transition: all 0.3s;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-primary-action {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-primary-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        color: white;
    }

    .btn-secondary-action {
        background: white;
        color: #667eea;
        border: 2px solid #667eea;
    }

    .btn-secondary-action:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    /* Print Styles */
    @media print {
        .success-page-wrapper {
            background: white;
        }

        .success-background,
        .btn-action,
        .action-section {
            display: none !important;
        }

        .success-card {
            box-shadow: none;
            border: 1px solid #dee2e6;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .success-header {
            padding: 2rem 1.5rem 1.5rem;
        }

        .info-section,
        .data-section,
        .action-section {
            padding: 1.5rem;
        }

        .success-title {
            font-size: 1.5rem;
        }

        .data-value.coupon-number {
            font-size: 2rem;
        }

        .btn-action {
            min-width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
    $(document).ready(function() {
    // Confetti animation on success
    setTimeout(() => {
        if (typeof confetti !== 'undefined') {
            // Multiple confetti bursts
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#667eea', '#764ba2', '#f093fb', '#ffffff']
            });
            
            setTimeout(() => {
                confetti({
                    particleCount: 50,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 },
                    colors: ['#667eea', '#764ba2']
                });
            }, 250);
            
            setTimeout(() => {
                confetti({
                    particleCount: 50,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 },
                    colors: ['#764ba2', '#f093fb']
                });
            }, 400);
        }
    }, 500);
});
</script>
@endpush