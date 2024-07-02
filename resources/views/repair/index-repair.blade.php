@extends('layouts.admin')
@section('page')
    {{ __('repair-business.repairs') }}
@endsection

@section('search')
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="post"
        action="{{ route('search-repair', [$task, $id]) }}">
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
        action="{{ route('search-repair', [$task, $id]) }}">
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
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.repairs') }}</h1>
        </div>
        <div class="row">
            <div class="col">
                @if (!$repairs->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('repair-business.table_id') }}</th>
                                    <th scope="col">{{ __('repair-business.table_customer') }}</th>
                                    <th scope="col">{{ __('repair-business.table_target') }}</th>
                                    <th scope="col">{{ __('repair-business.table_request') }}</th>
                                    <th scope="col">{{ __('repair-business.table_status') }}</th>
                                    <th scope="col">{{ __('repair-business.table_priority') }}</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($repairs as $repair)
                                    <tr>
                                        <td>{{ $repair->id }}</td>
                                        <td>
                                            @if (isset($repair->customer))
                                                <p class="m-0 p-0">{{ $repair->customer_data->first_name ?? '' }}
                                                    {{ $repair->customer_data->last_name ?? '' }}</p>
                                                <p class="m-0 p-0">
                                                    {{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $repair->customer_data->phone ?? '') }}
                                                </p>
                                                <p class="m-0 p-0">{{ $repair->customer_data->email ?? '' }}</p>
                                                <p class="m-0 p-0">{{ $repair->customer_data->company ?? '' }}</p>
                                            @endif
                                        </td>
                                        <td>{{ $repair->target }}</td>
                                        <td>{{ $repair->request }}</td>
                                        <td>
                                            @if (isset($repair->status))
                                                <p
                                                    class="font-weight-bold text-uppercase m-0 p-0 text-{{ $repair->status_data->color ?? '' }}">
                                                    {{ $repair->status_data->name ?? '' }} </p>
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($repair->priority))
                                                <p
                                                    class="font-weight-bold text-uppercase m-0 p-0 text-{{ $repair->priority_data->color ?? '' }}">
                                                    {{ $repair->priority_data->name ?? '' }} </p>
                                            @endif
                                        </td>
                                        <td>{{ date_format($repair->created_at, 'M d, Y') }}</td>
                                        <td>
                                            <a class="btn btn-primary btn-block btn-sm"
                                                href="{{ route('view-repair', $repair) }}"><i
                                                    class="fas fa-binoculars"></i>
                                                {{ __('repair-business.button_view') }}</a>
                                            @if (isset($task))
                                                @if ($task == 'invoice')
                                                    <form class="mt-1" method="POST"
                                                        action="{{ route('create-item-repair-invoice', [$repair, $id]) }}">
                                                        @csrf

                                                        <button type="submit" class="btn btn-primary btn-block btn-sm"><i
                                                                class="fas fa-plus"></i> {{ __('repair-business.button_add-to-invoice') }}</a>
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
                        {{ __('repair-business.no-information-to-show') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
