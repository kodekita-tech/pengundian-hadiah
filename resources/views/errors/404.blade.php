@extends('errors.layout')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('content')
<div class="container">
    <div class="row justify-content-center w-100">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card error-card border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <div class="avatar avatar-xl bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                            style="width: 120px; height: 120px;">
                            <i class="fi fi-rr-exclamation-triangle scale-2x"></i>
                        </div>
                    </div>
                    <h1 class="display-1 fw-bold text-danger mb-3">404</h1>
                    <h3 class="mb-3">Halaman Tidak Ditemukan</h3>
                    <p class="text-muted mb-4">
                        Maaf, halaman yang Anda cari tidak ditemukan atau telah dipindahkan.
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
                        <a href="javascript:history.back()" class="btn btn-secondary waves-effect waves-light">
                            <i class="fi fi-rr-undo me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

