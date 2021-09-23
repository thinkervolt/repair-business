@extends('layouts.admin')
@section('page') Categories @endsection




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
            <h1 class="h3 mb-0 text-gray-800">Inventory - Categories</h1>
           
          </div>



          <!-- Content Row -->
          <div class="row">

          <div class="col">


                     <!-- FORM -->
                     <form action="{{route('inventory-create-category')}}" method="POST">
                        @csrf
        
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
                                <div class="col-sm-2"> </div>
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-block btn-primary mt-3 mb-2 btn-sm"><i class="fa fa-save"></i> Create</button>
                                </div>
                            </div>
                        </form>
                    <!-- END FORM -->

                <!-- INDEX -->

@if(!$inventory_categories->isEmpty())
    <div class="table-responsive">

        <table class="table table-sm mt-3 table-hover ">
            <thead>
            <tr>
                <th  scope="col">ID</th>
                <th  scope="col">NAME</th>
                <th  scope="col"></th>
                <th  scope="col"></th>
            </tr>
            </thead>
            <tbody>

            @foreach($inventory_categories as $inventory_category)
                    <tr>
                    <td><p class="m-0 p-0">{{$inventory_category->id}}</p></td>
                    <td><input type="text" class="form-control form-control-sm" value= "{{$inventory_category->name}}"></td>
                    <td ><a class="btn btn-warning btn-block btn-sm" href="{{route('view-invoice',$inventory_category)}}"><i class="fas fa-edit"></i> Update</a></td>
                    <td ><a class="btn btn-danger btn-block btn-sm" href="{{route('view-invoice',$inventory_category)}}"><i class="fas fa-trash"></i> Delete</a></td>
                    </tr>
            
            @endforeach

            </tbody>
        </table>
        {{ $inventory_categories->links() }}
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
