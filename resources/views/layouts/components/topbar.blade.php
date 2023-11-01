<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    @if (Session::has('sidebar-position'))
        @if (Session::get('sidebar-position') == 'close')
            <a id="sidebarToggleTop" href="{{ route('set-sidebar-position', 'open') }}"
                class="btn btn-link text-secondary rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </a>
        @endif
        @if (Session::get('sidebar-position') == 'open')
            <a id="sidebarToggleTop" href="{{ route('set-sidebar-position', 'close') }}"
                class="btn btn-link text-secondary rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </a>
        @endif
    @else
        <a id="sidebarToggleTop" href="{{ route('set-sidebar-position', 'close') }}"
            class="btn btn-link text-secondary rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </a>
    @endif
    @yield('search')
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow d-sm-none">

            @yield('mobile-search-buttom')
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                @yield('mobile-search')
            </div>
        </li>
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                @if (isset($notifications_count))
                    @if ($notifications_count > 0)
                        <span class="badge badge-danger badge-counter">{{ $notifications_count }}</span>
                    @endif
                @endif
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header bg-secondary">
                    Notifications
                </h6>
                @if (isset($notifications))

                    @foreach ($notifications as $notification)
                        <a class="dropdown-item d-flex align-items-center"
                            href="{{ route('notification', $notification) }}">
                            <div class="mr-3">
                                <div class="icon-circle bg-primary">
                                    <i class="fas fa-bell fa-fw text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">
                                    {{ date_format($notification->created_at, 'M d, Y') }}</div>
                                <span class="font-weight-bold">{{ $notification->message }}</span>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="dropdown-item d-flex align-items-center">
                        <div class="mr-3">
                            <div class="icon-circle bg-primary">
                                <i class="fas  fa-bell-slash fa-fw text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500"> </div>
                            <span class="font-weight-bold"> There is no Notifications</span>
                        </div>
                    </div>

                @endif

            </div>
        </li>
        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                <i class="fas fa-user "></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ route('profile') }}">

                    <i class="fas fa-address-card fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="{{ route('index-log') }}">
                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                    Activity Log
                </a>
                <a class="dropdown-item" href="{{ route('register') }}">
                    <i class="fas fa-user-plus fa-sm fa-fw mr-2 text-gray-400"></i>
                    Create User
                </a>
                <a class="dropdown-item" href="{{ route('users') }}">
                    <i class="fas fa-users fa-sm fa-fw mr-2 text-gray-400"></i>
                    Manage Users
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault();  document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>
