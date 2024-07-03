@extends('layouts.admin')
@section('page')
    {{ __('repair-business.inventory-products') }}
@endsection

@section('search')
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="post"
        action="{{ route('inventory-search-product', [$task, $id]) }}">
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
        action="{{ route('inventory-search-product', [$task, $id]) }}">
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
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.inventory-products') }}</h1>
        </div>
        <div class="row">
            <div class="col">
                @if (!isset($search))
                    <form action="{{ route('inventory-create-product') }}" method="POST">
                        @csrf
                        <div class="form-group row m-0">
                            <label for="category" class="col-sm-4 col-form-label">{{ __('repair-business.input_category') }}</label>
                            <div class="col-sm-8">
                                <select name="category"
                                    class="form-control form-control-sm @error('category') is-invalid @enderror">
                                    @if (!$inventory_categories->isEmpty())
                                        @foreach ($inventory_categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }} </option>
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
                                <input type="text"
                                    class="form-control form-control-sm @error('name') is-invalid @enderror" id="name"
                                    value="{{ old('name') }}" name="name" placeholder="">
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
                                    class="form-control form-control-sm @error('barcode') is-invalid @enderror"
                                    id="barcode"
                                    value="@if ($task == 'create-product') {{ $id }} @else {{ old('barcode') }} @endif"
                                    name="barcode" placeholder="">
                                @error('barcode')
                                    <span class="invalid-feedback mb-1" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row m-0">
                            <label for="purchase_price" class="col-sm-4 col-form-label">{{ __('repair-business.input_purchase-price') }}</label>
                            <div class="col-sm-8">
                                <input type="text"
                                    class="form-control form-control-sm @error('purchase_price') is-invalid @enderror"
                                    id="purchase_price" value="{{ old('purchase_price') }}" name="purchase_price"
                                    placeholder="">
                                @error('purchase_price')
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
                                    id="selling_price" value="{{ old('selling_price') }}" name="selling_price"
                                    placeholder="">
                                @error('selling_price')
                                    <span class="invalid-feedback mb-1" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row m-0">
                            <label for="quantity" class="col-sm-4 col-form-label">{{ __('repair-business.input_quantity') }}</label>
                            <div class="col-sm-8">
                                <input type="text"
                                    class="form-control form-control-sm @error('quantity') is-invalid @enderror"
                                    id="quantity" value="{{ old('quantity') }}" name="quantity" placeholder="">
                                @error('quantity')
                                    <span class="invalid-feedback mb-1" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <hr>
                        <p class="small text-muted font-italic ml-2 p-0">{{ __('repair-business.optional-information') }}</p>
                        <div class="form-group row m-0">
                            <label for="supplier" class="col-sm-4 col-form-label">{{ __('repair-business.input_supplier') }}</label>
                            <div class="col-sm-8">
                                <input type="text"
                                    class="form-control form-control-sm @error('supplier') is-invalid @enderror"
                                    id="supplier" value="{{ old('supplier') }}" name="supplier" placeholder="">
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
                                    class="form-control form-control-sm @error('min_stock') is-invalid @enderror"
                                    id="min_stock" value="0" name="min_stock" placeholder="">
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
                                    class="form-control form-control-sm @error('max_stock') is-invalid @enderror"
                                    id="max_stock" value="0" name="max_stock" placeholder="">
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
                                    <option value="no">{{ __('repair-business.no') }}</option>
                                    <option value="yes">{{ __('repair-business.yes') }}</option>
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
                                <button type="submit" class="btn btn-primary mt-3 mb-2 btn-sm"><i
                                        class="fa fa-save"></i> {{ __('repair-business.button_create') }}</button>
                            </div>
                        </div>
                    </form>
                @endif
                @if (!$inventory_products->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('repair-business.table_barcode') }}</th>
                                    <th scope="col">{{ __('repair-business.table_name') }}</th>
                                    <th scope="col">{{ __('repair-business.table_category') }}</th>
                                    <th scope="col">{{ __('repair-business.table_stock') }}</th>
                                    <th scope="col"></th>
                                    @if ($task == 'invoice')
                                        <th scope="col"></th>
                                    @endif
                                    @if ($task == 'repair')
                                        <th scope="col"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($inventory_products as $product)
                                    <tr>
                                        <td>{{ $product->barcode }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>{{ $product->transactions->where('transaction', 'purchase')->sum('quantity') - $product->transactions->where('transaction', 'sell')->sum('quantity') }}
                                        </td>

                                        <td class="text-right">
                                            <a href="{{ route('inventory-view-product', $product) }}"
                                                class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> {{ __('repair-business.button_view') }}</a>
                                        </td>
                                        @if ($task == 'invoice')
                                            <td class="text-right">

                                                <form method="POST"
                                                    action="{{ route('inventory-sell-transaction', ['invoice', $id, $product]) }}">
                                                    @csrf
                                                    @method('POST')

                                                    <button type="submit" class="btn btn-primary  btn-sm"><i
                                                            class="fas fa-plus"></i> {{ __('repair-business.button_add-to-invoice') }}</a>
                                                </form>
                                            </td>
                                        @endif

                                        @if ($task == 'repair')
                                            <td class="text-right">
                                                <form method="POST"
                                                    action="{{ route('inventory-sell-transaction', ['repair', $id, $product]) }}">
                                                    @csrf
                                                    @method('POST')

                                                    <button type="submit" class="btn btn-primary  btn-sm"><i
                                                            class="fas fa-plus"></i> {{ __('repair-business.button_add-to-repair') }}</a>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $inventory_products->links() }}
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
