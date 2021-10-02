@extends('layouts.admin')
@section('page') Restock @endsection




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
            <h1 class="h3 mb-0 text-gray-800">Inventory - Restock </h1>
          </div>



          <!-- Content Row -->
          <div class="row">

          <div class="col">


                     <!-- FORM -->
                     <form action="{{route('inventory-create-transaction',$product)}}" method="POST">
                        @csrf

                        <div class="form-group row m-0">
                            <label  class="col-sm-2 col-form-label">Product</label>
                            <div class="col-sm-10">
                                <a href="{{ route('inventory-view-product',$product)}}" class="btn btn-link btn-block  btn-sm text-left"> {{$product->name}}</a>
                            </div>
                        </div>
                        <div class="form-group row m-0">
                            <label  class="col-sm-2 col-form-label">Barcode</label>
                            <div class="col-sm-10">
                                <p class="small ml-2">{{$product->barcode}}</p>
                            </div>
                        </div>
                        

                        <div class="form-group row m-0">
                            <label for="purchase_price" class="col-sm-2 col-form-label">Purchase Price</label>
                            <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm @error('purchase_price') is-invalid @enderror" id="purchase_price" value="{{old('purchase_price')}}" name="purchase_price" placeholder="">
                            @error('purchase_price')
                                <span class="invalid-feedback mb-1" role="alert">
                                {{ $message }}
                                </span>
                            @enderror
                            </div>
                        </div>

                            <div class="form-group row m-0">
                                <label for="quantity" class="col-sm-2 col-form-label">Quantity</label>
                                <div class="col-sm-10">
                                <input type="text" class="form-control form-control-sm @error('quantity') is-invalid @enderror" id="quantity" value="{{old('quantity')}}" name="quantity" placeholder="">
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
                                    <button type="submit" class="btn btn-block btn-primary mt-3 mb-2 btn-sm"><i class="fa fa-save"></i> Save</button>
                                </div>
                            </div>
                        </form>
                    <!-- END FORM -->
          


          </div>

            </div>

          

        </div>
        <!-- /.container-fluid -->

@endsection
