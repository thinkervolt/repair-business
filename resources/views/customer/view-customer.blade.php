@extends('layouts.admin')
@section('page') View Customer @endsection

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
            <h1 class="h3 mb-0 text-gray-800">View Customer</h1>
           
          </div>



          <!-- Content Row -->
          <div class="row">

          <div class="col">
              
<!-- FORM -->
<form action="{{route('update-customer',$customer)}}" method="POST">
    @csrf
    @method('PUT')

        <div class="form-group row m-0">
            <label for="first_name" class="col-sm-2 col-form-label">First Name</label>
            <div class="col-sm-10">
            <input type="text" class="form-control form-control-sm @error('first_name') is-invalid @enderror" id="first_name" value="{{$customer->first_name}}" name="first_name" placeholder="">

            @error('first_name')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>

        <div class="form-group row m-0">
            <label for="last_name" class="col-sm-2 col-form-label">Last Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control form-control-sm @error('last_name') is-invalid @enderror" id="last_name"   value="{{$customer->last_name}}"  name="last_name" placeholder="">

                @error('last_name')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>

        <div class="form-group row m-0">
            <label for="phone" class="col-sm-2 col-form-label">Phone</label>
            <div class="col-sm-10">
                <input type="text" class="form-control form-control-sm @error('phone') is-invalid @enderror" id="phone" value="{{$customer->phone}}" name="phone" placeholder="">
                @error('phone')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>



        <div class="form-group row m-0">
            <label for="email" class="col-sm-2 col-form-label">E-mail</label>
            <div class="col-sm-10">
                <input type="text" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" value="{{$customer->email}}" name="email" placeholder="">
                @error('email')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>

        <div class="form-group row m-0">
            <label for="address" class="col-sm-2 col-form-label">Address</label>
            <div class="col-sm-10">
                <input type="text" class="form-control form-control-sm @error('address') is-invalid @enderror" id="address" value="{{$customer->address}}" name="address" placeholder="">
                @error('address')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>

        <div class="form-group row m-0">
            <label for="city" class="col-sm-2 col-form-label">City</label>
            <div class="col-sm-10">
                <input type="text" class="form-control form-control-sm @error('city') is-invalid @enderror" id="city" value="{{$customer->city}}" name="city" placeholder="">
                @error('city')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>

        <div class="form-group row m-0">
            <label for="state" class="col-sm-2 col-form-label">State</label>
            <div class="col-sm-10">
                <input type="text" class="form-control form-control-sm @error('state') is-invalid @enderror" id="state" value="{{$customer->state}}" name="state" placeholder="">
                @error('state')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>

        <div class="form-group row m-0">
            <label for="zip" class="col-sm-2 col-form-label">Zip</label>
            <div class="col-sm-10">
                <input type="text" class="form-control form-control-sm @error('zip') is-invalid @enderror" id="zip" value="{{$customer->zip}}" name="zip" placeholder="">
                @error('zip')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>

        <div class="form-group row m-0">
            <label for="company" class="col-sm-2 col-form-label">Company</label>
            <div class="col-sm-10">
                <input type="text" class="form-control form-control-sm @error('company') is-invalid @enderror" id="company" value="{{$customer->company}}" name="company" placeholder="">
                @error('company')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>



        <div class="form-group row m-0">
            <div class="col-sm-2"> </div>
            <div class="col-sm-10">
                <button type="submit" class="btn  btn-warning mt-3 mb-2 btn-sm"><i class="fa fa-edit"></i> Update</button>
                
            </div>
        </div>

    </form >
       
    <form  action="{{route('delete-customer',$customer)}}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group row m-0">
            <div class="col-sm-2"> </div>
            <div class="col-sm-10">
                <button type="submit" class="btn btn-danger mt-1 mb-2 btn-sm"><i class="fa fa-trash"></i> Delete</button>
                
            </div>
        </div>
    </form>

    
 
    <div class="form-group row m-0">
        <div class="col-sm-2"> </div>
            <div class="col-sm-10">
                <a  href="{{route('create-repair',$customer)}}" class="btn btn-primary mt-1 mb-2 btn-sm"><i class="fas fa-wrench"></i> Create Repair</a>
            </div>
    </div>
   
    <p class="d-block d-sm-inline m-0 small">id: {{$customer->id}}</p>
    <p class="d-block d-sm-inline m-0 small">created_at: {{$customer->created_at}}</p>
    <p class="d-block d-sm-inline m-0 small">updated_at: {{$customer->updated_at}}</p>



<!-- END FORM -->
          </div>

            </div>

          

              <!-- INDEX -->
              <h4 class="mt-2">REPAIRS</h4>
@if(!$repairs->isEmpty())
<hr>

    

    <div class="table-responsive">

        <table class="table table-sm mt-3 table-hover ">
            <thead>
            <tr>
                <th  scope="col">ID</th>
                <th  scope="col">TARGET</th>
                <th  scope="col">REQUEST</th>
                <th  scope="col">STATUS</th>
                <th  scope="col">PRIORITY</th>
                <th  scope="col"></th>
                <th  scope="col"></th>
            </tr>
            </thead>
            <tbody>

            @foreach($repairs as $repair)
                <tr>
                    <td>{{$repair->id}}</td>
                    <td>{{$repair->target}}</td>
                    <td>{{$repair->request}}</td>
                    <td>@if(isset($repair->status)) <p  class="font-weight-bold text-uppercase m-0 p-0 text-{{$repair->status_data->color ?? ''}}"  >{{$repair->status_data->name ?? ''}} </p> @endif</td>
                    <td>@if(isset($repair->priority)) <p class="font-weight-bold text-uppercase m-0 p-0 text-{{$repair->priority_data->color ?? ''}}">{{$repair->priority_data->name ?? ''}} </p> @endif</td>
                    <td>{{ date_format($repair->created_at,"M d, Y")}}</td>
                    <td >
                        <a class="btn btn-primary btn-block btn-sm" href="{{route('view-repair',$repair)}}"><i class="fas fa-binoculars"></i> View</a>
                        @if(isset($task))
                            @if($task == 'invoice')
                                <form class="mt-1" method="POST" action="{{route('create-item-repair-invoice',[$repair,$id])}}">
                                @csrf

                                <button type="submit" class="btn btn-primary btn-block btn-sm" ><i class="fas fa-plus"></i> Add to Invoice</a>
                                </form>

                            @endif

                        @endif
                    </td>
                </tr>
            
            @endforeach

            </tbody>
        </table>
        {{ $repairs->links() }}
    </div>
@else

<div class="alert alert-secondary" role="alert">
Nothing has been found!
</div>
@endif

<!-- END INDEX -->



                <!-- INDEX -->
                <h4 class="mt-2">INVOICES</h4>
                @if(!$invoices->isEmpty())

                <hr>

 
    <div class="table-responsive">

        <table class="table table-sm mt-3 table-hover ">
            <thead>
            <tr>
                <th  scope="col">ID</th>
                <th  scope="col">STATUS</th>
                <th  scope="col">BALANCE</th>
                <th  scope="col"></th>
                <th  scope="col"></th>
            </tr>
            </thead>
            <tbody>

            @foreach($invoices as $invoice)
                    <tr>
                    <td>{{$invoice->id}}</td>
              
                    <td>
                        @if(isset($invoice->status)) <p class="font-weight-bold text-uppercase m-0 p-0 text-{{$invoice->status_data->color}}" >{{$invoice->status_data->name}}</p> @endif
                    </td>
                    <td>
                    <p class="@if($invoice->balance < 0) text-success @endif @if($invoice->balance > 0) text-danger @endif"> $ {{number_format((float)$invoice->balance, 2, '.', ',')}} </p>
                    </td>
                    <td>
                    {{ date_format($invoice->created_at,"M d, Y")}}
                    </td>
                 
                    <td ><a class="btn btn-primary btn-block btn-sm" href="{{route('view-invoice',$invoice)}}"><i class="fas fa-binoculars"></i> View</a></td>
                    </tr>
            
            @endforeach

            </tbody>
        </table>
        {{ $invoices->links() }}
    </div>
@else

<div class="alert alert-secondary" role="alert">
Nothing has been found!
</div>
@endif

<!-- END INDEX -->

        </div>
        <!-- /.container-fluid -->

@endsection
