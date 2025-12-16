<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#316AFF">
<meta name="format-detection" content="telephone=no">

<title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Laravel') }}</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="icon" type="image/png" href="{{ asset('assets') }}/images/favicon.png">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets') }}/images/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
    rel="stylesheet">

<link rel="stylesheet" href="{{ asset('assets') }}/libs/flaticon/css/all/all.css">
<link rel="stylesheet" href="{{ asset('assets') }}/libs/lucide/lucide.css">
<link rel="stylesheet" href="{{ asset('assets') }}/libs/fontawesome/css/all.min.css">
<link rel="stylesheet" href="{{ asset('assets') }}/libs/simplebar/simplebar.css">
<link rel="stylesheet" href="{{ asset('assets') }}/libs/node-waves/waves.css">
<link rel="stylesheet" href="{{ asset('assets') }}/libs/bootstrap-select/css/bootstrap-select.min.css">

<link rel="stylesheet" href="{{ asset('assets') }}/libs/flatpickr/flatpickr.min.css">
<link rel="stylesheet" href="{{ asset('assets') }}/libs/datatables/datatables.min.css">
<link rel="stylesheet" href="{{ asset('assets') }}/css/styles.css">
<style>
    .avatar.text-white {
        font-weight: 600;
        font-size: 0.75rem;
        line-height: 1;
    }

    .avatar-sm.text-white {
        font-size: 0.65rem;
    }
</style>
@stack('styles')