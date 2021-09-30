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

            }, 20);
        };
    $(document).on({
        keypress: _onKeypress
    });

    $(document).on("altdeviceinput", function (e, barCode) {

        motoroala_output = barCode.replace(/\\/g, '');

        var postData = "text";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
              type: "post",
              url: "/barcode/invoice",
              dataType:"json",
              data: "barcode="+motoroala_output+"&invoice={{$invoice->id}}",
              contentType: "application/x-www-form-urlencoded",
              success: function(response, textStatus, jqXHR) {
               
                  console.log(response);

                 
                  if(response.response == 'barcode-not-found'){
                    $("#invoice-alert-message").text('Product not found, scan again or check your Inventory');
                    $('#invoice-alert').removeClass('d-none');
                    $('#invoice-alert').addClass('show');
                  }

                  if(response.response == 'product-out-stock'){

                    $("#invoice-alert-message").text('Product not Available, out of Stock');
                    $('#invoice-alert').removeClass('d-none');
                    $('#invoice-alert').addClass('show');
                  }

                  if(response.response == 'new-transaction-created'){
                    document.location.reload(true);
                  }

              },
              error: function(jqXHR, textStatus, errorThrown) {
                  console.log(errorThrown);
              }
          })


    });
})($);

</script>

