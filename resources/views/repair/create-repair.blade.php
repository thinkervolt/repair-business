@extends('layouts.admin')
@section('page') Create Repair @endsection

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
            <h1 class="h3 mb-0 text-gray-800">Create Repair</h1>
           
          </div>



          <!-- Content Row -->
          <div class="row">

          <div class="col">

          

            
            <div class="row justify-content-md-center ">
                 <!-- CUSTOMER -->
                @if(isset($customer))
                <div class="col-lg-4 mb-2">
                    <div class="card shadow py-2">
                        <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">CUSTOMER</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$customer->first_name}} {{$customer->last_name}}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $customer->phone)}}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$customer->email}}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$customer->address}} {{$customer->city}} {{$customer->state}} {{$customer->zip}}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$customer->company}}</div>
                            </div>
                            <div class="col-auto d-none d-md-block">
                            <i class="fas fa-user fa-4x text-gray-300"></i>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                @endif
           
            <!-- END CUSTOMER -->

            
                <div class="col-lg-8 mb-2">
                    <div class="card shadow h-100 py-2">
                        <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">REPAIR</div>

                                    <!-- FORM -->
                                        <form action="{{route('new-repair',$customer)}}" method="POST">
                                            @csrf

                                                <div class="form-group row m-0">
                                                    <label for="target" class="col-sm-2 col-form-label">Target</label>
                                                    <div class="col-sm-10">
                                                    <input type="text" class="form-control form-control-sm @error('target') is-invalid @enderror" id="target" value="{{old('target')}}" name="target" placeholder="">
                                                    @error('target')
                                                        <span class="invalid-feedback mb-1" role="alert">
                                                        {{ $message }}
                                                        </span>
                                                    @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row m-0">
                                                    <label for="data_request" class="col-sm-2 col-form-label">Request</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control form-control-sm @error('data_request') is-invalid @enderror" id="data_request"   value="{{old('data_request')}}"  name="data_request" placeholder="">
                                                        @error('data_request')
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
                            <div class="col-auto d-none d-md-block">
                            <i class="fas fa-wrench fa-4x text-gray-300"></i>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END FORM -->
              

          </div>

            </div>

          

        </div>
        <!-- /.container-fluid -->

@endsection
