@extends('layouts.admin')
@section('page')
{{ __('repair-business.transactions') }}
@endsection

@section('search')
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="post"
        action="{{ route('inventory-search-transaction') }}">
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
    <form class="form-inline mr-auto w-100 navbar-search" method="post"
        action="{{ route('inventory-search-transaction') }}">
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
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.transactions') }}</h1>
        </div>

        <div class="row">
            <div class="col">
                @if (!$transactions->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('repair-business.table_date') }}</th>
                                    <th scope="col">{{ __('repair-business.table_transaction') }}</th>
                                    <th scope="col">{{ __('repair-business.table_invoice') }}</th>
                                    <th scope="col">{{ __('repair-business.table_repair') }}</th>
                                    <th scope="col">{{ __('repair-business.table_product') }}</th>
                                    <th scope="col">{{ __('repair-business.table_quantity') }}</th>
                                    <th scope="col">{{ __('repair-business.table_supplier') }}</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ date_format($transaction->created_at, 'M d, Y') }}</td>
                                        <td class="text-uppercase">{{ $transaction->transaction }}</td>
                                        <td>
                                            @if (isset($transaction->invoice_id))
                                                <a
                                                    href="{{ route('view-invoice', $transaction->invoice_id) }}">{{ $transaction->invoice_id }}</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($transaction->repair_id))
                                                <a
                                                    href="{{ route('view-repair', $transaction->repair_id) }}">{{ $transaction->repair_id }}</a>
                                            @endif
                                        </td>
                                        <td>{{ $transaction->product->name }}</td>
                                        <td>
                                            <p
                                                class="m-0 p-0 @if ($transaction->transaction == 'purchase') text-success @endif @if ($transaction->transaction == 'sell') text-danger @endif">
                                                @if ($transaction->transaction == 'sell')
                                                    -
                                                    @endif @if ($transaction->transaction == 'purchase')
                                                        +
                                                    @endif{{ $transaction->quantity }}
                                            </p>
                                        </td>
                                        <td>{{ $transaction->product->supplier }}</td>
                                        <td>
                                            <a class="btn btn-primary btn-block btn-sm"
                                                href="{{ route('inventory-view-transaction', $transaction) }}"><i
                                                    class="fas fa-binoculars"></i> {{ __('repair-business.button_view') }} </a>

                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $transactions->links() }}
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
