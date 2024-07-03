@extends('layouts.admin')
@section('page')
    {{ __('repair-business.restock') }} #{{ $product->id }}
@endsection

@section('page-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.restock') }} #{{ $product->id }}</h1>
    </div>
    <div class="row justify-content-center shadow p-md-4">
        <div class="col-md-8">
            <form action="{{ route('inventory-create-transaction', $product) }}" method="POST">
                @csrf
                <div class="form-group row m-0">
                    <label class="col-sm-4 col-form-label">{{ __('repair-business.input_product') }}</label>
                    <div class="col-sm-8">
                        <a href="{{ route('inventory-view-product', $product) }}"
                            class="btn btn-link btn-block  btn-sm text-left"> {{ $product->name }}</a>
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label class="col-sm-4 col-form-label">{{ __('repair-business.input_barcode') }} </label>
                    <div class="col-sm-8">
                        <p class="small ml-2">{{ $product->barcode }}</p>
                    </div>
                </div>

                <div class="form-group row m-0">
                    <label for="purchase_price" class="col-sm-4 col-form-label">{{ __('repair-business.input_purchase-price') }}</label>
                    <div class="col-sm-8">
                        <input type="text"
                            class="form-control form-control-sm @error('purchase_price') is-invalid @enderror"
                            id="purchase_price" value="{{ old('purchase_price') }}" name="purchase_price" placeholder="">
                        @error('purchase_price')
                            <span class="invalid-feedback mb-1" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row m-0">
                    <label for="quantity" class="col-sm-4 col-form-label">{{ __('repair-business.input_quantity') }}</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm @error('quantity') is-invalid @enderror"
                            id="quantity" value="{{ old('quantity') }}" name="quantity" placeholder="">
                        @error('quantity')
                            <span class="invalid-feedback mb-1" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row m-0">
                    <div class="col-sm-4"> </div>
                    <div class="col-sm-8">
                        <button type="submit" class="btn btn-primary mt-3 mb-2 btn-sm"><i class="fa fa-save"></i>
                            {{ __('repair-business.button_save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
