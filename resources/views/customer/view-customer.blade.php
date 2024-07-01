@extends('layouts.admin')
@section('page')
{{ __('repair-business.view-customer') }}
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.view-customer') }}</h1>
        </div>
        <div class="row">
            <div class="col">
                <form action="{{ route('update-customer', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group row m-0">
                        <label for="first_name" class="col-sm-2 col-form-label">{{ __('repair-business.input_first-name') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('first_name') is-invalid @enderror"
                                id="first_name" value="{{ $customer->first_name }}" name="first_name" placeholder="">

                            @error('first_name')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="last_name" class="col-sm-2 col-form-label">{{ __('repair-business.input_last-name') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('last_name') is-invalid @enderror" id="last_name"
                                value="{{ $customer->last_name }}" name="last_name" placeholder="">

                            @error('last_name')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="phone" class="col-sm-2 col-form-label">{{ __('repair-business.input_phone') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                id="phone" value="{{ $customer->phone }}" name="phone" placeholder="">
                            @error('phone')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>



                    <div class="form-group row m-0">
                        <label for="email" class="col-sm-2 col-form-label">{{ __('repair-business.input_email') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm @error('email') is-invalid @enderror"
                                id="email" value="{{ $customer->email }}" name="email" placeholder="">
                            @error('email')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="address" class="col-sm-2 col-form-label">{{ __('repair-business.input_address') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('address') is-invalid @enderror" id="address"
                                value="{{ $customer->address }}" name="address" placeholder="">
                            @error('address')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="city" class="col-sm-2 col-form-label">{{ __('repair-business.input_city') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm @error('city') is-invalid @enderror"
                                id="city" value="{{ $customer->city }}" name="city" placeholder="">
                            @error('city')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="state" class="col-sm-2 col-form-label">{{ __('repair-business.input_state') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm @error('state') is-invalid @enderror"
                                id="state" value="{{ $customer->state }}" name="state" placeholder="">
                            @error('state')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="zip" class="col-sm-2 col-form-label">{{ __('repair-business.input_zip') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm @error('zip') is-invalid @enderror"
                                id="zip" value="{{ $customer->zip }}" name="zip" placeholder="">
                            @error('zip')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="company" class="col-sm-2 col-form-label">{{ __('repair-business.input_company') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                class="form-control form-control-sm @error('company') is-invalid @enderror" id="company"
                                value="{{ $customer->company }}" name="company" placeholder="">
                            @error('company')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>



                    <div class="form-group row m-0">
                        <div class="col-sm-2"> </div>
                        <div class="col-sm-10">
                            <button type="submit" class="btn  btn-warning mt-3 mb-2 btn-sm"><i class="fa fa-edit"></i>
                                {{ __('repair-business.button_update') }}</button>

                        </div>
                    </div>

                </form>

                <form action="{{ route('delete-customer', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group row m-0">
                        <div class="col-sm-2"> </div>
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-danger mt-1 mb-2 btn-sm"><i class="fa fa-trash"></i>
                                {{ __('repair-business.button_delete') }}</button>

                        </div>
                    </div>
                </form>



                <div class="form-group row m-0">
                    <div class="col-sm-2"> </div>
                    <div class="col-sm-10">
                        <a href="{{ route('create-repair', $customer) }}" class="btn btn-primary mt-1 mb-2 btn-sm"><i
                                class="fas fa-wrench"></i> {{ __('repair-business.button_create-repair') }}</a>
                    </div>
                </div>

                <p class="d-block d-sm-inline m-0 small">id: {{ $customer->id }}</p>
                <p class="d-block d-sm-inline m-0 small">created_at: {{ $customer->created_at }}</p>
                <p class="d-block d-sm-inline m-0 small">updated_at: {{ $customer->updated_at }}</p>

            </div>

        </div>

        <h4 class="mt-2">{{ __('repair-business.repairs') }}</h4>
        @if (!$repairs->isEmpty())
            <hr>

            <div class="table-responsive">

                <table class="table table-sm mt-3 table-hover ">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('repair-business.table_id') }}</th>
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
                                <td class="text-right">
                                    <a class="btn btn-primary btn-sm"
                                        href="{{ route('view-repair', $repair) }}"><i class="fas fa-binoculars"></i>
                                        View</a>
                                    @if (isset($task))
                                        @if ($task == 'invoice')
                                            <form class="mt-1" method="POST"
                                                action="{{ route('create-item-repair-invoice', [$repair, $id]) }}">
                                                @csrf

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
                {{ $repairs->links() }}
            </div>
        @else
            <div class="alert alert-secondary" role="alert">
                {{ __('repair-business.no-information-to-show') }}
            </div>
        @endif

        <h4 class="mt-2">{{ __('repair-business.invoices') }}</h4>
        @if (!$invoices->isEmpty())
            <hr>


            <div class="table-responsive">

                <table class="table table-sm mt-3 table-hover ">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('repair-business.table_id') }}</th>
                            <th scope="col">{{ __('repair-business.table_status') }}</th>
                            <th scope="col">{{ __('repair-business.table_balance') }}</th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->id }}</td>

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

                                <td class="text-right"><a class="btn btn-primary btn-sm"
                                        href="{{ route('view-invoice', $invoice) }}"><i class="fas fa-binoculars"></i>
                                        {{ __('repair-business.button_view') }}</a></td>
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
@endsection
