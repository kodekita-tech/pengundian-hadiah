@extends('errors.layout')

@section('title', '500 - Server Error')

@section('content')
<div class="container">
    <div class="row justify-content-center w-100">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card error-card border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <div class="avatar avatar-xl bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                            style="width: 120px; height: 120px;">
                            <i class="fi fi-rr-server scale-2x"></i>
                        </div>
                    </div>
                    <h1 class="display-1 fw-bold text-danger mb-3">500</h1>
                    <h3 class="mb-3">Server Error</h3>
                    <p class="text-muted mb-4">
                        Maaf, terjadi kesalahan pada server. Silakan coba lagi nanti atau hubungi administrator.
                    </p>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        @auth
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary waves-effect waves-light">
                            <i class="fi fi-rr-arrow-left me-1"></i> Kembali ke Dashboard
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-primary waves-effect waves-light">
                            <i class="fi fi-rr-sign-in me-1"></i> Login
                        </a>
                        @endauth
                        <a href="javascript:location.reload()" class="btn btn-secondary waves-effect waves-light">
                            <i class="fi fi-rr-refresh me-1"></i> Refresh Halaman
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

