@extends('layouts.admin')
@section('page')
    Trash
@endsection

@section('search')
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="post"
        action="{{ route('search-trash') }}">
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
    <form class="form-inline mr-auto w-100 navbar-search" method="post" action="{{ route('search-trash') }}">
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
            <h1 class="h3 mb-0 text-gray-800">Trash</h1>

        </div>

        <!-- Content Row -->
        <div class="row">

            <div class="col">

                <!-- INDEX -->

                @if (!$customers->isEmpty() or !$repairs->isEmpty() or !$invoices->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">ITEM</th>
                                    <th scope="col">DATA</th>
                                    <th scope="col">DELETED</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!$customers->isEmpty())
                                    @foreach ($customers as $customer)
                                        <tr>
                                            <td>{{ $customer->id }}</td>
                                            <td>CUSTOMER</td>
                                            <td>
                                                [{{ $customer->first_name }} {{ $customer->last_name }}]
                                                [{{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $customer->phone) }}]
                                                [{{ $customer->email }}]
                                            </td>
                                            <td>{{ $customer->updated_at }}</td>
                                            <td>
                                                <form action="{{ route('restore-customer', $customer) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                        class="btn btn-block btn-primary mt-1 mb-2 btn-sm"><i
                                                            class="fas fa-trash-restore"></i> Restore</button>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="{{ route('destroy-customer', $customer) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-block btn-danger mt-1 mb-2 btn-sm"><i
                                                            class="fas fa-trash"></i> Destroy</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                                @if (!$repairs->isEmpty())
                                    @foreach ($repairs as $repair)
                                        <tr>
                                            <td>{{ $repair->id }}</td>
                                            <td>REPAIR</td>
                                            <td>
                                                [{{ $repair->target }}]
                                                [{{ $repair->request }}]

                                                [ @if (isset($repair->customer_data->first_name))
                                                    {{ $repair->customer_data->first_name }}
                                                @endif
                                                @if (isset($repair->customer_data->last_name))
                                                    {{ $repair->customer_data->last_name }}
                                                @endif
                                                @if (isset($repair->customer_data->email))
                                                    {{ $repair->customer_data->email }}
                                                @endif
                                                @if (isset($repair->customer_data->phone))
                                                    {{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $repair->customer_data->phone) }}
                                                @endif ]
                                            </td>
                                            <td>{{ $repair->updated_at }}</td>
                                            <td>
                                                <form action="{{ route('restore-repair', $repair) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                        class="btn btn-block btn-primary mt-1 mb-2 btn-sm"><i
                                                            class="fas fa-trash-restore"></i> Restore</button>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="{{ route('destroy-repair', $repair) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-block btn-danger mt-1 mb-2 btn-sm"><i
                                                            class="fas fa-trash"></i> Destroy</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                                @if (!$invoices->isEmpty())
                                    @foreach ($invoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->id }}</td>
                                            <td>INVOICE</td>
                                            <td>
                                                [TOTAL: {{ $invoice->total }}]
                                                [BALANCE: {{ $invoice->balance }}]
                                                [{{ $invoice->customer_name }} {{ $invoice->customer_email }}
                                                {{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $invoice->customer_phone) }}]
                                            </td>
                                            <td>{{ $invoice->updated_at }}</td>
                                            <td>
                                                <form action="{{ route('restore-invoice', $invoice) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                        class="btn btn-block btn-primary mt-1 mb-2 btn-sm"><i
                                                            class="fas fa-trash-restore"></i> Restore</button>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="{{ route('destroy-invoice', $invoice) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-block btn-danger mt-1 mb-2 btn-sm"><i
                                                            class="fas fa-trash"></i> Destroy</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
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
