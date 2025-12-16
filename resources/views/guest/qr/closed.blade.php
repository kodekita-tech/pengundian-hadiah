@extends('guest.layouts.app')

@section('title', 'Pendaftaran Ditutup - ' . $event->nm_event)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <div class="avatar avatar-lg bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                            style="width: 100px; height: 100px;">
                            <i class="fi fi-rr-lock scale-2x"></i>
                        </div>
                    </div>
                    
                    <h4 class="card-title mb-3 fw-bold">{{ $event->nm_event }}</h4>
                    
                    <div class="mb-4">
                        <span class="badge bg-warning text-dark rounded-pill px-4 py-2">
                            <i class="fi fi-rr-exclamation-triangle me-2"></i> Pendaftaran Ditutup
                        </span>
                    </div>

                    <div class="alert alert-warning border-0 mb-4">
                        <i class="fi fi-rr-info me-2"></i>
                        <p class="mb-0">
                            @if($event->status === \App\Models\Event::STATUS_DRAFT)
                                Pendaftaran untuk event ini belum dibuka. Silakan hubungi panitia untuk informasi lebih lanjut.
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

                    <div class="card border-0 shadow-sm mb-4" style="background-color: #f8f9fa;">
                        <div class="card-body p-4">
                            <div class="row text-start">
                                <div class="col-12 mb-3">
                                    <div class="d-flex align-items-center gap-2 text-muted">
                                        <i class="fi fi-rr-calendar scale-1x"></i>
                                        <div>
                                            <strong class="d-block mb-1">Periode Event</strong>
                                            <small>{{ $event->tgl_mulai->format('d M Y') }} - {{ $event->tgl_selesai->format('d M Y') }}</small>
                                        </div>
                                    </div>
                                </div>
                                @if($event->opd)
                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-2 text-muted">
                                        <i class="fi fi-rr-building scale-1x"></i>
                                        <div>
                                            <strong class="d-block mb-1">OPD</strong>
                                            <small>{{ $event->opd->nama_instansi }}</small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-muted">
                        <small>
                            <i class="fi fi-rr-info me-1"></i>
                            Jika Anda memiliki pertanyaan, silakan hubungi panitia event.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
    }
    .card {
        border-radius: 12px;
    }
    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffc107;
        border-radius: 8px;
    }
</style>
@endpush
