<nav class="navbar navbar-expand navbar-light bg-white static-top osahan-nav sticky-top">
    &nbsp;&nbsp;
    <button aria-label="Minimize sidebar" class="btn btn-link btn-sm text-secondary order-1 order-sm-0" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button> &nbsp;&nbsp;
    <a class="navbar-brand mr-1" aria-label="homepage" href="{{ route('home') }}"><img class="img-fluid" loading="lazy" alt="logo" width="50px" height="50px" src="{{ asset('assets/img/logo.png') }}"></a>
    <!-- Navbar Search -->
    <form action="{{ route('video.search') }}" method="GET" class="d-none d-md-inline-block form-inline mr-0 mr-md-5 my-2 my-md-0 osahan-navbar-search">
        <div class="input-group">
            {{ csrf_field() }}

            <input type="text" name="search" class="form-control" placeholder="Video id, Author, Category ..." required>
            <div class="input-group-append">
                <button class="btn btn-light" aria-label="Search" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
    <!-- Navbar -->
    <ul class="navbar-nav ml-auto ml-md-0 osahan-right-navbar">
        @if(Auth::check())
            <li class="nav-item mx-1">
                <a class="nav-link" href="{{ route('video.create') }}">
                    <i class="fas fa-plus-circle fa-fw"></i>
                    Upload
                </a>
            </li>
            <li class="nav-item dropdown no-arrow mx-1">
                <a class="nav-link dropdown-toggle" aria-label="dropdown" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell fa-fw"></i>
                    <span class="badge badge-danger">9+</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="alertsDropdown">
                    <a class="dropdown-item" href="#"><i class="fas fa-fw fa-edit "></i> &nbsp; Action</a>
                    <a class="dropdown-item" href="#"><i class="fas fa-fw fa-headphones-alt "></i> &nbsp; Another action</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#"><i class="fas fa-fw fa-star "></i> &nbsp; Something else here</a>
                </div>
            </li>
            <li class="nav-item dropdown no-arrow mx-1">
                @include('shared.navbar.parameters')
            </li>
        @endif
        <li class="nav-item dropdown no-arrow osahan-right-navbar-user">
            @if(Auth::check())
                <a class="nav-link dropdown-toggle user-dropdown-link" href="#" aria-label="dropdown" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img alt="Avatar" loading="lazy" src="/{{ Auth::user()->avatar }}">
                    {{ Auth::user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="{{ route('channel.index', ['userId' => Auth::user()->id]) }}"><i class="fas fa-fw fa-user-circle"></i> &nbsp; My channel</a>
                    <a class="dropdown-item" href="subscriptions.html"><i class="fas fa-fw fa-video"></i> &nbsp; Subscriptions</a>
                    <a class="dropdown-item" href="settings.html"><i class="fas fa-fw fa-cog"></i> &nbsp; Settings</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}" data-toggle="modal" data-target="#logoutModal"><i class="fas fa-fw fa-sign-out-alt"></i> &nbsp; Logout</a>
                </div>
            @else
                <a class="nav-link dropdown-toggle user-dropdown-link" aria-label="dropdown" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Create an account / Login
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="{{ route('register') }}"><i class="fas fa-fw fa-user-circle"></i> &nbsp; Create an account</a>
                    <a class="dropdown-item" href="{{ route('login') }}"><i class="fas fa-fw fa-video"></i> &nbsp; Login</a>
                </div>
            @endif
        </li>
    </ul>
</nav>
