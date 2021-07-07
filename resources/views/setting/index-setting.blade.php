@extends('layouts.admin')
@section('page') Settings @endsection

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
            <h1 class="h3 mb-0 text-gray-800">Settings</h1>
           
          </div>



          <!-- Content Row -->
          <div class="row">

          <div class="col">

                <!-- INDEX -->

@if(!$settings->isEmpty())
    <div class="table-responsive">

        <table class="table table-sm mt-3 table-hover ">
            <thead>
            <tr>
                <th  scope="col">NAME</th>
                <th  scope="col">GROUP</th>
                <th  scope="col">VALUE</th>
                <th  scope="col"></th>
            </tr>
            </thead>
            <tbody>

            @foreach($settings as $setting)
                    <form method="POST" action="{{route('update-setting',$setting)}}">
                    @csrf
                    @method('PUT')
                    <tr>
                    <td>{{$setting->name}}</td>
                    <td>{{$setting->group}}</td>
                    <td>
                    <textarea name="data" class="form-control form-control-sm m-0 overflow-auto">{{$setting->data}}</textarea>
                    </td>
                    <td ><button class="btn btn-warning btn-block btn-sm"><i class="fas fa-edit"></i> Update</button></td>
                    </tr>
                    </form>
            @endforeach

            </tbody>
        </table>
    </div>
@else

<div class="alert alert-secondary" role="alert">
Nothing has been found!
</div>
@endif

<!-- END INDEX -->
              

              


  

            <form method="POST" action="{{route('update-company-setting',$company_profile)}}">
            @csrf
            @method('PUT')

            
        <div class="form-group row m-0">
            <label for="name" class="col-sm-2 col-form-label">Company Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" value="{{$company_profile->name}}" name="name" placeholder="">
                @error('name')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>

        <div class="form-group row m-0">
            <label for="phone" class="col-sm-2 col-form-label">Company Phone</label>
            <div class="col-sm-10">
                <input type="text" class="form-control form-control-sm @error('phone') is-invalid @enderror" id="phone" value="{{$company_profile->phone}}" name="phone" placeholder="">
                @error('phone')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>

        <div class="form-group row m-0">
            <label for="email" class="col-sm-2 col-form-label">Company E-mail</label>
            <div class="col-sm-10">
                <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" value="{{$company_profile->email}}" name="email" placeholder="">
                @error('email')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>

        <div class="form-group row m-0">
            <label for="address" class="col-sm-2 col-form-label">Company Address</label>
            <div class="col-sm-10">
                <input type="text" class="form-control form-control-sm @error('address') is-invalid @enderror" id="address" value="{{$company_profile->address}}" name="address" placeholder="">
                @error('address')
                <span class="invalid-feedback mb-1" role="alert">
                   {{ $message }}
                </span>
            @enderror
            </div>
        </div>

        <div class="form-group row m-0">
            <label for="terms" class="col-sm-2 col-form-label">Company Terms & Conditions</label>
            <div class="col-sm-10">
                <textarea  class="form-control form-control-sm @error('terms') is-invalid @enderror" id="terms"  rows="10" name="terms"> {{$company_profile->terms}}</textarea>
                @error('terms')
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
      
   

          </div>

            </div>

          

        </div>
        <!-- /.container-fluid -->

@endsection
