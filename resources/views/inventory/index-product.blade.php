@extends('layouts.admin')
@section('page') Products @endsection

@section('search')
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="post" action="{{route('inventory-search-product',[$task,$id])}}" >
    @csrf

    
        <div class="input-group">
              <input type="text" class="form-control bg-light border-0 small" id="search"  name="search" value="@if(isset($search)){{$search}}@endif" aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-secondary" type="button">
                  <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>


@endsection

@section('mobile-search-buttom')
    <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-search fa-fw"></i>
    </a>
@endsection

@section('mobile-search')
  

    <form class="form-inline mr-auto w-100 navbar-search" method="post" action="{{route('inventory-search-product',[$task,$id])}}" >
    @csrf
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" id="search"  name="search" value="@if(isset($search)){{$search}}@endif" aria-label="Search" aria-describedby="basic-addon2">
                <div class="input-group-append">
                    <button class="btn btn-secondary" type="button">
                            <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
        </div>
    </form>
@endsection


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
            <h1 class="h3 mb-0 text-gray-800">Inventory - Products</h1>
           
          </div>



          <!-- Content Row -->
          <div class="row">

          <div class="col">

            @if(!isset($search))


                     <!-- FORM -->
                     <form action="{{route('inventory-create-product')}}" method="POST">
                        @csrf

                        <div class="form-group row m-0">
                            <label for="category" class="col-sm-2 col-form-label">Category</label>
                            <div class="col-sm-10">

                            <select  name="category" class="form-control form-control-sm @error('category') is-invalid @enderror" >
                                @if(!$inventory_categories->isEmpty())
                                    @foreach($inventory_categories as $category )
                                        <option value="{{$category->id}}">{{$category->name}} </option>
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
                                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" value="{{old('name')}}" name="name" placeholder="">
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
                                <input type="text" class="form-control form-control-sm @error('barcode') is-invalid @enderror" id="barcode" value="@if($task == 'create-product') {{$id}} @else {{old('barcode')}} @endif" name="barcode" placeholder="">
                                @error('barcode')
                                    <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                    </span>
                                @enderror
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
                                <label for="selling_price" class="col-sm-2 col-form-label">Selling Price</label>
                                <div class="col-sm-10">
                                <input type="text" class="form-control form-control-sm @error('selling_price') is-invalid @enderror" id="selling_price" value="{{old('selling_price')}}" name="selling_price" placeholder="">
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
                                <input type="text" class="form-control form-control-sm @error('quantity') is-invalid @enderror" id="quantity" value="{{old('quantity')}}" name="quantity" placeholder="">
                                @error('quantity')
                                    <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                    </span>
                                @enderror
                                </div>
                            </div>

                            <hr>
                            <p class="small text-muted font-italic ml-2 p-0">Following Information is Optional</p>
                            <div class="form-group row m-0">
                                <label for="supplier" class="col-sm-2 col-form-label">Supplier</label>
                                <div class="col-sm-10">
                                <input type="text" class="form-control form-control-sm @error('supplier') is-invalid @enderror" id="supplier" value="{{old('supplier')}}" name="supplier" placeholder="">
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
                                <input type="number" class="form-control form-control-sm @error('min_stock') is-invalid @enderror" id="min_stock" value="0" name="min_stock" placeholder="">
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
                                <input type="number" class="form-control form-control-sm @error('max_stock') is-invalid @enderror" id="max_stock" value="0" name="max_stock" placeholder="">
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
                                <select class="form-control form-control-sm @error('email_alert') is-invalid @enderror" name="email_alert">
                                    <option value="no">NO</option>
                                    <option value="yes">YES</option>
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
                                    <button type="submit" class="btn btn-block btn-primary mt-3 mb-2 btn-sm"><i class="fa fa-save"></i> Create</button>
                                </div>
                            </div>
                        </form>
                    <!-- END FORM -->

                    @endif

                <!-- INDEX -->

@if(!$inventory_products->isEmpty())
    <div class="table-responsive">

        <table class="table table-sm mt-3 table-hover ">
            <thead>
            <tr>
                <th  scope="col">BARCODE</th>
                <th  scope="col">NAME</th>
                <th  scope="col">CATEGORY</th>
                <th  scope="col">STOCK</th>
                <th  scope="col"></th>
                @if($task == 'invoice')
                <th  scope="col"></th>
                @endif
                @if($task == 'repair')
                <th  scope="col"></th>
                @endif
            </tr>
            </thead>
            <tbody>

            @foreach($inventory_products as $product)
                    <tr>
                    
                    <td>{{$product->barcode}}</td>
                    <td>{{$product->name}}</td>
                    <td>{{$product->category->name}}</td>
                    <td>{{ ($product->transactions->where('transaction', 'purchase')->sum('quantity')) - ($product->transactions->where('transaction', 'sell')->sum('quantity')) }}</td>

                    <td class="text-right">
                        <a href="{{route('inventory-view-product',$product)}}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> View</a>
                    </td>
                    @if($task == 'invoice')
                    <td class="text-right">
                       
                        <form  method="POST" action="{{route('inventory-sell-transaction',['invoice',$id,$product])}}">
                        @csrf
                        @method('POST')

                        <button type="submit" class="btn btn-primary  btn-sm" ><i class="fas fa-plus"></i> Add to Invoice</a>
                        </form>

                   
                    </td>
                    @endif

                    @if($task == 'repair')
                    <td class="text-right">
                       
                        <form  method="POST" action="{{route('inventory-sell-transaction',['repair',$id,$product])}}">
                        @csrf
                        @method('POST')

                        <button type="submit" class="btn btn-primary  btn-sm" ><i class="fas fa-plus"></i> Add to Repair</a>
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
Nothing has been found!
</div>
@endif

<!-- END INDEX -->
              

          </div>

            </div>

          

        </div>
        <!-- /.container-fluid -->

@endsection
