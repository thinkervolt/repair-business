@extends('layouts.admin')
@section('page')  Transaction @endsection

@section('search')
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="post" action="{{route('inventory-search-transaction')}}" >
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
  

    <form class="form-inline mr-auto w-100 navbar-search" method="post" action="{{route('inventory-search-transaction')}}" >
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
            <h1 class="h3 mb-0 text-gray-800">Transactions</h1>
           
          </div>



          <!-- Content Row -->
          <div class="row">

          <div class="col">

                <!-- INDEX -->

@if(!$transactions->isEmpty())
    <div class="table-responsive">

        <table class="table table-sm mt-3 table-hover ">
            <thead>
            <tr>
                <th  scope="col">DATE</th>
                <th  scope="col">TRANSACTION</th>
                <th  scope="col">INVOICE</th>
                <th  scope="col">PRODUCT</th>
                <th  scope="col">QUANTITY</th>
                <th  scope="col">SUPPLIER</th>
                <th  scope="col"></th>
            </tr>
            </thead>
            <tbody>

            @foreach($transactions as $transaction)
                    <tr>
                    <td>{{ date_format($transaction->created_at,"M d, Y")}}</td>
                    <td class="text-uppercase">{{$transaction->transaction}}</td>
                    <td>@if(isset($transaction->invoice)) <a href="{{route('view-invoice',$transaction->invoice)}}" >{{$payment->invoice}}</a> @endif </td>
                    <td>{{$transaction->product->name}}</td>
                    <td><p class="m-0 p-0 @if($transaction->transaction == 'purchase') text-success @endif @if($transaction->transaction == 'sell') text-danger @endif">@if($transaction->transaction == 'sell') - @endif @if($transaction->transaction == 'purchase') + @endif{{$transaction->quantity}}</p></td>
                    <td>{{$transaction->product->supplier}}</td>
                    <td>
                        <a class="btn btn-primary btn-block btn-sm" href="{{route('inventory-view-transaction',$transaction)}}"><i class="fas fa-binoculars"></i> View</a>

                    </td>
                    </tr>
            
            @endforeach

            </tbody>
        </table>
        {{ $transactions->links() }}
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
