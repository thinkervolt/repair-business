<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link text-secondary d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->

    @yield('search')

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">

            @yield('mobile-search-buttom')

            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">

                @yield('mobile-search')



            </div>
        </li>

        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                @if ($notifications_count > 0)
                    <span class="badge badge-danger badge-counter">{{ $notifications_count }}</span>
                @endif
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header bg-secondary">
                    Notifications
                </h6>
                @if (!$notifications->isEmpty())

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

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span
                    class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                <i class="fas fa-user "></i>
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
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
                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                    class="d-none">
                    @csrf
                </form>

            </div>
        </li>

    </ul>

</nav>