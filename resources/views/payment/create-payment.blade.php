@extends('layouts.admin')
@section('page') Create Payment @endsection

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
            <h1 class="h3 mb-0 text-gray-800">Create Payment</h1>
           
          </div>



          <!-- Content Row -->
          <div class="row">

          <div class="col">
              
<!-- FORM -->
<form action="{{route('create-payment')}}" method="POST">
    @csrf

        <div class="form-group row m-0">
            <label for="amount" class="col-sm-2 col-form-label">Amount</label>
            <div class="col-sm-10">
            <input type="text" class="form-control form-control-sm @error('amount') is-invalid @enderror" id="amount" value="{{old('amount')}}" name="amount" placeholder="">
            @error('amount')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>

        

        <div class="form-group row m-0">
            <label for="method" class="col-sm-2 col-form-label">Method</label>
            <div class="col-sm-10">
                <select class="form-control form-control-sm @error('method') is-invalid @enderror" name="method">
                   <option value="cash">CASH</option>
                   <option value="card">CARD</option>
                   <option value="check">CHECK</option>
                   <option value="other">OTHER</option>
                 
                </select>
                @error('method')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>

        <div class="form-group row m-0">
            <label for="ref" class="col-sm-2 col-form-label">Reference</label>
            <div class="col-sm-10">
            <input type="text" class="form-control form-control-sm @error('ref') is-invalid @enderror" id="ref" value="{{old('ref')}}" name="ref" placeholder="">
            @error('ref')
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
          </div>

            </div>

          

        </div>
        <!-- /.container-fluid -->

@endsection
