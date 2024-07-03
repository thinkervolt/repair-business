@extends('layouts.admin')
@section('page')
{{ __('repair-business.product') }} #{{ $product->id}}
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.product') }} #{{ $product->id}}</h1>
            <h1 class="h3 mb-0 text-primary">{{ __('repair-business.stock') }}
                {{ $product->transactions->where('transaction', 'purchase')->sum('quantity') - $product->transactions->where('transaction', 'sell')->sum('quantity') }}
            </h1>
        </div>
        <div class="row">
            <div class="col">
                <form action="{{ route('inventory-update-product', $product) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group row m-0">
                        <label for="category" class="col-sm-4 col-form-label">{{ __('repair-business.input_category') }}</label>
                        <div class="col-sm-8">

                            <select name="category"
                                class="form-control form-control-sm @error('category') is-invalid @enderror">
                                @if (!$inventory_categories->isEmpty())
                                    @foreach ($inventory_categories as $category)
                                        <option @if ($product->category_id == $category->id) selected @endif
                                            value="{{ $category->id }}">{{ $category->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('category')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="name" class="col-sm-4 col-form-label">{{ __('repair-business.input_name') }}</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                id="name" value="{{ $product->name }}" name="name" placeholder="">
                            @error('name')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="barcode" class="col-sm-4 col-form-label">{{ __('repair-business.input_barcode') }}</label>
                        <div class="col-sm-8">
                            <input type="text"
                                class="form-control form-control-sm @error('barcode') is-invalid @enderror" id="barcode"
                                value="{{ $product->barcode }}" name="barcode" placeholder="">
                            @error('barcode')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="selling_price" class="col-sm-4 col-form-label">{{ __('repair-business.input_selling-price') }}</label>
                        <div class="col-sm-8">
                            <input type="text"
                                class="form-control form-control-sm @error('selling_price') is-invalid @enderror"
                                id="selling_price" value="{{ $product->selling_price }}" name="selling_price"
                                placeholder="">
                            @error('selling_price')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="supplier" class="col-sm-4 col-form-label">{{ __('repair-business.input_supplier') }}</label>
                        <div class="col-sm-8">
                            <input type="text"
                                class="form-control form-control-sm @error('supplier') is-invalid @enderror" id="supplier"
                                value="{{ $product->supplier }}" name="supplier" placeholder="">
                            @error('supplier')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="min_stock" class="col-sm-4 col-form-label">{{ __('repair-business.input_minimum-stock') }}</label>
                        <div class="col-sm-8">
                            <input type="number"
                                class="form-control form-control-sm @error('min_stock') is-invalid @enderror" id="min_stock"
                                value="{{ $product->min_stock }}" name="min_stock" placeholder="">
                            @error('min_stock')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="max_stock" class="col-sm-4 col-form-label">{{ __('repair-business.input_maximum-stock') }}</label>
                        <div class="col-sm-8">
                            <input type="number"
                                class="form-control form-control-sm @error('max_stock') is-invalid @enderror" id="max_stock"
                                value="{{ $product->max_stock }}" name="max_stock" placeholder="">
                            @error('max_stock')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <label for="email_alert" class="col-sm-4 col-form-label">{{ __('repair-business.input_email-alert') }}</label>
                        <div class="col-sm-8">
                            <select class="form-control form-control-sm @error('email_alert') is-invalid @enderror"
                                name="email_alert">
                                <option value="no" @if ($product->email_alert == 'no') selected @endif>{{ __('repair-business.no') }}</option>
                                <option value="yes" @if ($product->email_alert == 'yes') selected @endif>{{ __('repair-business.yes') }}</option>
                            </select>
                            @error('email_alert')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <div class="col-sm-4"> </div>
                        <div class="col-sm-8">
                            <button type="submit" class="btn  btn-warning mt-3 mb-2 btn-sm"><i
                                    class="fa fa-edit"></i> {{ __('repair-business.button_update') }}</button>
                        </div>
                    </div>
                </form>
                <form action="{{ route('inventory-delete-product', $product) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="form-group row m-0 p-0">
                        <div class="col-sm-4"> </div>
                        <div class="col-sm-8">
                            <button type="submit" class="btn  btn-danger mb-2 btn-sm"><i
                                    class="fa fa-trash"></i> {{ __('repair-business.button_delete') }}</button>
                        </div>
                    </div>
                </form>
                <div class="form-group row m-0 p-0">
                    <div class="col-sm-4"> </div>
                    <div class="col-sm-8">
                        <a href="{{ route('inventory-restock-transaction', $product) }}"
                            class="btn btn-primary mb-2 btn-sm"><i class="fas fa-truck-loading"></i> {{ __('repair-business.button_restock') }}</a>
                    </div>
                </div>
                <p class="d-block d-sm-inline m-0 small">id: {{ $product->id }}</p>
                <p class="d-block d-sm-inline m-0 small">created_at: {{ $product->created_at }}</p>
                <p class="d-block d-sm-inline m-0 small">updated_at: {{ $product->updated_at }}</p>
        
                @if (!$transactions->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('repair-business.table_id') }}</th>
                                    <th scope="col">{{ __('repair-business.table_transaction') }}</th>
                                    <th scope="col">{{ __('repair-business.table_purchase-price') }}</th>
                                    <th scope="col">{{ __('repair-business.table_selling-price') }}</th>
                                    <th scope="col">{{ __('repair-business.table_quantity') }}</th>
                                    <th scope="col">{{ __('repair-business.table_date') }}</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($transactions as $transaction)
                                    <tr>

                                        <td>{{ $transaction->id }}</td>
                                        <td class="text-uppercase">{{ $transaction->transaction }}</td>
                                        <td>{{ $transaction->purchase_price }}</td>
                                        <td>{{ $transaction->selling_price }}</td>
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
                                        <td>{{ date_format($transaction->created_at, 'M d, Y') }}</td>
                                        <td><a class="btn btn-primary btn-block btn-sm"
                                                href="{{ route('inventory-view-transaction', $transaction) }}"><i
                                                    class="fas fa-binoculars"></i> {{ __('repair-business.button_view') }} </a></td>

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
