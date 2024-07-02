@extends('layouts.admin')
@section('page')
    {{ __('repair-business.repair') }} #{{ $repair->id }}
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.repair') }} #{{ $repair->id }}</h1>

            <div>
                <a href="{{ route('mail-repair', $repair) }}" class="btn btn-primary btn-sm my-1">
                    <i class="fas fa-envelope"></i> {{ __('repair-business.button_mail-receipt') }}
                </a>
                <a href="{{ route('print-repair', $repair) }}" class="btn btn-primary btn-sm my-1" target="popup"
                    onclick="window.open('{{ route('print-repair', $repair) }}','popup','width=600,height=600'); return false;">
                    <i class="fas fa-receipt"></i> {{ __('repair-business.button_receipt') }}
                </a>

                <form class="d-inline" method="POST" action="{{ route('create-invoice', [$repair, 'view_repair']) }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm my-1">
                        <i class="fas fa-file-invoice-dollar"></i> {{ __('repair-business.button_invoice') }}
                    </button>
                </form>
                <a href="{{ route('index-customer', [($task = 'repair'), $repair]) }}"
                    class="btn btn-secondary btn-sm my-1"><i class="fas fa-exchange-alt"></i>
                    {{ __('repair-business.button_customer') }}</a>
                <a href="{{ route('inventory-index-product', [($task = 'repair'), $repair]) }}"
                    class="btn btn-secondary btn-sm my-1"><i class="fas fa-plus"></i>
                    {{ __('repair-business.button_add-product') }}</a>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="row justify-content-md-center ">
                    <div class="col-lg-4 mb-2">

                        @if (isset($repair->customer))
                            <div class="card shadow  py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                                {{ __('repair-business.customer') }}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $repair->customer_data->first_name ?? '' }}
                                                {{ $repair->customer_data->last_name ?? '' }}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $repair->customer_data->phone ?? '') }}
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $repair->customer_data->email ?? '' }}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $repair->customer_data->address ?? '' }}
                                                {{ $repair->customer_data->city ?? '' }}
                                                {{ $repair->customer_data->state ?? '' }}
                                                {{ $repair->customer_data->zip ?? '' }}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $repair->customer_data->company ?? '' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (isset($repair->user))
                            <div class="card shadow  py-2 mt-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                                {{ __('repair-business.assigned') }}
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $repair->agent_data->name ?? '' }}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $repair->agent_data->email ?? '' }}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $repair->agent_data->role ?? '' }}</div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (isset($repair->priority) or isset($repair->status))
                            <div class="card shadow  py-2 mt-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                                {{ __('repair-business.information') }}</div>

                                            @if (isset($repair->status))
                                                <div
                                                    class="h5 mb-0 font-weight-bold text-uppercase text-{{ $repair->status_data->color ?? '' }}">
                                                    {{ __('repair-business.status') }}:
                                                    {{ $repair->status_data->name ?? '' }}</div>
                                            @endif
                                            @if (isset($repair->priority))
                                                <div
                                                    class="h5 mb-0 font-weight-bold text-uppercase text-{{ $repair->priority_data->color ?? '' }}">
                                                    {{ __('repair-business.priority') }}:
                                                    {{ $repair->priority_data->name ?? '' }}</div>
                                            @endif


                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                    <div class="col-lg-8 mb-2">
                        <div class="card shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                            {{ __('repair-business.repair') }}
                                        </div>
                                        <form action="{{ route('update-repair', $repair) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="form-group row m-0">
                                                <label for="target"
                                                    class="col-sm-4 col-form-label">{{ __('repair-business.input_repair-target') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text"
                                                        class="form-control form-control-sm @error('target') is-invalid @enderror"
                                                        id="target" value="{{ $repair->target }}" name="target"
                                                        placeholder="">
                                                    @error('target')
                                                        <span class="invalid-feedback mb-1" role="alert">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row m-0">
                                                <label for="data_request"
                                                    class="col-sm-4 col-form-label">{{ __('repair-business.input_repair-request') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text"
                                                        class="form-control form-control-sm @error('data_request') is-invalid @enderror"
                                                        id="data_request" value="{{ $repair->request }}"
                                                        name="data_request" placeholder="">
                                                    @error('data_request')
                                                        <span class="invalid-feedback mb-1" role="alert">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row m-0">
                                                <label for="user"
                                                    class="col-sm-4 col-form-label">{{ __('repair-business.input_assigned') }}</label>
                                                <div class="col-sm-8">
                                                    <select
                                                        class="form-control form-control-sm @error('user') is-invalid @enderror"
                                                        name="user">
                                                        <option @if ($repair->user == null) selected @endif></option>
                                                        @if (!$users->isEmpty())
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}"
                                                                    @if ($repair->user == $user->id) selected @endif>
                                                                    {{ $user->name }} </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('user')
                                                        <span class="invalid-feedback mb-1" role="alert">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row m-0">
                                                <label for="status"
                                                    class="col-sm-4 col-form-label">{{ __('repair-business.input_status') }}</label>
                                                <div class="col-sm-8">
                                                    <select
                                                        class="form-control form-control-sm @error('status') is-invalid @enderror"
                                                        name="status">
                                                        <option @if ($repair->status === null) selected @endif></option>
                                                        @if (!$statuses->isEmpty())
                                                            @foreach ($statuses as $status)
                                                                <option value="{{ $status->id }}"
                                                                    @if ($repair->status == $status->id) selected @endif>
                                                                    {{ $status->name }} </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('status')
                                                        <span class="invalid-feedback mb-1" role="alert">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>


                                            <div class="form-group row m-0">
                                                <label for="priority"
                                                    class="col-sm-4 col-form-label">{{ __('repair-business.input_priority') }}</label>
                                                <div class="col-sm-8">
                                                    <select
                                                        class="form-control form-control-sm @error('priority') is-invalid @enderror"
                                                        name="priority">
                                                        <option @if ($repair->priority == null) selected @endif></option>
                                                        @if (!$priorities->isEmpty())
                                                            @foreach ($priorities as $priority)
                                                                <option value="{{ $priority->id }}"
                                                                    @if ($repair->priority == $priority->id) selected @endif>
                                                                    {{ $priority->name }} </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('priority')
                                                        <span class="invalid-feedback mb-1" role="alert">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row m-0">
                                                <label for="estimate"
                                                    class="col-sm-4 col-form-label">{{ __('repair-business.input_estimate') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text"
                                                        class="form-control form-control-sm @error('estimate') is-invalid @enderror"
                                                        id="estimate"
                                                        value="{{ number_format((float) $repair->estimate, 2, '.', ',') }}"
                                                        name="estimate" placeholder="0.00">
                                                    @error('estimate')
                                                        <span class="invalid-feedback mb-1" role="alert">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row m-0">
                                                <div class="col-sm-4"> </div>
                                                <div class="col-sm-8">
                                                    <button type="submit" class="btn btn-warning mt-3 mb-2 btn-sm"><i
                                                            class="fa fa-edit"></i>
                                                        {{ __('repair-business.button_update') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                        <hr>

                                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                            {{ __('repair-business.jobs_and_comments') }}</div>
                                        <form action="{{ route('create-item-repair', $repair) }}" method="POST">
                                            @csrf

                                            <div class="form-group row m-0">
                                                <label for="data"
                                                    class="col-sm-4 col-form-label">{{ __('repair-business.input_description') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text"
                                                        class="form-control form-control-sm @error('data') is-invalid @enderror"
                                                        id="data" value="{{ old('data') }}" name="data"
                                                        placeholder="">
                                                    @error('data')
                                                        <span class="invalid-feedback mb-1" role="alert">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row m-0">
                                                <label for="group"
                                                    class="col-sm-4 col-form-label">{{ __('repair-business.input_group') }}</label>
                                                <div class="col-sm-8">
                                                    <select
                                                        class="form-control form-control-sm @error('group') is-invalid @enderror"
                                                        name="group">
                                                        <option value="job">{{ __('repair-business.input_job') }}
                                                        </option>
                                                        <option value="comment">{{ __('repair-business.input_comment') }}
                                                        </option>

                                                    </select>
                                                    @error('group')
                                                        <span class="invalid-feedback mb-1" role="alert">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row m-0">
                                                <div class="col-sm-4"> </div>
                                                <div class="col-sm-8">
                                                    <button type="submit" class="btn btn-primary mt-3 mb-2 btn-sm"><i
                                                            class="fa fa-save"></i>
                                                        {{ __('repair-business.button_create') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow  py-2">
            <div class="card-body">

                <h4 class="mt-2 text-uppercase">{{ __('repair-business.jobs_and_comments') }}</h4>
                @if (!$jobs->isEmpty())
                    <hr>
                    <div class="row mb-2 ">
                        <div class="col-12">
                            @foreach ($jobs as $job)
                                <div class="row my-2">
                                    <div class="col-md-auto  align-self-center">
                                        <p class="font-weight-bold text-primary text-uppercase">
                                            {{ __('repair-business.job') }}:</p>
                                    </div>
                                    <div class="col-md align-self-center">
                                        <p class="text-primary">{{ $job->data }}</p>
                                    </div>
                                    <div class="col-md-auto align-self-center">
                                        <p class="small">by {{ $job->agent_data->name ?? '' }}</p>
                                    </div>
                                    <div class="col-md-auto align-self-center">
                                        <p class="small ">{{ date_format($job->created_at, 'M d, Y h:iA') }}</p>
                                    </div>

                                    <div class="col-md-auto  align-self-center">
                                        <form method="POST" action="{{ route('delete-item-repair', $job) }}">

                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"> <i
                                                    class="fas fa-trash"></i>
                                                {{ __('repair-business.button_delete') }}</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if (!$comments->isEmpty())
                    <hr>
                    <div class="row mb-2 ">
                        <div class="col-12">
                            @foreach ($comments as $comment)
                                <div class="row">
                                    <div class="col-md-auto">
                                        <p class="font-weight-bold text-secondary text-uppercase">
                                            {{ __('repair-business.comment') }}:</p>
                                    </div>
                                    <div class="col-md  align-self-center">
                                        <p class="text-secondary">{{ $comment->data }}</p>
                                    </div>
                                    <div class="col-md-auto  align-self-center">
                                        <p class="small">by {{ $comment->agent_data->name }}</p>
                                    </div>
                                    <div class="col-md-auto  align-self-center">
                                        <p class="small">{{ date_format($comment->created_at, 'M d, Y h:iA') }}</p>
                                    </div>
                                    <div class="col-md-auto  align-self-center">
                                        <form method="POST" action="{{ route('delete-item-repair', $comment) }}">

                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger  btn-sm"> <i
                                                    class="fas fa-trash"></i>
                                                {{ __('repair-business.button_delete') }}</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <div class="card shadow  mt-2 py-2">
            <div class="card-body">
                <h4 class="mt-2 text-uppercase">{{ __('repair-business.parts') }}</h4>
                @if (!$transactions->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th>{{ __('repair-business.table_item') }}</th>
                                    <th>{{ __('repair-business.table_description') }}</th>
                                    <th class="right">{{ __('repair-business.table_unit-cost') }}</th>
                                    <th class="center">{{ __('repair-business.table_quantity') }}</th>
                                    <th class="right">{{ __('repair-business.table_total') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @foreach ($transactions as $transaction)
                                <tr>


                                    <td class="left strong">
                                        <input type="text" class="form-control form-control-sm" disabled
                                            value="{{ $transaction->product->barcode }}">

                                    </td>
                                    <td class="left">
                                        <input type="text" class="form-control form-control-sm" disabled
                                            value="{{ $transaction->product->name }}">

                                    </td>

                                    <td class="right">
                                        <input type="text" class="form-control form-control-sm" disabled
                                            value="{{ $transaction->selling_price }}">
                                    </td>
                                    <td class="center">

                                        <input type="text" class="form-control form-control-sm" disabled
                                            value="{{ $transaction->quantity }}">
                                    </td>
                                    <td class="right">$
                                        {{ number_format((float) ($transaction->selling_price * $transaction->quantity), 2, '.', ',') }}

                                    </td>
                                    <td class="text-right">
                                        <form method="POST"
                                            action="{{ route('inventory-cancel-transaction', [$task, $repair, $transaction]) }}">
                                            @method('delete')
                                            @csrf
                                            <button class="btn btn-danger  btn-sm mb-1"><i class="fas fa-trash"></i>
                                                {{ __('repair-business.button_delete') }}</button>

                                        </form>

                                    </td>

                                </tr>
                            @endforeach
                        </table>
                    </div>
                @else
                    <div class="alert alert-secondary" role="alert">
                        {{ __('repair-business.no-information-to-show') }}
                    </div>
                @endif

            </div>
        </div>
        <div class="card shadow  mt-2 py-2">
            <div class="card-body">

                <h4 class="mt-2 text-uppercase">{{ __('repair-business.invoices') }}</h4>
                @if (!$invoices->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('repair-business.table_id') }}</th>
                                    <th scope="col">{{ __('repair-business.table_status') }}</th>
                                    <th scope="col">{{ __('repair-business.table_balance') }}</th>
                                    <th scope="col">{{ __('repair-business.table_date') }}</th>
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
                                            @if (isset($invoice->status))
                                                <p
                                                    class="font-weight-bold text-uppercase m-0 p-0 text-{{ $invoice->status_data->color ?? '' }}">
                                                    {{ $invoice->status_data->name ?? '' }}</p>
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
                                                href="{{ route('view-invoice', $invoice) }}"><i
                                                    class="fas fa-binoculars"></i>
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
        </div>
        @if (!$logs->isEmpty())
            <hr>
            <div class="row mb-2">
                <div class="col-12">
                    @foreach ($logs as $log)
                        <div class="row">
                            <div class="col-md">
                                <p class="small text-muted p-0 m-0">{{ $log->data }}</p>
                            </div>
                            <div class="col-md-auto">
                                <p class="small text-muted p-0 m-0">by {{ $log->user_data->name ?? '' }}</p>
                            </div>
                            <div class="col-md-auto">
                                <p class="small text-muted p-0 m-0">{{ date_format($log->created_at, 'M d, Y h:iA') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            {{ $logs->links() }}
        @endif
    </div>
    <hr>
    <div class="container text-center py-4">
        <form method="POST" action="{{ route('delete-repair', $repair) }}">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-danger mt-3 mb-2 btn-sm"><i class="fa fa-trash"></i>
                {{ __('repair-business.button_delete') }}</button>
        </form>
    </div>
@endsection
