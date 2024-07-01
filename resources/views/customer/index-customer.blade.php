@extends('layouts.admin')
@section('page')
    {{ __('repair-business.customers') }}
@endsection

@section('search')
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="post"
        action="{{ route('search-customer', [$task, $id]) }}">
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
        action="{{ route('search-customer', [$task, $id]) }}">
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
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.customers') }}</h1>
        </div>
        <div class="row">
            <div class="col">
                @if (!$customers->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('repair-business.table_name') }}</th>
                                    <th scope="col">{{ __('repair-business.table_phone') }}</th>
                                    <th scope="col">{{ __('repair-business.table_email') }}</th>
                                    <th scope="col">{{ __('repair-business.table_company') }}</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($customers as $customer)
                                    <tr>
                                        <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
                                        <td>{{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $customer->phone) }}
                                        </td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->company }}</td>
                                        <td class="text-right">
                                            <a class="btn btn-primary  btn-sm"
                                                href="{{ route('view-customer', $customer) }}"><i
                                                    class="fas fa-binoculars"></i> {{ __('repair-business.button_view') }}</a>
                                            @if (isset($task))
                                                @if ($task == 'repair')
                                                    <form class="mt-1" method="POST"
                                                        action="{{ route('update-customer-repair', [$customer, $id]) }}">
                                                        @csrf
                                                        @method('PUT')

                                                        <button type="submit" class="btn btn-primary btn-sm"><i
                                                                class="fas fa-plus"></i>{{ __('repair-business.button_add-to-repair') }}</a>
                                                    </form>
                                                @endif

                                                @if ($task == 'invoice')
                                                    <form class="mt-1" method="POST"
                                                        action="{{ route('update-customer-invoice', [$customer, $id]) }}">
                                                        @csrf
                                                        @method('PUT')

                                                        <button type="submit" class="btn btn-primary btn-sm"><i
                                                                class="fas fa-plus"></i> {{ __('repair-business.button_add-to-invoice') }}</a>
                                                    </form>
                                                @endif
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $customers->links() }}
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
