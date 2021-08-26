@extends('layouts.admin')
@section('page') Create Register Report @endsection

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
            <h1 class="h3 mb-0 text-gray-800">Create Register Report</h1>
           
          </div>

          <form method="POST" action="{{route('get-register-report')}}">
          @csrf
  <div class="row">
    <div class="col-md">
      <label >Date:</label>
      <input type="date" class="form-control mb-1"  value="{{date('Y-m-d')}}" name="date" id="from">
    </div>

    <div class="col-md">
      <label >Cash Register Total:</label>
      <input type="text" class="form-control mb-1"  name="cash" placeholder="$ 0.00">
    </div>

    <div class="col-md">
      <label >Credit/Debit Total:</label>
      <input type="text" class="form-control mb-1"  name="card" placeholder="$ 0.00">
    </div>

    
    <div class="col-md">
      <label ></label>
      <button type="submit" class="btn btn-primary  btn-block mb-1 mt-2"><i class="far fa-file-alt"></i>  Get Report</button>
    </div>
  </div>
  
 
</form>

 
        </div>
        <!-- /.container-fluid -->

@endsection
