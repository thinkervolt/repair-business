@extends('layouts.admin')
@section('page')
    Product
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
            <h1 class="h3 mb-0 text-gray-800">Inventory - Product </h1>
            <h1 class="h3 mb-0 text-primary">Stock
                {{ $product->transactions->where('transaction', 'purchase')->sum('quantity') - $product->transactions->where('transaction', 'sell')->sum('quantity') }}
            </h1>



        </div>



        <!-- Content Row -->
        <div class="row">

            <div class="col">


                <!-- FORM -->
                <form action="{{ route('inventory-update-product', $product) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group row m-0">
                        <label for="category" class="col-sm-2 col-form-label">Category</label>
                        <div class="col-sm-10">

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
                        <label for="name" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
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
                        <label for="barcode" class="col-sm-2 col-form-label">Barcode</label>
                        <div class="col-sm-10">
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
                        <label for="selling_price" class="col-sm-2 col-form-label">Selling Price</label>
                        <div class="col-sm-10">
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
                        <label for="supplier" class="col-sm-2 col-form-label">Supplier</label>
                        <div class="col-sm-10">
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
                        <label for="min_stock" class="col-sm-2 col-form-label">Minimum Stock</label>
                        <div class="col-sm-10">
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
                        <label for="max_stock" class="col-sm-2 col-form-label">Maximun Stock</label>
                        <div class="col-sm-10">
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
                        <label for="email_alert" class="col-sm-2 col-form-label">E-mail Alert</label>
                        <div class="col-sm-10">
                            <select class="form-control form-control-sm @error('email_alert') is-invalid @enderror"
                                name="email_alert">
                                <option value="no" @if ($product->email_alert == 'no') selected @endif>NO</option>
                                <option value="yes" @if ($product->email_alert == 'yes') selected @endif>YES</option>
                            </select>
                            @error('email_alert')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>


                    <div class="form-group row m-0">
                        <div class="col-sm-2"> </div>
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-block btn-warning mt-3 mb-2 btn-sm"><i
                                    class="fa fa-edit"></i> Update</button>
                        </div>
                    </div>
                </form>

                <form action="{{ route('inventory-delete-product', $product) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="form-group row m-0 p-0">
                        <div class="col-sm-2"> </div>
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-block btn-danger mb-2 btn-sm"><i
                                    class="fa fa-trash"></i> Delete</button>
                        </div>
                    </div>
                </form>

                <div class="form-group row m-0 p-0">
                    <div class="col-sm-2"> </div>
                    <div class="col-sm-10">
                        <a href="{{ route('inventory-restock-transaction', $product) }}"
                            class="btn btn-block btn-primary mb-2 btn-sm"><i class="fas fa-truck-loading"></i> Restock</a>
                    </div>
                </div>

                <p class="d-block d-sm-inline m-0 small">id: {{ $product->id }}</p>
                <p class="d-block d-sm-inline m-0 small">created_at: {{ $product->created_at }}</p>
                <p class="d-block d-sm-inline m-0 small">updated_at: {{ $product->updated_at }}</p>
                <!-- END FORM -->

                <!-- INDEX -->

                @if (!$transactions->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">TRANSACTION</th>
                                    <th scope="col">PURCHASE PRICE</th>
                                    <th scope="col">SELLING PRICE</th>
                                    <th scope="col">QUANTITY</th>
                                    <th scope="col">DATE</th>
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
                                                    class="fas fa-binoculars"></i> View</a></td>

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
