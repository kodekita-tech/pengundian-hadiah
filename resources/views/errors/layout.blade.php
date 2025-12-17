<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="theme-color" content="#316AFF">
	<meta name="robots" content="noindex, nofollow">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title', 'Error') | {{ config('app.name') }}</title>
	<link rel="icon" type="image/png" href="{{ asset('assets') }}/images/favicon.png">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
		rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('assets') }}/libs/flaticon/css/all/all.css">
	<link rel="stylesheet" href="{{ asset('assets') }}/css/styles.css">
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		html,
		body {
			height: 100%;
			width: 100%;
		}

		body {
			background: #98FB98;
			/* Fallback for older browsers */
			background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
			background-attachment: fixed;
			background-repeat: no-repeat;
			background-size: cover;
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 20px;
		}

		.error-card {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(10px);
			border-radius: 20px;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
			max-width: 600px;
			width: 100%;
			margin: 0 auto;
		}

		.container {
			width: 100%;
			height: 100%;
			display: flex;
			align-items: center;
			justify-content: center;
		}
	</style>
</head>

<body>
	@yield('content')
</body>

</html>