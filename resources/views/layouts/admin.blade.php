<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>@include('layouts.components.head')</head>

<body id="page-top" class="@if (Session::has('sidebar-position')) @if (Session::get('sidebar-position') == 'close')  sidebar-toggled @endif @endif ">

    <div id="wrapper">
        @include('layouts.components.sidebar')
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('layouts.components.topbar')
                @yield('page-content')
            </div>
            @include('layouts.components.footer')
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    @include('layouts.components.scripts')

    @yield('scripts')


    @if (Route::current()->getName() == 'view-invoice')
        @include('components.invoice-barcode')
    @elseif(Route::current()->getName() == 'view-repair')
        @include('components.repair-barcode')
    @else
        @include('components.barcode')
    @endif


    @if (Route::current()->getName() == 'view-invoice')
        @include('components.invoice-barcode')
    @elseif(Route::current()->getName() == 'view-repair')
        @include('components.repair-barcode')
    @else
        @include('components.barcode')
    @endif


</body>

</html>
