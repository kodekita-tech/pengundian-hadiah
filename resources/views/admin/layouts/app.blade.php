<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('admin.layouts.partials.head')
</head>

<body>
    <div class="page-layout">

        @include('admin.layouts.partials.header')

        @include('admin.layouts.partials.sidebar')

        <main class="app-wrapper">
            <div class="container">
                @yield('content')
            </div>
        </main>

        @include('admin.layouts.partials.footer')

    </div>

    @include('admin.layouts.partials.scripts')
</body>

</html>