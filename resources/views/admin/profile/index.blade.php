@extends('admin.layouts.app')

@section('title', 'My Profile')

@section('content')
@php
$badgeClass = match($user->role) {
'superadmin' => 'bg-danger',
'developer' => 'bg-primary',
'admin_opd' => 'bg-success',
default => 'bg-secondary'
};
$roleText = ucfirst(str_replace('_', ' ', $user->role));
@endphp

<div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
    <div class="clearfix">
        <h1 class="app-page-title">Profile</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Profile</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-4 align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="position-relative">
                            <div class="avatar avatar-xxl {{ $user->avatar_color }} text-white d-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px; font-size: 2rem; border-radius: 50%; aspect-ratio: 1/1;">
                                {{ $user->initials }}
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="fw-bold mb-0">{{ $user->name }}</h4>
                            <small class="mb-2">{{ $user->email }}</small>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <span class="badge badge-sm px-2 rounded-pill {{ $badgeClass }} text-white">{{ $roleText
                                    }}</span>
                                @if($user->opd)
                                <span class="badge badge-sm px-2 rounded-pill text-bg-info">{{ $user->opd->nama_instansi
                                    }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-sm-12">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Basic Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="mb-1 d-block text-muted small">Full Name</span>
                            <p class="text-dark fw-semibold mb-0">{{ $user->name }}</p>
                        </div>
                        <div class="mb-3">
                            <span class="mb-1 d-block text-muted small">Email</span>
                            <p class="text-dark fw-semibold mb-0">{{ $user->email }}</p>
                        </div>
                        <div class="mb-3">
                            <span class="mb-1 d-block text-muted small">Role</span>
                            <p class="text-dark fw-semibold mb-0">
                                <span class="badge {{ $badgeClass }}">{{ $roleText }}</span>
                            </p>
                        </div>
                        @if($user->opd)
                        <div class="mb-3">
                            <span class="mb-1 d-block text-muted small">OPD</span>
                            <p class="text-dark fw-semibold mb-0">{{ $user->opd->nama_instansi }}</p>
                            @if($user->opd->singkatan)
                            <small class="text-muted">({{ $user->opd->singkatan }})</small>
                            @endif
                        </div>
                        @endif
                        <div class="mb-2">
                            <span class="mb-1 d-block text-muted small">Joined Date</span>
                            <p class="text-dark fw-semibold mb-0">{{ $user->created_at->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8 col-sm-12">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Account Settings</h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <form action="{{ route('admin.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                                    <small class="text-muted">Email cannot be changed</small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Role</label>
                                    <input type="text" class="form-control" value="{{ $roleText }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        name="password" placeholder="Leave blank if you don't want to change password">
                                    <small class="text-muted">Leave blank if you don't want to change password</small>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="btn btn-secondary waves-effect waves-light me-2">Cancel</a>
                                <button type="submit" class="btn btn-success waves-effect waves-light">Save
                                    Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection