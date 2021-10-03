<script>
    (function($) {
    var _timeoutHandler = 0,
        _inputString = '',
        _onKeypress = function(e) {
            if (_timeoutHandler) {
                clearTimeout(_timeoutHandler);
            }
            _inputString += e.key;

            _timeoutHandler = setTimeout(function () {
                if (_inputString.length <= 3) {
                    _inputString = '';
                    return;
                }
                $(e.target).trigger('altdeviceinput', _inputString);
                _inputString = '';

            }, 80);
        };
    $(document).on({
        keypress: _onKeypress
    });

    $(document).on("altdeviceinput", function (e, barcode_input) {


        /* SYMBOL LS1208 BARCODE SCANNER (Slash Removal)*/ 
            /* barcode =  barcode_input.replace(/\\/g, ''); */

        /* NETUM NT-1228BL BARCODE SCANNER (Enter Removal)*/

            barcode = barcode_input.replace('Enter','');

        // console.log(barcode);


        var postData = "text";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
              type: "post",
              url: "/barcode",
              dataType:"json",
              data: "barcode="+barcode,
              contentType: "application/x-www-form-urlencoded",
              success: function(response, textStatus, jqXHR) {

                if(response.response == 'product-found'){
                    $("#barcode-window-title").text('PRODUCT: '+response.data.barcode);
                    $("#barcode-window-message-a").text(response.data.name);
                    $("#barcode-window-message-b").text('$ '+response.data.selling_price);
                    $("#barcode-window-message-c").text(response.data_response + ' Available');

                    $("#barcode-window-open-button").attr('href','/inventory/product/view/'+response.data.id);
                    $('#barcode-window-open-button').removeClass('d-none');
                    $("#barcode-window-restock-button").attr('href','/inventory/transaction/restock/'+response.data.id);
                    $('#barcode-window-restock-button').removeClass('d-none');
                    $("#barcode-window-quick-sell-button").attr('href','/inventory/transaction/quick-sell/'+response.data.id);
                    $('#barcode-window-quick-sell-button').removeClass('d-none');
                    $("#barcode-window-create-product-button").attr('');
                    $('#barcode-window-create-product-button').addClass('d-none');

                    
                    $('#barcode-window').modal('show');
                }

                if(response.response == 'barcode-not-found'){

                    $("#barcode-window-title").text('BARCODE '+response.data_response+' NOT FOUND');
                    $("#barcode-window-message-a").text('');
                    $("#barcode-window-message-b").text('');
                    $("#barcode-window-message-c").text('');

                    $("#barcode-window-open-button").attr('');
                    $('#barcode-window-open-button').addClass('d-none');
                    $("#barcode-window-restock-button").attr('');
                    $('#barcode-window-restock-button').addClass('d-none');
                    $("#barcode-window-quick-sell-button").attr('');
                    $('#barcode-window-quick-sell-button').addClass('d-none');
                    $("#barcode-window-create-product-button").attr('href','/inventory/products/create-product/'+response.data_response);
                    $('#barcode-window-create-product-button').removeClass('d-none');

                    $('#barcode-window').modal('show');

                }

                if(response.response == 'repair-found'){

                    $("#barcode-window-title").text('REPAIR FOUND');
                    $("#barcode-window-message-a").text('');
                    $("#barcode-window-message-b").text('');
                    $("#barcode-window-message-c").text('');

                    $("#barcode-window-open-button").attr('href','/view-repair/'+response.data.id);
                    $('#barcode-window-open-button').removeClass('d-none');
                    $("#barcode-window-restock-button").attr('');
                    $('#barcode-window-restock-button').addClass('d-none');
                    $("#barcode-window-quick-sell-button").attr('');
                    $('#barcode-window-quick-sell-button').addClass('d-none');
                    $("#barcode-window-create-product-button").attr('');
                    $('#barcode-window-create-product-button').addClass('d-none');

                    $('#barcode-window').modal('show');

                }

                if(response.response == 'invoice-found'){

                    $("#barcode-window-title").text('INVOICE FOUND');
                    $("#barcode-window-message-a").text('');
                    $("#barcode-window-message-b").text('');
                    $("#barcode-window-message-c").text('');

                    $("#barcode-window-open-button").attr('href','/view-invoice/'+response.data.id);
                    $('#barcode-window-open-button').removeClass('d-none');
                    $("#barcode-window-restock-button").attr('');
                    $('#barcode-window-restock-button').addClass('d-none');
                    $("#barcode-window-quick-sell-button").attr('');
                    $('#barcode-window-quick-sell-button').addClass('d-none');
                    $("#barcode-window-create-product-button").attr('');
                    $('#barcode-window-create-product-button').addClass('d-none');

                    $('#barcode-window').modal('show');

                }

               
              },
              error: function(jqXHR, textStatus, errorThrown) {
                  console.log(errorThrown);
              }
          })


    });
})($);

</script>


<div id="barcode-window" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="barcode-window-title"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p id="barcode-window-message-a" class="font-weight-bold text-primary m-0 p-0"></p>
          <p id="barcode-window-message-b" class="font-weight-bold m-0 p-0"></p>
          <p id="barcode-window-message-c" class="font-weight-bold m-0 p-0"></p>
        </div>

        <div class="modal-footer">
          <a id="barcode-window-create-product-button" href="" class="btn btn-success d-none" ><i class="fas fa-plus"></i> Create Product</a>  
          <a id="barcode-window-quick-sell-button" href="" class="btn btn-success d-none" ><i class="fas fa-shopping-cart"></i> Quick Sell</a>  
          <a id="barcode-window-restock-button" href="" class="btn btn-primary d-none" ><i class="fas fa-truck-loading"></i> Restock</a>  
          <a id="barcode-window-open-button" href="" class="btn btn-primary d-none" ><i class="fas fa-binoculars"></i> View</a>  
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
        </div>
        
      </div>
    </div>
  </div>

