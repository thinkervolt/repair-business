<ul class="navbar-nav bg-secondary sidebar sidebar-dark accordion @if (Session::has('sidebar-position')) @if (Session::get('sidebar-position') == 'close') toggled @endif @endif "
    id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-wrench"></i>
        </div>
        <div class="sidebar-brand-text mx-3">{{ config('app.name', 'REPAIR-BUSINESS') }}</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCustomers"
            aria-expanded="true" aria-controls="collapseCustomers">
            <i class="fas fa-users"></i>
            <span>Customers</span>
        </a>
        <div id="collapseCustomers" class="collapse" aria-labelledby="collapseCustomers"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('create-customer') }}">Create Customer</a>
                <a class="collapse-item" href="{{ route('index-customer') }}">Customers</a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRepairs"
            aria-expanded="true" aria-controls="collapseRepairs">
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
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInvoices"
            aria-expanded="true" aria-controls="collapseInvoices">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Invoices</span>
        </a>
        <div id="collapseInvoices" class="collapse" aria-labelledby="collapseInvoices" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <form method="POST" id="create-invoice" action="{{ route('create-invoice', [0, 'empty']) }}">
                    @csrf
                    <a href="#" class="collapse-item"
                        onclick="document.getElementById('create-invoice').submit();">Create Invoice</a>
                </form>
                <a class="collapse-item" href="{{ route('index-invoice') }}">Invoices</a>
                <a class="collapse-item" href="{{ route('setting-invoice') }}">Settings</a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInventory"
            aria-expanded="true" aria-controls="collapseInventory">
            <i class="fas fa-th"></i>
            <span>Inventory</span>
        </a>
        <div id="collapseInventory" class="collapse" aria-labelledby="collapseInventory"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('inventory-index-product') }}">Products</a>
                <a class="collapse-item" href="{{ route('inventory-index-transaction') }}">Transactions</a>

                <a class="collapse-item" href="{{ route('inventory-index-category') }}">Categories</a>
            </div>
        </div>
    </li>
    <div class="topbar-divider d-none d-sm-block"></div>
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePayments"
            aria-expanded="true" aria-controls="collapsePayments">
            <i class="fas fa-money-bill-alt"></i>
            <span>Payments</span>
        </a>
        <div id="collapsePayments" class="collapse" aria-labelledby="collapsePayments"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">

                <a class="collapse-item" href="{{ route('new-payment') }}">Create Payment</a>

                <a class="collapse-item" href="{{ route('index-payment') }}">Payments</a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports"
            aria-expanded="true" aria-controls="collapseReports">
            <i class="far fa-file-alt"></i>
            <span>Reports</span>
        </a>
        <div id="collapseReports" class="collapse" aria-labelledby="collapseReports"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('create-report') }}">Create Report</a>
                <a class="collapse-item" href="{{ route('create-register-report') }}">Register Report</a>
            </div>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('index-setting') }}">
            <i class="fas fa-sliders-h"></i>
            <span>Settings</span></a>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('index-trash') }}">
            <i class="fas fa-fw fa-trash-alt"></i>
            <span>Trash</span></a>
    </li>
    <hr class="sidebar-divider d-none d-md-block">
</ul>
