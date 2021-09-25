<!DOCTYPE html>
<html lang="en">

<head>

  <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="shortcut icon" href="{{asset('vendor/fontawesome-free/favicon.ico')}}">

  <title>{{ config('app.name', 'REPAIR-BUSINESS') }} - @yield('page')</title>
  

  <!-- Custom fonts for this template-->
  <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-secondary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fas fa-wrench"></i>
        </div>
        <div class="sidebar-brand-text mx-3">{{ config('app.name', 'Laravel') }}</div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Nav Item - Pages Collapse Menu -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCustomers" aria-expanded="true" aria-controls="collapseCustomers">
            <i class="fas fa-users"></i>
          <span>Customers</span>
        </a>
        <div id="collapseCustomers" class="collapse" aria-labelledby="collapseCustomers" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="{{ route('create-customer') }}">Create Customer</a>
            <a class="collapse-item" href="{{ route('index-customer') }}">Customers</a>
          </div>
        </div>
      </li>
      <hr class="sidebar-divider">

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRepairs" aria-expanded="true" aria-controls="collapseRepairs">
          <i class="fas fa-tools"></i>
          <span>Repairs</span>
        </a>
        <div id="collapseRepairs" class="collapse" aria-labelledby="collapseRepairs" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="{{ route('create-repair') }}">Create Repair</a>
            <a class="collapse-item" href="{{ route('index-repair') }}">Repairs</a>
            <a class="collapse-item" href="{{ route('setting-repair') }}">Settings</a>
          </div>
        </div>
      </li>

      <hr class="sidebar-divider">

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInvoices" aria-expanded="true" aria-controls="collapseInvoices">
          <i class="fas fa-file-invoice-dollar"></i>
          <span>Invoices</span>
        </a>
        <div id="collapseInvoices" class="collapse" aria-labelledby="collapseInvoices" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <form method="POST" id="create-invoice" action="{{ route('create-invoice',[0,'empty']) }}">

            @csrf
            <a href="#" class="collapse-item" onclick="document.getElementById('create-invoice').submit();">Create Invoice</a>
            </form>
            <a class="collapse-item" href="{{ route('index-invoice') }}">Invoices</a>
            <a class="collapse-item" href="{{ route('setting-invoice') }}">Settings</a>
          </div>
        </div>
      </li>

      <hr class="sidebar-divider">

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInventory" aria-expanded="true" aria-controls="collapseInventory">
          <i class="fas fa-th"></i>
          <span>Inventory</span>
        </a>
        <div id="collapseInventory" class="collapse" aria-labelledby="collapseInventory" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
 
            <a  class="collapse-item" href="{{ route('inventory-index-product') }}">Products</a>
            <a  class="collapse-item" href="{{ route('inventory-index-product') }}">Transactions</a>
         
            <a class="collapse-item" href="{{ route('inventory-index-category') }}">Categories</a>
          </div>
        </div>
      </li>

      <hr class="sidebar-divider">

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePayments" aria-expanded="true" aria-controls="collapsePayments">
          <i class="fas fa-money-bill-alt"></i>
          <span>Payments</span>
        </a>
        <div id="collapsePayments" class="collapse" aria-labelledby="collapsePayments" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
 
            <a  class="collapse-item" href="{{ route('new-payment') }}">Create Payment</a>
         
            <a class="collapse-item" href="{{ route('index-payment') }}">Payments</a>
          </div>
        </div>
      </li>

      <hr class="sidebar-divider">

      <li class="nav-item">
        <a class="nav-link" href="{{ route('create-report') }}">
        <i class="far fa-file-alt"></i>
          <span>Reports</span></a>
      </li>

      <hr class="sidebar-divider">

      <li class="nav-item">
        <a class="nav-link" href="{{ route('index-setting') }}">
          <i class="fas fa-sliders-h"></i>
          <span>Settings</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <li class="nav-item">
        <a class="nav-link" href="{{ route('index-trash') }}">
          <i class="fas fa-fw fa-trash-alt"></i>
          <span>Trash</span></a>
      </li>


      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
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
              <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">

              @yield('mobile-search')



              </div>
            </li>

            <!-- Nav Item - Alerts -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                @if($notifications_count > 0) <span class="badge badge-danger badge-counter">{{$notifications_count}}</span> @endif
              </a>
              <!-- Dropdown - Alerts -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header bg-secondary">
                  Notifications
                </h6>
                @if(!$notifications->isEmpty())

                  @foreach($notifications as $notification)
                  <a class="dropdown-item d-flex align-items-center" href="{{route('notification',$notification)}}">
                    <div class="mr-3">
                      <div class="icon-circle bg-primary">
                      <i class="fas fa-bell fa-fw text-white"></i>
                      </div>
                    </div>
                    <div>
                      <div class="small text-gray-500"> {{ date_format($notification->created_at,"M d, Y")}}</div>
                      <span class="font-weight-bold">{{$notification->message}}</span>
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
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                <i class="fas fa-user "></i>
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
              <a class="dropdown-item" href="{{ route('profile') }}">
           
                  <i class="fas fa-address-card fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
                </a>
                <a class="dropdown-item" href="{{route('index-log')}}">
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
                 
              
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();  document.getElementById('logout-form').submit();">
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
        <!-- End of Topbar -->



        @yield('page-content')

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; {{ config('app.name', 'Laravel') }} 2019-{{date('Y')}}</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>



  <!-- Bootstrap core JavaScript-->
  <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

  <!-- Custom scripts for all pages-->
  <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>


</body>

</html>
