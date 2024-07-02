@extends('layouts.admin')
@section('page')
{{ __('repair-business.invoices') }}
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
    <div class="container-fluid">
    
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            {{ __('repair-business.invoices') }}
        </div>
        <div class="row">
            <div class="col">
                @if (!$invoices->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('repair-business.table_id') }}</th>
                                    <th scope="col">{{ __('repair-business.table_customer') }}</th>
                                    <th scope="col">{{ __('repair-business.table_description') }}</th>
                                    <th scope="col">{{ __('repair-business.table_status') }}</th>
                                    <th scope="col">{{ __('repair-business.table_balance') }}</th>
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
                                                    class="fas fa-binoculars"></i> {{ __('repair-business.button_view') }}</a></td>
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
