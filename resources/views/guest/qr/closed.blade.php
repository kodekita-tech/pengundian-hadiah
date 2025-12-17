@extends('guest.layouts.app')

@section('title', 'Pendaftaran Ditutup - ' . $event->nm_event)

@section('content')
<div class="closed-page-wrapper">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <div class="card closed-card">
                    <div class="card-header-gradient"></div>
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <div class="closed-icon">
                                <i class="fi fi-rr-lock"></i>
                            </div>
                        </div>

                        <h3 class="closed-title mb-3">{{ $event->nm_event }}</h3>

                        <div class="text-center mb-3">
                            <span class="closed-badge">
                                <i class="fi fi-rr-exclamation-triangle me-2"></i>
                                Pendaftaran Ditutup
                            </span>
                        </div>

                        <div class="closed-alert mb-3">
                            <div class="alert-icon">
                                <i class="fi fi-rr-info"></i>
                            </div>
                            <div class="alert-content">
                                <p class="mb-0">
                                    @if($event->status === \App\Models\Event::STATUS_DRAFT)
                                    Pendaftaran untuk event ini belum dibuka. Silakan hubungi panitia untuk informasi
                                    lebih lanjut.
                                    @elseif($event->status === \App\Models\Event::STATUS_REGISTRATION_CLOSED)
                                    Pendaftaran untuk event ini sudah ditutup.
                                    @elseif($event->status === \App\Models\Event::STATUS_DRAWING)
                                    Event sedang dalam tahap pengundian. Pendaftaran sudah ditutup.
                                    @elseif($event->status === \App\Models\Event::STATUS_COMPLETED)
                                    Event ini sudah selesai.
                                    @else
                                    Pendaftaran untuk event ini tidak tersedia saat ini.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="closed-info-card mb-3">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fi fi-rr-calendar"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Periode Event</div>
                                    <div class="info-value">
                                        {{ $event->tgl_mulai->format('d M Y') }} - {{ $event->tgl_selesai->format('d M
                                        Y') }}
                                    </div>
                                </div>
                            </div>

                            @if($event->opd)
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fi fi-rr-building"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Penyelenggara</div>
                                    <div class="info-value">{{ $event->opd->nama_penyelenggara }}</div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="closed-footer">
                            <i class="fi fi-rr-info me-2"></i>
                            <span>Jika Anda memiliki pertanyaan, silakan hubungi panitia event.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .closed-page-wrapper {
        min-height: 100vh;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 50%, #4facfe 100%);
        padding: 1rem 0;
    }

    .closed-card {
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        position: relative;
        animation: slideUp 0.5s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card-header-gradient {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #f093fb 0%, #f5576c 50%, #4facfe 100%);
    }

    .closed-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        margin: 0 auto;
        box-shadow: 0 8px 20px rgba(245, 87, 108, 0.3);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    .closed-title {
        font-size: clamp(1.25rem, 3vw, 1.75rem);
        font-weight: 700;
        color: #212529;
        text-align: center;
        line-height: 1.3;
    }

    .closed-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1.25rem;
        background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
        color: #212529;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
    }

    .closed-alert {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem;
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 87, 34, 0.1) 100%);
        border-left: 3px solid #ffc107;
        border-radius: 10px;
    }

    .alert-icon {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }

    .alert-content {
        flex: 1;
        color: #856404;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .closed-info-card {
        background: linear-gradient(135deg, rgba(79, 172, 254, 0.05) 0%, rgba(240, 147, 251, 0.05) 100%);
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid rgba(79, 172, 254, 0.1);
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 0;
    }

    .info-item:not(:last-child) {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .info-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #6c757d;
        letter-spacing: 0.5px;
        margin-bottom: 0.2rem;
    }

    .info-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #212529;
    }

    .closed-footer {
        text-align: center;
        color: #6c757d;
        font-size: 0.85rem;
        padding-top: 0.75rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .closed-footer i {
        color: #4facfe;
    }

    @media (max-width: 768px) {
        .closed-page-wrapper {
            padding: 0.5rem 0;
        }

        .closed-card {
            margin: 0.5rem;
        }

        .closed-icon {
            width: 70px;
            height: 70px;
            font-size: 1.75rem;
        }

        .closed-title {
            font-size: 1.25rem;
        }

        .closed-badge {
            padding: 0.45rem 1rem;
            font-size: 0.85rem;
        }
    }
</style>
@endpush