@extends('layouts.admin')
@section('page') Logs @endsection

@section('search')
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="post" action="{{route('search-log')}}" >
    @csrf

    
        <div class="input-group">
              <input type="text" class="form-control bg-light border-0 small" id="search"  name="search" value="@if(isset($search)){{$search}}@endif" aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-secondary" type="button">
                  <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>


@endsection

@section('mobile-search-buttom')
    <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-search fa-fw"></i>
    </a>
@endsection

@section('mobile-search')
  

    <form class="form-inline mr-auto w-100 navbar-search" method="post" action="{{route('search-log')}}" >
    @csrf
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" id="search"  name="search" value="@if(isset($search)){{$search}}@endif" aria-label="Search" aria-describedby="basic-addon2">
                <div class="input-group-append">
                    <button class="btn btn-secondary" type="button">
                            <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
        </div>
    </form>
@endsection

@section('page-content')


        <!-- Begin Page Content -->
        <div class="container-fluid">
          @if(session()->has('error'))
              <div class="alert {{ session()->get('alert') }} alert-dismissible fade show">
                  <li>{{ session()->get('error') }}</li>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
          @endif
          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Logs</h1>
          </div>


            @if(!$logs->isEmpty())


            <div class="row mb-2">
                <div class="col-12">
                @foreach($logs as $log)
                    <div class="row">
                        <div class="col-md">
                            <p class="small text-muted p-0 m-0">{{$log->data ?? '. . .'}}</p>
                        </div>
                        <div class="col-md-auto">
                            <p class="small text-muted p-0 m-0">by {{ $log->user_data->name ?? '. . .'}}</p>
                        </div>
                        <div class="col-md-auto">
                            <p class="small text-muted p-0 m-0">{{ date_format($log->created_at ?? '. . .',"M d, Y h:iA")}}</p>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>

                {{ $logs->links() }}



            @endif


            
</div>
@endsection
