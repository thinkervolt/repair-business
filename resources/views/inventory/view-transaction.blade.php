@extends('layouts.admin')
@section('page') Transaction @endsection




@section('page-content') 


        <!-- Begin Page Content -->
        <div class="container-fluid">
          @if(session()->has('error'))
              <div class="alert {{ session()->get('alert') }} alert-dismissible fade show">
                  <li>{{ session()->get('error') }}</li>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
          @endif
          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Inventory - Transaction </h1>
          </div>



          <!-- Content Row -->
          <div class="row">

          <div class="col">


                     <!-- FORM -->
                     <form action="{{route('inventory-update-transaction',$transaction)}}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group row m-0">
                            <label  class="col-sm-2 col-form-label">Product</label>
                            <div class="col-sm-10">
                                <a href="{{ route('inventory-view-product',$transaction->product_id)}}" class="btn btn-link btn-block btn-sm text-left"> {{$transaction->product->name}}</a>
                            </div>
                        </div>
                        <div class="form-group row m-0">
                            <label  class="col-sm-2 col-form-label">Invoice</label>
                            <div class="col-sm-10">
                                @if(isset($transaction->invoice_id))
                                <a href="{{ route('view-invoice',$transaction->invoice_id)}}" class="btn btn-link btn-block border btn-sm text-left"> {{$transaction->invoice_id}}</a>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row m-0">
                            <label  class="col-sm-2 col-form-label">Repair</label>
                            <div class="col-sm-10">
                                @if(isset($transaction->repair_id))
                                <a href="{{ route('view-repair',$transaction->repair_id)}}" class="btn btn-link btn-block border btn-sm text-left"> {{$transaction->repair_id}}</a>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row m-0">
                            <label for="transaction" class="col-sm-2 col-form-label">Transaction</label>
                            <div class="col-sm-10">
                            <select class="form-control form-control-sm @error('transaction') is-invalid @enderror" id="transaction" name="transaction">
                                <option value="purchase" @if($transaction->transaction == 'purchase') selected @endif>PURCHASE</option>
                                <option value="sell" @if($transaction->transaction == 'sell') selected @endif >SELL</option>
                            </select>
                        
                            @error('transaction')
                                <span class="invalid-feedback mb-1" role="alert">
                                {{ $message }}
                                </span>
                            @enderror
                            </div>
                        </div>

                        <div class="form-group row m-0">
                            <label for="purchase_price" class="col-sm-2 col-form-label">Purchase Price</label>
                            <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm @error('purchase_price') is-invalid @enderror" id="purchase_price" value="{{$transaction->purchase_price}}" name="purchase_price" placeholder="">
                            @error('purchase_price')
                                <span class="invalid-feedback mb-1" role="alert">
                                {{ $message }}
                                </span>
                            @enderror
                            </div>
                        </div>
                        <div class="form-group row m-0">
                            <label for="selling_price" class="col-sm-2 col-form-label">Selling Price</label>
                            <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm @error('selling_price') is-invalid @enderror" id="selling_price" value="{{$transaction->selling_price}}" name="selling_price" placeholder="">
                            @error('selling_price')
                                <span class="invalid-feedback mb-1" role="alert">
                                {{ $message }}
                                </span>
                            @enderror
                            </div>
                        </div>

                            <div class="form-group row m-0">
                                <label for="quantity" class="col-sm-2 col-form-label">Quantity</label>
                                <div class="col-sm-10">
                                <input type="text" class="form-control form-control-sm @error('quantity') is-invalid @enderror" id="quantity" value="{{$transaction->quantity}}" name="quantity" placeholder="">
                                @error('quantity')
                                    <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                    </span>
                                @enderror
                                </div>
                            </div>

                            <div class="form-group row m-0">
                                <div class="col-sm-2"> </div>
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-block btn-warning mt-3 mb-2 btn-sm"><i class="fa fa-edit"></i> Update</button>
                                </div>
                            </div>
                        </form>

                        <form action="{{route('inventory-delete-transaction',$transaction)}}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="form-group row m-0 p-0">
                                <div class="col-sm-2"> </div>
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-block btn-danger mb-2 btn-sm"><i class="fa fa-trash"></i> Delete</button>
                                </div>
                            </div>
                        </form>

                        <p class="d-block d-sm-inline m-0 small">id: {{$transaction->id}}</p>
                        <p class="d-block d-sm-inline m-0 small">created_at: {{$transaction->created_at}}</p>
                        <p class="d-block d-sm-inline m-0 small">updated_at: {{$transaction->updated_at}}</p>
                    <!-- END FORM -->
          


          </div>

            </div>

          

        </div>
        <!-- /.container-fluid -->

@endsection
