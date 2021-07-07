@extends('layouts.admin')
@section('page') Repairs @endsection

@section('search')
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="post" action="{{route('search-repair',[$task,$id])}}" >
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
  

    <form class="form-inline mr-auto w-100 navbar-search" method="post" action="{{route('search-repair',[$task,$id])}}" >
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
            <h1 class="h3 mb-0 text-gray-800">Repairs</h1>
           
          </div>



          <!-- Content Row -->
          <div class="row">

          <div class="col">

                <!-- INDEX -->

@if(!$repairs->isEmpty())
    <div class="table-responsive">

        <table class="table table-sm mt-3 table-hover ">
            <thead>
            <tr>
                <th  scope="col">ID</th>
                <th  scope="col">CUSTOMER</th>
                <th  scope="col">TARGET</th>
                <th  scope="col">REQUEST</th>
                <th  scope="col">STATUS</th>
                <th  scope="col">PRIORITY</th>
                <th  scope="col"></th>
                <th  scope="col"></th>
            </tr>
            </thead>
            <tbody>

            @foreach($repairs as $repair)
                <tr>
                    <td>{{$repair->id}}</td>
                    <td>
                        @if(isset($repair->customer))
                        <p class="m-0 p-0">{{$repair->customer_data->first_name ?? ''}} {{$repair->customer_data->last_name ?? ''}}</p>
                        <p class="m-0 p-0">{{preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $repair->customer_data->phone ?? '')}}</p>
                        <p class="m-0 p-0">{{$repair->customer_data->email ?? ''}}</p>
                        <p class="m-0 p-0">{{$repair->customer_data->company ?? ''}}</p>
                        @endif
                    </td>
                    <td>{{$repair->target}}</td>
                    <td>{{$repair->request}}</td>
                    <td>@if(isset($repair->status)) <p  class="font-weight-bold text-uppercase m-0 p-0 text-{{$repair->status_data->color ?? ''}}"  >{{$repair->status_data->name ?? ''}} </p> @endif</td>
                    <td>@if(isset($repair->priority)) <p class="font-weight-bold text-uppercase m-0 p-0 text-{{$repair->priority_data->color ?? ''}}">{{$repair->priority_data->name ?? ''}} </p> @endif</td>
                    <td>{{ date_format($repair->created_at,"M d, Y")}}</td>
                    <td >
                        <a class="btn btn-primary btn-block btn-sm" href="{{route('view-repair',$repair)}}"><i class="fas fa-binoculars"></i> View</a>
                        @if(isset($task))
                            @if($task == 'invoice')
                                <form class="mt-1" method="POST" action="{{route('create-item-repair-invoice',[$repair,$id])}}">
                                @csrf

                                <button type="submit" class="btn btn-primary btn-block btn-sm" ><i class="fas fa-plus"></i> Add to Invoice</a>
                                </form>

                            @endif

                        @endif
                    </td>
                </tr>
            
            @endforeach

            </tbody>
        </table>
        {{ $repairs->links() }}
    </div>
@else

<div class="alert alert-secondary" role="alert">
Nothing has been found!
</div>
@endif

<!-- END INDEX -->
              

          </div>

            </div>

          

        </div>
        <!-- /.container-fluid -->

@endsection
