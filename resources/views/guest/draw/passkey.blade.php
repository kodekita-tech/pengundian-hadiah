@extends('guest.layouts.app')

@section('title', 'Verifikasi Passkey - ' . $event->nm_event)

@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center"
    style="background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);">
    <div class="card shadow-lg border-0" style="max-width: 500px; width: 100%; border-radius: 20px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="mb-3">
                    <i class="fi fi-rr-lock" style="font-size: 48px; color: #00BFFF;"></i>
                </div>
                <h4 class="fw-bold mb-2">Protected Event</h4>
                <p class="text-muted mb-0">{{ $event->nm_event }}</p>
            </div>

            <div class="alert alert-info border-0 mb-4"
                style="background-color: rgba(0, 191, 255, 0.1); border-left: 4px solid #00BFFF;">
                <i class="fi fi-rr-info me-2"></i>
                <small>Event ini dilindungi dengan passkey. Silakan masukkan passkey untuk mengakses halaman
                    pengundian.</small>
            </div>

            <form action="{{ route('draw.verify', $event->shortlink) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="passkey" class="form-label fw-semibold">Passkey</label>
                    <input type="password" class="form-control form-control-lg @error('passkey') is-invalid @enderror"
                        id="passkey" name="passkey" placeholder="Masukkan passkey" autofocus required>
                    @error('passkey')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3"
                    style="background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%); border: none;">
                    <i class="fi fi-rr-unlock me-2"></i>
                    Verifikasi & Lanjutkan
                </button>
            </form>

            <div class="text-center text-muted">
                <small>
                    <i class="fi fi-rr-shield-check me-1"></i>
                    Akses aman dengan passkey
                </small>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    body {
        overflow: hidden;
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        background-attachment: fixed;
        min-height: 100vh;
    }

    .form-control:focus {
        border-color: #00BFFF;
        box-shadow: 0 0 0 0.2rem rgba(0, 191, 255, 0.25);
    }
</style>
@endpush
@endsection