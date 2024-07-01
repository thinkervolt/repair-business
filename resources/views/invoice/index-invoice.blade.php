@extends('layouts.admin')
@section('page')
    Invoices
@endsection

@section('search')
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="post"
        action="{{ route('search-invoice') }}">
        @csrf


        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" id="search" name="search"
                value="@if (isset($search)) {{ $search }} @endif" aria-label="Search"
                aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-secondary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>
@endsection

@section('mobile-search-buttom')
    <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-search fa-fw"></i>
    </a>
@endsection

@section('mobile-search')
    <form class="form-inline mr-auto w-100 navbar-search" method="post" action="{{ route('search-invoice') }}">
        @csrf
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" id="search" name="search"
                value="@if (isset($search)) {{ $search }} @endif" aria-label="Search"
                aria-describedby="basic-addon2">
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
        @if (session()->has('error'))
            <div class="alert {{ session()->get('alert') }} alert-dismissible fade show">
                <li>{{ session()->get('error') }}</li>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Invoices</h1>

        </div>



        <!-- Content Row -->
        <div class="row">

            <div class="col">

                <!-- INDEX -->

                @if (!$invoices->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">CUSTOMER</th>
                                    <th scope="col">DESCRIPTION</th>
                                    <th scope="col">STATUS</th>
                                    <th scope="col">BALANCE</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td>
                                            <p class="m-0 p-0">{{ $invoice->id }}</p>
                                        </td>
                                        <td>

                                            <p class="m-0 p-0">{{ $invoice->customer_name }}</p>
                                            <p class="m-0 p-0">
                                                {{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $invoice->customer_phone) }}
                                            </p>
                                            <p class="m-0 p-0">{{ $invoice->customer_email }}</p>
                                            <p class="m-0 p-0">{{ $invoice->customer_company }}</p>
                                        </td>
                                        <td>
                                            @foreach ($invoice->items as $item)
                                                <p class="m-0 p-0">{{ $item->name }} - {{ $item->description }}</p>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if (isset($invoice->status))
                                                <p
                                                    class="font-weight-bold text-uppercase m-0 p-0 text-{{ $invoice->status_data->color }}">
                                                    {{ $invoice->status_data->name }}</p>
                                            @endif
                                        </td>
                                        <td>
                                            <p
                                                class="@if ($invoice->balance < 0) text-success @endif @if ($invoice->balance > 0) text-danger @endif">
                                                $ {{ number_format((float) $invoice->balance, 2, '.', ',') }} </p>
                                        </td>
                                        <td>
                                            {{ date_format($invoice->created_at, 'M d, Y') }}
                                        </td>

                                        <td><a class="btn btn-primary btn-block btn-sm"
                                                href="{{ route('view-invoice', $invoice) }}"><i
                                                    class="fas fa-binoculars"></i> View</a></td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $invoices->links() }}
                    </div>
                @else
                    <div class="alert alert-secondary" role="alert">
                        {{ __('repair-business.no-information-to-show') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
