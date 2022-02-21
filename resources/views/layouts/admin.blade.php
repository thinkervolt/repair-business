<!DOCTYPE html>
<head>
    @include('layouts.admin.head')
</head>
<body id="page-top">
    <div id="wrapper">
        @include('layouts.admin.sidebar')
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                
                @include('layouts.admin.topbar')
                @yield('page-content')
            </div>
            @include('layouts.admin.footer')
        </div>
        @include('layouts.admin.error')
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    @include('layouts.admin.foot')


  


</body>
</html>
