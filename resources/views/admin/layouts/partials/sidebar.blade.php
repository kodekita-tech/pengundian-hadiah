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
            <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fi fi-rr-apps"></i>
                    <span class="menu-label">Dashboard</span>
                </a>
            </li>
            <li class="menu-heading">
                <span class="menu-label">Management</span>
            </li>
            @if(auth()->user()->role !== 'admin_penyelenggara')
            <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a class="menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="fi fi-rr-users"></i>
                    <span class="menu-label">Users</span>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('admin.opd.*') ? 'active' : '' }}">
                <a class="menu-link {{ request()->routeIs('admin.opd.*') ? 'active' : '' }}" href="{{ route('admin.opd.index') }}">
                    <i class="fi fi-rr-building"></i>
                    <span class="menu-label">Penyelenggara</span>
                </a>
            </li>
            @endif
            <li class="menu-item {{ request()->routeIs('admin.event.*') ? 'active' : '' }}">
                <a class="menu-link {{ request()->routeIs('admin.event.*') ? 'active' : '' }}" href="{{ route('admin.event.index') }}">
                    <i class="fi fi-rr-calendar"></i>
                    <span class="menu-label">Event</span>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('admin.winner.*') ? 'active' : '' }}">
                <a class="menu-link {{ request()->routeIs('admin.winner.*') ? 'active' : '' }}" href="{{ route('admin.winner.index') }}">
                    <i class="fi fi-rr-trophy"></i>
                    <span class="menu-label">Pemenang</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

