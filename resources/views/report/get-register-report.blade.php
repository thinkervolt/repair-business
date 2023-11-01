@extends('layouts.admin')
@section('page') View Register Report @endsection

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

         
            <h1 class="h3 mb-0 text-gray-800">View Register Report</h1>
            <h5 class="mb-0 text-gray-800">Date: {{date('M d, Y',strtotime($payment_data['date']))}} </h5>
            
          
  
           
          </div>


    <!-- INDEX -->

    @if(!empty($payments))
        <h3>PAYMENTS</h3>

          


          <div class="row">
            <div class="col-md text-left"> PAYMENTS: {{$payment_data['count']}}</div>
            <div class="col-md text-center"> TOTAL: $ {{number_format((float)$payment_data['total'], 2, '.', ',')}}</div>
            <div class="col-md text-right">
                <p class="muted font-italic small m-0 p-0">CASH: $ {{number_format((float)$payment_data['total_cash'], 2, '.', ',')}}</p>
                <p class="muted font-italic small m-0 p-0">CARD: $ {{number_format((float)$payment_data['total_card'], 2, '.', ',')}}</p>
                <p class="muted font-italic small m-0 p-0">CHECK: $ {{number_format((float)$payment_data['total_check'], 2, '.', ',')}}</p>
                <p class="muted font-italic small m-0 p-0">OTHER: $ {{number_format((float)$payment_data['total_other'], 2, '.', ',')}}</p>
            </div>
          </div>

    
    <div class="table-responsive">



        <table class="table table-sm mt-3 table-hover ">
            <thead>
            <tr>
                <th  scope="col"><p class="m-0 p-0 small">ID</p></th>
                <th  scope="col"><p class="m-0 p-0 small">INVOICE</p></th>
                <th  scope="col"><p class="m-0 p-0 small">AMOUNT</p></th>
                <th  scope="col"><p class="m-0 p-0 small">METHOD</p></th>
                <th  scope="col"><p class="m-0 p-0 small">REF</p></th>
                <th  scope="col"><p class="m-0 p-0 small">DATE</p></th>
                
            </tr>
            </thead>
            <tbody>

            @foreach($payments as $payment)
                <tr>
                    <td> <p class="m-0 p-0 small">{{$payment->id}}</p></td>
                    <td>
                        <p class="m-0 p-0 small">{{$payment->invoice ?? ''}}</p>
                    </td>
                    <td> <p class="m-0 p-0 small"> $ {{number_format((float)$payment->amount, 2, '.', ',')}} </p></td>
                    <td> <p class="m-0 p-0 small text-uppercase">{{$payment->method}}</p></td>
                    <td> <p class="m-0 p-0 small">{{$payment->ref}}</p></td>
                    <td> <p class="m-0 p-0 small">{{ date_format($payment->created_at,"M d, Y")}}</p></td>
                
                </tr>
            
            @endforeach

            </tbody>
        </table>
    
    </div>
@endif

<!-- END INDEX -->


<div class="row">
    <div class="col">
        <p class="mb-0 pb-0" >TOTAL CASH REGISTER: $ {{number_format((float)$payment_data['cash_register'], 2, '.', ',')}}</p>
        <p>TOTAL CASH INVOICES: $ {{number_format((float)$payment_data['total_cash'], 2, '.', ',')}}</p>
        <hr>
        <p class="font-weight-bold" >NOT-INVOICED CASH TRANSACTIONS: $ {{number_format((float)($payment_data['cash_register'] - $payment_data['total_cash'] ), 2, '.', ',')}}</p>
    </div>
    <div class="col">
        <p class="mb-0 pb-0">TOTAL CARD REGISTER: $ {{number_format((float)$payment_data['card_register'], 2, '.', ',')}}</p>
        <p>TOTAL CARD INVOICES: $ {{number_format((float)$payment_data['total_card'], 2, '.', ',')}}</p>
        <hr>
        <p class="font-weight-bold">NOT-INVOICED CARD TRANSACTIONS: $ {{number_format((float)($payment_data['card_register'] - $payment_data['total_card'] ), 2, '.', ',')}}</p>
    </div>
</div>


<form method="POST" action="{{route('register-report-insert')}}">
    @method('POST')

    @csrf
    <input type="hidden" name="cash" value={{$payment_data['cash_register'] - $payment_data['total_cash']}}>
    <input type="hidden" name="card" value={{$payment_data['card_register'] - $payment_data['total_card']}}>
    <input type="hidden" name="date" value={{$payment_data['date']}}>


<button  class="btn btn-primary" type="submit">Insert Data</button>
</form>


              

 
        </div>
        <!-- /.container-fluid -->

@endsection
