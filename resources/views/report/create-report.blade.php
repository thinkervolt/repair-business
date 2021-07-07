@extends('layouts.admin')
@section('page') Create Report @endsection

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
            <h1 class="h3 mb-0 text-gray-800">Create Report</h1>
           
          </div>

          <form method="POST" action="{{route('get-report')}}">
          @csrf
  <div class="row">
    <div class="col-md">

      <label for="from">From Date:</label>
      <input type="date" class="form-control mb-1"  value="{{date('Y-m-d')}}" name="from" id="from">
    </div>
    <div class="col-md">
    <label for="to">To  Date:</label>
      <input type="date" class="form-control mb-1" value="{{date('Y-m-d')}}" name="to" id="to">
    </div>

    


    <div class="col-md-12">
    <div class="row">
      <div class="col-md">
        <div class="form-check m-3">
          <input class="form-check-input" type="checkbox"  id="invoices" name="invoices" checked>
          <label class="form-check-label" for="invoices">
            Invoices
          </label>
        </div>
      </div>
      <div class="col-md">
        <div class="form-check m-3">
          <input class="form-check-input" type="checkbox"  id="repairs" name="repairs" checked>
          <label class="form-check-label" for="repairs">
            Repairs
          </label>
        </div>
      </div>
      <div class="col-md">
        <div class="form-check m-3">
          <input class="form-check-input" type="checkbox"  id="payments" name="payments" checked>
          <label class="form-check-label" for="payments">
            Payments
          </label>
        </div>
      </div>
    </div>
</div>


      </div>
  <button type="submit" class="btn btn-primary btn-sm btn-block"><i class="far fa-file-alt"></i>  Get Report</button>
</form>

 
        </div>
        <!-- /.container-fluid -->

@endsection
