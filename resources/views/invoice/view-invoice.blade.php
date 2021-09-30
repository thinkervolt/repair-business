@extends('layouts.admin')
@section('page') View Invoice @endsection

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

          <div id="invoice-alert" class="alert  alert-dismissible fade d-none">
            <li id="invoice-alert-message"></li>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>



  <div class="card ">
  @php $item_count = 0 @endphp

    <div class="card-header">
      Invoice
      <strong>#{{$invoice->id}}</strong> 
      <span class="float-right"> @if(isset($invoice->status)) <p class="font-weight-bold text-uppercase m-0 p-0 text-{{$invoice->status_data->color}}">{{$invoice->status_data->name}}</p> @endif</span>
    </div>

    <div class="card-body">
      <form method="POST" action="{{route('update-invoice',$invoice)}}">
      @csrf
      @method('PUT')
      <div class="row mb-4">
        <div class="col-sm-6">
          <h6 class="mb-3">From:</h6>
          <div>
            <div><input type="text" class="form-control form-control-sm mb-1" name="company_name" placeholder="Company Name" value="{{$invoice->company_name}}"></div>
            <div><input type="text" class="form-control form-control-sm mb-1" name="company_phone" placeholder="Company Phone" value="{{$invoice->company_phone}}"></div>
            <div><input type="text" class="form-control form-control-sm mb-1" name="company_email" placeholder="Company E-mail" value="{{$invoice->company_email}}"></div>
            <div><input type="text" class="form-control form-control-sm mb-1" name="company_address" placeholder="Company Address" value="{{$invoice->company_address}}"></div>
          </div>
        </div>

        <div class="col-sm-6">
          <h6 class="mb-3">To:</h6>
          <div><input type="text" class="form-control form-control-sm mb-1" name="customer_name" placeholder="Customer Name" value="{{$invoice->customer_name}}"></div>
          <div><input type="text" class="form-control form-control-sm mb-1" name="customer_phone" placeholder="Customer Phone" value="{{$invoice->customer_phone}}"></div>
          <div><input type="text" class="form-control form-control-sm mb-1" name="customer_email" placeholder="Customer E-mail" value="{{$invoice->customer_email}}"></div>
          <div><input type="text" class="form-control form-control-sm mb-1" name="customer_address" placeholder="Customer Address" value="{{$invoice->customer_address}}"></div>
          <div><input type="text" class="form-control form-control-sm mb-1" name="customer_company" placeholder="Customer Company" value="{{$invoice->customer_company}}"></div>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-sm-6">
          <h6 class="mb-3">Tax:</h6>
          <input type="text" class="form-control form-control-sm" name="tax_porcentage" placeholder="Tax Porcentage" value="{{$invoice->tax_porcentage}}">
        </div>
        <div class="col-sm-6">
          <h6 class="mb-3">Status:</h6>
          <select class="form-control form-control-sm @error('status') is-invalid @enderror" name="status">
              <option  @if($invoice->status === null) selected @endif ></option>
                @if(!$invoice_statuses->isEmpty())
                  @foreach($invoice_statuses as $status)
                      <option value="{{$status->id}}" @if($invoice->status == $status->id) selected @endif > {{$status->name}} </option>
                  @endforeach
                @endif
        </select>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-sm">
          <button type="submit" class="btn btn-warning btn-sm btn-block mb-1"><i class="fas fa-edit"></i> Update</button>
          </form>
        </div>
        <div class="col-sm">
        <form method="POST" action="{{route('delete-invoice',$invoice)}}">
        @method('put')
      @csrf
          <button type="submit" class="btn btn-danger btn-sm btn-block mb-1"><i class="fas fa-trash"></i> Delete</button>
          </form>
        </div>
        <div class="col-sm">
          
          <a  href="{{route('print-invoice',[$invoice,'receipt'])}}" class="btn btn-primary btn-sm btn-block mb-1" target="popup" onclick="window.open('{{route('print-invoice',[$invoice,'receipt'])}}','popup','width=600,height=600'); return false;">
          
          <i class="fas fa-receipt"></i> Receipt
          </a>
          
        </div>
        <div class="col-sm">
          <a href="{{route('index-customer',[$task = 'invoice',$invoice])}}" class="btn btn-primary btn-sm btn-block">@if(isset($invoice->customer_id))<i class="fas fa-exchange-alt"></i>@else <i class="fas fa-plus"></i>  @endif  Customer</a>
        </div>
        <div class="col-sm">
          <a href="{{route('index-repair',[$task = 'invoice',$invoice])}}" class="btn btn-primary btn-sm btn-block"><i class="fas fa-plus"></i>  Repair</a>
        </div>
        <div class="col-sm">
          <a href="{{route('inventory-index-product',[$task = 'invoice',$invoice])}}" class="btn btn-primary btn-sm btn-block"><i class="fas fa-plus"></i>  Product</a>
        </div>
        <div class="col-sm">

     
          <a  href="{{route('print-invoice',[$invoice,'print'])}}" class="btn btn-primary btn-sm btn-block mb-1" target="popup" onclick="window.open('{{route('print-invoice',[$invoice,'print'])}}','popup','width=600,height=600'); return false;">
          
            <i class="fas fa-print"></i> Print
            </a>
    
        </div>


      </div>
      <p class="d-block d-sm-inline m-0 small">id: {{$invoice->id}}</p>
    <p class="d-block d-sm-inline m-0 small">created_at: {{$invoice->created_at}}</p>
    <p class="d-block d-sm-inline m-0 small">updated_at: {{$invoice->updated_at}}</p>

      


<div class="table-responsive-sm">
<table class="table table-striped">
<thead>
<tr>
<th></th>
<th class="center">#</th>
<th>Item</th>
<th>Description</th>

<th class="right">Unit Cost</th>
  <th class="center">Qty</th>
<th class="right">Total</th>
</tr>
</thead>
<tbody>
  @if(!$invoice_items->isEmpty())
 
    @foreach($invoice_items as $item)
    <tr>
      <td>
      <form method="POST" action="{{route('delete-item-invoice',$item)}}">
      @method('delete')
      @csrf
      <button class="btn btn-danger btn-block btn-sm mb-1"><i class="fas fa-trash"></i> Delete</button>

      </form>


      <form method="POST" action="{{route('update-item-invoice',$item)}}">
      @csrf
      @method('PUT')
      <button class="btn btn-warning btn-block btn-sm mb-1"><i class="fas fa-edit"></i> Update</button>

      </td>
      <td class="center col-auto">{{$item_count = $item_count + 1}}</td>
      <td class="left strong">
        <input type="text" class="form-control form-control-sm" name="name" placeholder="Item Name" value="{{$item->name}}">
      </td>
      <td class="left">
        <textarea  class="form-control form-control-sm" placeholder="Item Description" name="description">{{$item->description}}</textarea>
        <hr>
        <textarea  class="form-control form-control-sm" placeholder="Item Sub-Description" name="sub_description">{{$item->sub_description}}</textarea>

      </td>

      <td class="right">
      <input type="text" class="form-control form-control-sm" name="unit_cost" placeholder="Unit Cost" value="{{$item->unit_cost}}">
      </td>
        <td class="center">
        
        <input type="text" class="form-control form-control-sm" name="quantity" placeholder="Quantity" value="{{$item->quantity}}">
        </td>
      <td class="right">$ {{number_format((float)$item->total, 2, '.', ',')}}
      
      </td>
      
    </form>
    </tr>
    @endforeach
    
  @endif


  <!-- INVENTORY ITEMS -->



  @if(!$transactions->isEmpty())
  @foreach($transactions as $transaction)
  <tr>
    <td>
    <form method="POST" action="{{route('inventory-cancel-transaction',[$invoice,$transaction,])}}">
    @method('delete')
    @csrf
    <button class="btn btn-danger btn-block btn-sm mb-1"><i class="fas fa-trash"></i> Delete</button>

    </form>

    </td>
    <td class="center col-auto">{{$item_count = $item_count + 1}}</td>
    <td class="left strong">
      <input type="text" class="form-control form-control-sm" disabled value="{{$transaction->product->barcode}}">

    </td>
    <td class="left">
      <input type="text" class="form-control form-control-sm" disabled value="{{$transaction->product->name}}">
  
    </td>

    <td class="right">
    <input type="text" class="form-control form-control-sm" disabled value="{{$transaction->selling_price}}">
    </td>
      <td class="center">
      
        <input type="text" class="form-control form-control-sm" disabled value="{{$transaction->quantity}}">
      </td>
    <td class="right">$ {{number_format((float)($transaction->selling_price * $transaction->quantity ), 2, '.', ',')}}
    
    </td>
    
  </tr>
  @endforeach
  
@endif

<!-- END INVENTORY ITEMS -->

  <!-- NEW ITEM -->


  <tr>
  
      <td>
   
      <form method="POST" action="{{route('create-item-invoice',$invoice)}}">
      @csrf
      <button class="btn btn-primary btn-block btn-sm mb-1"><i class="fas fa-save"></i> Create</button>

      </td>
      <td class="center col-auto">{{$item_count = $item_count + 1}}</td>
      <td class="left strong">
        <input type="text" class="form-control form-control-sm" name="name" placeholder="Item Name" value="">
      </td>
      <td class="left">

        <input type="text" class="form-control form-control-sm" name="description" placeholder="Item Description" value="">
        <hr>
        <input type="text" class="form-control form-control-sm" name="sub_description" placeholder="Item Sub-Description" value="">
      

      </td>

      <td class="right">
      <input type="text" class="form-control form-control-sm" name="unit_cost" placeholder="Unit Cost" value="">
      </td>
        <td class="center">
        
        <input type="text" class="form-control form-control-sm" name="quantity" placeholder="Quantity" value="">
        </td>
      <td class="right">
      
      </td>
      
    </form>
</tr>

  <!-- END NEW ITEM -->


</tbody>
</table>
</div>
<div class="row">
<div class="col-lg-4 col-sm-5">

</div>

<div class="col-lg-4 col-sm-5 ml-auto">
<table class="table table-clear">
<tbody>
<tr>
<td class="text-left">
<strong>Subtotal</strong>
</td>
<td class="text-right">$ {{number_format((float)$invoice->subtotal, 2, '.', ',')}}</td>
</tr>

<tr>
<td class="text-left">


 <strong>TAX ( {{number_format((float)$invoice->tax_porcentage, 2, '.', ',')}}%)</strong>

 

</td>
<td class="text-right">$ {{number_format((float)$invoice->tax, 2, '.', ',')}}</td>
</tr>
<tr>
<td class="text-left">
<strong>Total</strong>
</td>
<td class="text-right">
<strong>$ {{number_format((float)$invoice->total, 2, '.', ',')}}</strong>
</td>
</tr>

@if(!$payments->isEmpty())
 
    @foreach($payments as $payment)
    <tr>

    <td class="text-left">
    <strong>Payment</strong>
    <form method="POST" action="{{route('delete-payment',$payment)}}">

    @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger btn-sm d-block mt-1"><i class="fas fa-trash"></i> Delete</button>
    </form>
    </td>
    <td class="text-right">
    <p class="text-muted small text-uppercase m-0 p-0">{{$payment->method}}</p>
    @if(isset($payment->ref))<p class="text-muted small text-uppercase m-0 p-0"> REF: {{$payment->ref}}</p> @endif
    <p class="text-muted small text-uppercase m-0 p-0">{{ date_format($payment->created_at,"M d, Y h:iA")}}</p>
    <p class="font-weight-bold @if($payment->amount > 0) text-success @endif @if($payment->amount < 0) text-danger @endif">$ {{number_format((float)$payment->amount, 2, '.', ',')}}</p>
    </td>

    </tr>
    @endforeach
  
@endif

<tr>
    <td class="text-left">
    <strong>Balance</strong>
    </td>
    <td class="text-right">
    <h3 class="font-weight-bold @if($invoice->balance < 0) text-success @endif @if($invoice->balance > 0) text-danger @endif ">$ {{number_format((float)$invoice->balance, 2, '.', ',')}}</h3>
    </td>

  </tr>


</tbody>
</table>

</div>

</div>
<hr>

<h6 class="mb-3">Payment</h6>

<!-- FORM -->
<form action="{{route('create-payment',$invoice)}}" method="POST">
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


                         

@if(!$logs->isEmpty())

<hr>

<div class="row mb-2">
    <div class="col-12">
    @foreach($logs as $log)
        <div class="row">
            <div class="col-md">
                <p class="small text-muted p-0 m-0">{{$log->data}}</p>
            </div>
            <div class="col-md-auto">
                <p class="small text-muted p-0 m-0">by {{ $log->user_data->name ?? ''}}</p>
            </div>
            <div class="col-md-auto">
                <p class="small text-muted p-0 m-0">{{ date_format($log->created_at,"M d, Y h:iA")}}</p>
            </div>
        </div>
    @endforeach
    </div>
</div>

    {{ $logs->links() }}



@endif



            


@endsection