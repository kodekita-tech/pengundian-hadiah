@extends('guest.layouts.app')

@section('content')
<div class="page-layout">

    <div class="auth-cover-wrapper">
        <div class="row g-0">
            <div class="col-lg-6">
                <div class="auth-cover"
                    style="background-image: url({{ asset('assets') }}/images/auth/auth-cover-bg.png);">
                    <div class="clearfix">
                        <img src="{{ asset('assets') }}/images/auth/auth.png" alt="" class="img-fluid cover-img ms-5">
                        <div class="auth-content">
                            <h1 class="display-6 fw-bold">Welcome Back!</h1>
                            <p>{{ __('Welcome to Pengundian CFD') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 align-self-center">
                <div class="p-3 p-sm-5 maxw-450px m-auto auth-inner" data-simplebar>
                    <div class="mb-4 text-center">
                        <a href="index.html" aria-label="GXON logo">
                            <img class="visible-light" src="{{ asset('assets') }}/images/logo-full.svg" alt="GXON logo">
                            <img class="visible-dark" src="{{ asset('assets') }}/images/logo-full-white.svg"
                                alt="GXON logo">
                        </a>
                    </div>
                    <div class="text-center mb-5">
                        <h5 class="mb-1">{{ __('Welcome to Pengundian CFD') }}</h5>
                    </div>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label" for="loginEmail">{{ __('Email Address') }}</label>
                            <input id="loginEmail" type="email"
                                class="form-control @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}" placeholder="info@example.com" required autocomplete="email"
                                autofocus>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="loginPassword">{{ __('Password') }}</label>
                            <input id="loginPassword" type="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="********"
                                name="password" required autocomplete="current-password">

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                                    <label class="form-check-label" for="rememberMe"> {{ __('Remember Me') }} </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button type="submit" value="Submit"
                                class="btn btn-primary waves-effect waves-light w-100">{{ __('Login') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection