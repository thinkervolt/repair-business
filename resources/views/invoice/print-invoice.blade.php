<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>{{ config('app.name', 'Laravel') }} - @yield('page')</title>
  

  <!-- Custom fonts for this template-->
  <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset(' https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i') }}" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

</head>
<body>


        <div class="container-fluid">


  <div class="card  border-0">
  @php $item_count = 0 @endphp

    <div class="card-header">
      Invoice
      <strong>#{{$invoice->id}}</strong> 
      <span class="float-right"> @if(isset($invoice->status)) <p class="font-weight-bold text-uppercase m-0 p-0 text-{{$invoice->status_data->color}}">{{$invoice->status_data->name}}</p> @endif</span>
    </div>

    <div class="card-body">

      <div class="row mb-4">
        <div class="col-sm-6">
          <h6 class="mb-3">From:</h6>
          <div>
            <div>{{$invoice->company_name}}</div>
            <div>{{$invoice->company_phone}}</div>
            <div>{{$invoice->company_email}}</div>
            <div>{{$invoice->company_address}}</div>
          </div>
        </div>

        <div class="col-sm-6">
          <h6 class="mb-3">To:</h6>
          <div>{{$invoice->customer_company}}</div>
          <div>{{$invoice->customer_name}}</div>
          <div>{{$invoice->customer_phone}}</div>
          <div>{{$invoice->customer_email}}</div>
          <div>{{$invoice->customer_address}}</div>
        </div>
      </div>
      </div>

      
      


<div class="table-responsive-sm">
<table class="table table-striped">
<thead>
<tr>
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

      <td class="center col-auto">{{$item_count = $item_count + 1}}</td>
      <td class="left strong">
        {{$item->name}}
      </td>
      <td class="left">
      <p >{{$item->description}}</p>
        <p class="small">{{$item->sub_description}}</p>

      </td>

      <td class="right">
      {{$item->unit_cost}}
      </td>
        <td class="center">
        
        {{$item->quantity}}
        </td>
      <td class="right">$ {{number_format((float)$item->total, 2, '.', ',')}}
      
      </td>
    </tr>
    @endforeach
    
  @endif

  
  @if(!$transactions->isEmpty())
 
    @foreach($transactions as $transaction)
    <tr>

      <td class="center col-auto">{{$item_count = $item_count + 1}}</td>
      <td class="left strong">
        {{$transaction->product->barcode}}
      </td>
      <td class="left">
      <p >{{$transaction->product->name}}</p>

      </td>

      <td class="right">
      {{$transaction->selling_price}}
      </td>
        <td class="center">
        
        {{$transaction->quantity}}
        </td>
      <td class="right">$ {{number_format((float)($transaction->selling_price * $transaction->quantity), 2, '.', ',')}}
      
      </td>
    </tr>
    @endforeach
    
  @endif



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

    </td>
    <td class="text-right">
    <p class="text-muted small text-uppercase m-0 p-0">{{$payment->method}}</p>
    @if(isset($payment->ref))<p class="text-muted small text-uppercase m-0 p-0"> REF: {{$payment->ref}}</p> @endif
    <p class="text-muted small text-uppercase m-0 p-0">{{ date_format($payment->created_at,"M d, Y h:iA")}}</p>
    <p class="font-weight-bold ">$ {{number_format((float)$payment->amount, 2, '.', ',')}}</p>
    </td>

    </tr>
    @endforeach
  
@endif

<tr>
    <td class="text-left">
    <strong>Balance</strong>
    </td>
    <td class="text-right">
    <h3 class="font-weight-bold">$ {{number_format((float)$invoice->balance, 2, '.', ',')}}</h3>
    </td>

  </tr>


</tbody>
</table>

</div>

</div>

</div>


<div class="text-center p-3 my-2">

        @php echo DNS1D::getBarcodeSVG('INV'.$invoice->id, 'C39',3,80,'black' ,false); @endphp
   
</div>

<div class="data-block">

            <p class="small text-justify">{{$terms}}</p>
   
</div>


</div>


</div>








<script>
window.print();
 setTimeout(window.close, 0);
</script>


</body>
</html>