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
            <span>{{ __('repair-business.link_dashboard') }}</span></a>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCustomers"
            aria-expanded="true" aria-controls="collapseCustomers">
            <i class="fas fa-users"></i>
            <span>{{ __('repair-business.link_customers') }}</span>
        </a>
        <div id="collapseCustomers" class="collapse" aria-labelledby="collapseCustomers"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item"
                    href="{{ route('create-customer') }}">{{ __('repair-business.link_create-customer') }}</a>
                <a class="collapse-item"
                    href="{{ route('index-customer') }}">{{ __('repair-business.link_customers') }}</a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRepairs"
            aria-expanded="true" aria-controls="collapseRepairs">
            <i class="fas fa-tools"></i>
            <span>{{ __('repair-business.link_repairs') }}</span>
        </a>
        <div id="collapseRepairs" class="collapse" aria-labelledby="collapseRepairs" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item"
                    href="{{ route('create-repair') }}">{{ __('repair-business.link_create-repair') }}</a>
                <a class="collapse-item"
                    href="{{ route('index-repair') }}">{{ __('repair-business.link_repairs') }}</a>
                <a class="collapse-item"
                    href="{{ route('setting-repair') }}">{{ __('repair-business.link_settings') }}</a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInvoices"
            aria-expanded="true" aria-controls="collapseInvoices">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>{{ __('repair-business.link_invoices') }}</span>
        </a>
        <div id="collapseInvoices" class="collapse" aria-labelledby="collapseInvoices" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <form method="POST" id="create-invoice" action="{{ route('create-invoice', [0, 'empty']) }}">
                    @csrf
                    <a href="#" class="collapse-item"
                        onclick="document.getElementById('create-invoice').submit();">{{ __('repair-business.link_create-invoice') }}</a>
                </form>
                <a class="collapse-item"
                    href="{{ route('index-invoice') }}">{{ __('repair-business.link_invoices') }}</a>
                <a class="collapse-item"
                    href="{{ route('setting-invoice') }}">{{ __('repair-business.link_settings') }}</a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInventory"
            aria-expanded="true" aria-controls="collapseInventory">
            <i class="fas fa-th"></i>
            <span>{{ __('repair-business.link_inventory') }}</span>
        </a>
        <div id="collapseInventory" class="collapse" aria-labelledby="collapseInventory"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item"
                    href="{{ route('inventory-index-product') }}">{{ __('repair-business.link_products') }}</a>
                <a class="collapse-item"
                    href="{{ route('inventory-index-transaction') }}">{{ __('repair-business.link_transactions') }}</a>
                <a class="collapse-item"
                    href="{{ route('inventory-index-category') }}">{{ __('repair-business.link_categories') }}</a>
            </div>
        </div>
    </li>
 
    @if (Auth::user()->role == 'admin')
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseManagement"
            aria-expanded="true" aria-controls="collapseManagement">
            <i class="fas fa-sliders-h"></i>
            <span>{{ __('repair-business.link_management') }}</span>
        </a>
        <div id="collapseManagement" class="collapse" aria-labelledby="collapseManagement"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('create-report') }}">{{ __('repair-business.link_create-report') }}</a>
                <a class="collapse-item" href="{{ route('create-register-report') }}">{{ __('repair-business.link_register-report') }}</a>
                <a class="collapse-item" href="{{ route('index-payment') }}">{{ __('repair-business.link_payments') }}</a>
                <a class="collapse-item" href="{{ route('index-setting') }}">{{ __('repair-business.link_settings') }}</a>
                <a class="collapse-item" href="{{ route('register') }}">{{ __('repair-business.link_create-user') }}</a>
                <a class="collapse-item" href="{{ route('users') }}">{{ __('repair-business.link_manage-users') }}</a>
                <a class="collapse-item" href="{{ route('index-log') }}">{{ __('repair-business.link_activity-log') }}</a>
                <a class="collapse-item" href="{{ route('index-trash') }}">{{ __('repair-business.link_trash') }}</a>
            </div>
    </li>
    @endif
    <hr class="sidebar-divider d-none d-md-block">
</ul>
