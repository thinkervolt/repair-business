@extends('layouts.admin')
@section('page') Payments @endsection

@section('search')
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="post" action="{{route('search-payment')}}" >
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
  

    <form class="form-inline mr-auto w-100 navbar-search" method="post" action="{{route('search-payment')}}" >
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
            <h1 class="h3 mb-0 text-gray-800">Payments</h1>
           
          </div>



          <!-- Content Row -->
          <div class="row">

          <div class="col">

                <!-- INDEX -->

@if(!$payments->isEmpty())
    <div class="table-responsive">

        <table class="table table-sm mt-3 table-hover ">
            <thead>
            <tr>
                <th  scope="col">DATE</th>
                <th  scope="col">INVOICE</th>
                <th  scope="col">AMOUNT</th>
                <th  scope="col">METHOD</th>
                <th  scope="col">REFERENCE</th>
                <th  scope="col"></th>
            </tr>
            </thead>
            <tbody>

            @foreach($payments as $payment)
                    <tr>
                    <td>{{ date_format($payment->created_at,"M d, Y")}}</td>
                    <td>@if(isset($payment->invoice)) <a href="{{route('view-invoice',$payment->invoice)}}" >{{$payment->invoice}}</a> @endif </td>
                    <td>$ {{number_format((float)$payment->amount, 2, '.', ',')}} </td>
                    <td class="text-uppercase">{{$payment->method}}</td>
                    <td>{{$payment->ref}}</td>
                    <td>
                        <a class="btn btn-primary btn-block btn-sm" href="{{route('view-payment',$payment)}}"><i class="fas fa-binoculars"></i> View</a>

                    </td>
                    </tr>
            
            @endforeach

            </tbody>
        </table>
        {{ $payments->links() }}
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
