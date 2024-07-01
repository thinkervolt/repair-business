@extends('layouts.admin')
@section('page')
    View Repair
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

        <div id="repair-alert" class="alert  alert-dismissible fade d-none">
            <li id="repair-alert-message"></li>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">View Repair #{{ $repair->id }}</h1>

            <div>
                <a href="{{ route('mail-repair', $repair) }}" class="btn btn-primary btn-sm my-1">
                    <i class="fas fa-envelope"></i> Mail Receipt
                </a>
                <a href="{{ route('print-repair', $repair) }}" class="btn btn-primary btn-sm my-1" target="popup"
                    onclick="window.open('{{ route('print-repair', $repair) }}','popup','width=600,height=600'); return false;">
                    <i class="fas fa-receipt"></i> Receipt
                </a>

                <form class="d-inline" method="POST" action="{{ route('create-invoice', [$repair, 'view_repair']) }}">

                    @csrf

                    <button type="submit" class="btn btn-secondary btn-sm my-1">
                        <i class="fas fa-file-invoice-dollar"></i> Invoice
                    </button>

                </form>
                <a href="{{ route('index-customer', [($task = 'repair'), $repair]) }}"
                    class="btn btn-secondary btn-sm my-1"><i class="fas fa-exchange-alt"></i> Customer</a>
                <a href="{{ route('inventory-index-product', [($task = 'repair'), $repair]) }}"
                    class="btn btn-secondary btn-sm my-1"><i class="fas fa-plus"></i> Product</a>
            </div>
        </div>



        <!-- Content Row -->
        <div class="row">

            <div class="col">

                <!-- CUSTOMER -->
                <div class="row justify-content-md-center ">
                    <div class="col-lg-4 mb-2">

                        @if (isset($repair->customer))
                            <div class="card shadow  py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                                CUSTOMER</div>
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
                                        <div class="col-auto d-none d-md-block">
                                            <i class="fas fa-user fa-4x text-gray-300"></i>
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
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">AGENT
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $repair->agent_data->name ?? '' }}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $repair->agent_data->email ?? '' }}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $repair->agent_data->role ?? '' }}</div>

                                        </div>
                                        <div class="col-auto d-none d-md-block">
                                            <i class="fas fa-id-card-alt fa-4x text-gray-300"></i>
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
                                                INFORMATION</div>

                                            @if (isset($repair->status))
                                                <div
                                                    class="h5 mb-0 font-weight-bold text-{{ $repair->status_data->color ?? '' }}">
                                                    STATUS: {{ $repair->status_data->name ?? '' }}</div>
                                            @endif
                                            @if (isset($repair->priority))
                                                <div
                                                    class="h5 mb-0 font-weight-bold text-{{ $repair->priority_data->color ?? '' }}">
                                                    PRIORITY: {{ $repair->priority_data->name ?? '' }}</div>
                                            @endif


                                        </div>
                                        <div class="col-auto d-none d-md-block">
                                            <i class="fas fa-info-circle fa-4x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    <!-- END CUSTOMER -->


                    <div class="col-lg-8 mb-2">
                        <div class="card shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">REPAIR
                                        </div>

                                        <!-- FORM -->
                                        <form action="{{ route('update-repair', $repair) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="form-group row m-0">
                                                <label for="target" class="col-sm-2 col-form-label">Target</label>
                                                <div class="col-sm-10">
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
                                                <label for="data_request" class="col-sm-2 col-form-label">Request</label>
                                                <div class="col-sm-10">
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
                                                <label for="user" class="col-sm-2 col-form-label">Assigned</label>
                                                <div class="col-sm-10">
                                                    <select
                                                        class="form-control form-control-sm @error('user') is-invalid @enderror"
                                                        name="user">
                                                        <option @if ($repair->user === null) selected @endif></option>
                                                        @if (!$users->isEmpty())
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}"
                                                                    @if ($repair->user === $user->id) selected @endif>
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
                                                <label for="status" class="col-sm-2 col-form-label">Status</label>
                                                <div class="col-sm-10">
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
                                                <label for="priority" class="col-sm-2 col-form-label">Priority</label>
                                                <div class="col-sm-10">
                                                    <select
                                                        class="form-control form-control-sm @error('priority') is-invalid @enderror"
                                                        name="priority">
                                                        <option @if ($repair->priority === null) selected @endif></option>
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
                                                <label for="estimate" class="col-sm-2 col-form-label">Estimate</label>
                                                <div class="col-sm-10">
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
                                                <div class="col-sm-2"> </div>
                                                <div class="col-sm-10">
                                                    <button type="submit"
                                                        class="btn btn-block btn-warning mt-3 mb-2 btn-sm"><i
                                                            class="fa fa-edit"></i> Update</button>
                                                </div>
                                            </div>



                                        </form>

                                        <form method="POST" action="{{ route('delete-repair', $repair) }}">

                                            @csrf
                                            @method('PUT')

                                            <div class="form-group row m-0">
                                                <div class="col-sm-2"> </div>
                                                <div class="col-sm-10">
                                                    <button type="submit"
                                                        class="btn btn-block btn-danger mt-3 mb-2 btn-sm"><i
                                                            class="fa fa-trash"></i> Delete</button>
                                                </div>
                                            </div>
                                        </form>
                                        <!-- END FORM -->
                                        <hr>


                                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">JOBS &
                                            COMMENTS</div>

                                        <!-- FORM -->
                                        <form action="{{ route('create-item-repair', $repair) }}" method="POST">
                                            @csrf

                                            <div class="form-group row m-0">
                                                <label for="data" class="col-sm-2 col-form-label">Description</label>
                                                <div class="col-sm-10">
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
                                                <label for="group" class="col-sm-2 col-form-label">Group</label>
                                                <div class="col-sm-10">
                                                    <select
                                                        class="form-control form-control-sm @error('group') is-invalid @enderror"
                                                        name="group">
                                                        <option value="job">Job</option>
                                                        <option value="comment">Comment</option>

                                                    </select>
                                                    @error('group')
                                                        <span class="invalid-feedback mb-1" role="alert">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>




                                            <div class="form-group row m-0">
                                                <div class="col-sm-2"> </div>
                                                <div class="col-sm-10">
                                                    <button type="submit"
                                                        class="btn btn-block btn-primary mt-3 mb-2 btn-sm"><i
                                                            class="fa fa-save"></i> Create</button>
                                                </div>
                                            </div>



                                        </form>
                                        <!-- END FORM -->




                                    </div>
                                    <div class="col-auto d-none d-md-block">
                                        <i class="fas fa-wrench fa-4x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END FORM -->


            </div>

        </div>





        @if (!$jobs->isEmpty())
            <hr>

            <div class="row mb-2 ">
                <div class="col-12">
                    @foreach ($jobs as $job)
                        <div class="row my-2">
                            <div class="col-md-auto  align-self-center">
                                <i class="fas fa-tools fa-2x text-secondary"></i>
                            </div>
                            <div class="col-md align-self-center">
                                <p class="">{{ $job->data }}</p>
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
                                    <button type="submit" class="btn btn-danger btn-block btn-sm"> <i
                                            class="fas fa-trash"></i> Delete</button>
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
                                <i class="fas fa-comment fa-2x"></i>
                            </div>
                            <div class="col-md  align-self-center">
                                <p class="p-0 m-0">{{ $comment->data }}</p>
                            </div>
                            <div class="col-md-auto  align-self-center">
                                <p class="small p-0 m-0">by {{ $comment->agent_data->name }}</p>
                            </div>
                            <div class="col-md-auto  align-self-center">
                                <p class="small p-0 m-0">{{ date_format($comment->created_at, 'M d, Y h:iA') }}</p>
                            </div>
                            <div class="col-md-auto  align-self-center">
                                <form method="POST" action="{{ route('delete-item-repair', $comment) }}">

                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-block btn-sm"> <i
                                            class="fas fa-trash"></i> Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <hr>
        <h4 class="mt-2">PARTS</h4>
        @if (!$transactions->isEmpty())
            <div class="table-responsive">

                <table class="table table-sm mt-3 table-hover ">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Item</th>
                            <th>Description</th>
                            <th class="right">Unit Cost</th>
                            <th class="center">Qty</th>
                            <th class="right">Total</th>
                        </tr>
                    </thead>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>
                                <form method="POST"
                                    action="{{ route('inventory-cancel-transaction', [$task, $repair, $transaction]) }}">
                                    @method('delete')
                                    @csrf
                                    <button class="btn btn-danger btn-block btn-sm mb-1"><i class="fas fa-trash"></i>
                                        Delete</button>

                                </form>

                            </td>

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

                        </tr>
                    @endforeach
                </table>
            </div>
        @else
            <div class="alert alert-secondary" role="alert">
                {{ __('repair-business.no-information-to-show') }}
            </div>
        @endif
        <hr>

        <h4 class="mt-2">INVOICES</h4>
        @if (!$invoices->isEmpty())
            <div class="table-responsive">

                <table class="table table-sm mt-3 table-hover ">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
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

                                <td><a class="btn btn-primary btn-block btn-sm"
                                        href="{{ route('view-invoice', $invoice) }}"><i class="fas fa-binoculars"></i>
                                        View</a></td>
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
                                <p class="small text-muted p-0 m-0">{{ date_format($log->created_at, 'M d, Y h:iA') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{ $logs->links() }}
        @endif








    </div>
    <!-- /.container-fluid -->

@endsection
