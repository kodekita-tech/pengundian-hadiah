<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="theme-color" content="#316AFF">
	<meta name="robots" content="index, follow">
	<meta name="author" content="LayoutDrop">
	<meta name="format-detection" content="telephone=no">
	<meta name="description" content="Sistem Pengundian CFD - Pendaftaran dan Pengundian Berhadiah">
	<title>@yield('title', 'Pengundian CFD') | {{ config('app.name') }}</title>
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
	<link rel="stylesheet" href="{{ asset('assets') }}/css/styles.css">
	@stack('styles')
</head>

<body>
	@yield('content')
	<script>
		document.documentElement.setAttribute('data-bs-theme', 'light');
	</script>
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="{{ asset('assets') }}/libs/global/global.min.js"></script>
	<script src="{{ asset('assets') }}/js/appSettings.js"></script>
	<script>
		setAppSettings({ appTheme: 'light' });
	</script>
	<script src="{{ asset('assets') }}/js/main.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.documentElement.setAttribute('data-bs-theme', 'light');
		});
	</script>
	@stack('scripts')
</body>

</html>