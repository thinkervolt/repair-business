@extends('layouts.admin')
@section('page') View Payment @endsection

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
            <h1 class="h3 mb-0 text-gray-800">View Payment</h1>
           
          </div>



          <!-- Content Row -->
          <div class="row">

          <div class="col">
              
<!-- FORM -->
<form action="{{route('update-payment',$payment)}}" method="POST">
    @csrf
    @method('PUT')

        <div class="form-group row m-0">
            <label for="amount" class="col-sm-2 col-form-label">Amount</label>
            <div class="col-sm-10">
            <input type="text" class="form-control form-control-sm @error('amount') is-invalid @enderror" id="amount" value="{{number_format((float)$payment->amount, 2, '.', ',')}}" name="amount" placeholder="">
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
                   <option @if($payment->method == 'cash') selected @endif  value="cash">CASH</option>
                   <option @if($payment->method == 'card') selected @endif value="card">CARD</option>
                   <option @if($payment->method == 'check') selected @endif value="check">CHECK</option>
                   <option @if($payment->method == 'other') selected @endif value="other">OTHER</option>
                 
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
            <input type="text" class="form-control form-control-sm @error('ref') is-invalid @enderror" id="ref" value="{{$payment->ref}}" name="ref" placeholder="">
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
                <button type="submit" class="btn btn-block btn-warning mt-3 mb-2 btn-sm"><i class="fa fa-edit"></i> Update</button>
            </div>
        </div>
    </form>
<!-- END FORM -->

@if($payment->invoice === null)
<form  action="{{route('delete-payment',$payment)}}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group row m-0">
        <div class="col-sm-2"> </div>
        <div class="col-sm-10">
            <button type="submit" class="btn btn-block btn-danger mt-1 mb-2 btn-sm"><i class="fa fa-trash"></i> Delete</button>
            
        </div>
    </div>
</form>
@else

<div class="form-group row m-0">
    <div class="col-sm-2"> </div>
    <div class="col-sm-10">
        <a  href="{{route('view-invoice',$payment->invoice)}}" class="btn btn-block btn-primary mt-1 mb-2 btn-sm"><i class="fas fa-binoculars"></i> View Invoice</a>
    </div>
</div>



@endif

<p class="d-block d-sm-inline m-0 small">id: {{$payment->id}}</p>
<p class="d-block d-sm-inline m-0 small">created_at: {{$payment->created_at}}</p>
<p class="d-block d-sm-inline m-0 small">updated_at: {{$payment->updated_at}}</p>
          </div>

            </div>

          

        </div>
        <!-- /.container-fluid -->

@endsection
