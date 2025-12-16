<aside class="app-menubar" id="appMenubar">
    <div class="app-navbar-brand">
        <a class="navbar-brand-logo" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('assets') }}/images/logo.svg" alt="GXON Admin Dashboard Logo">
        </a>
        <a class="navbar-brand-mini visible-light" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('assets') }}/images/logo-text.svg" alt="GXON Admin Dashboard Logo">
        </a>
        <a class="navbar-brand-mini visible-dark" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('assets') }}/images/logo-text-white.svg" alt="GXON Admin Dashboard Logo">
        </a>
    </div>
    <nav class="app-navbar" data-simplebar>
        <ul class="menubar">
            <li class="menu-item">
                <a class="menu-link" href="{{ route('admin.dashboard') }}">
                    <i class="fi fi-rr-apps"></i>
                    <span class="menu-label">Dashboard</span>
                </a>
            </li>
            <li class="menu-heading">
                <span class="menu-label">Management</span>
            </li>
            @if(auth()->user()->role !== 'admin_opd')
            <li class="menu-item">
                <a class="menu-link" href="{{ route('admin.users.index') }}">
                    <i class="fi fi-rr-users"></i>
                    <span class="menu-label">Users</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="{{ route('admin.opd.index') }}">
                    <i class="fi fi-rr-building"></i>
                    <span class="menu-label">OPD</span>
                </a>
            </li>
            @endif
            <li class="menu-item">
                <a class="menu-link" href="{{ route('admin.event.index') }}">
                    <i class="fi fi-rr-calendar"></i>
                    <span class="menu-label">Event</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

